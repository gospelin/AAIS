<?php

namespace App\Http\Controllers\Student;

use App\Models\Result;
use App\Models\StudentTermSummary;
use App\Models\FeePayment;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Enums\TermEnum;

class StudentController extends StudentBaseController
{
    /**
     * Display the student dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $student = $this->getAuthenticatedStudent();
        [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);

        if (!$student || !$currentSession || !$currentTerm) {
            return redirect()->route('student.login')->with('error', 'Unable to load dashboard.');
        }

        // Fetch current class
        $currentClass = $student->getCurrentClass($currentSession->id, $currentTerm);

        // Fetch recent results
        $recentResults = Result::where('student_id', $student->id)
            ->where('session_id', $currentSession->id)
            ->where('term', $currentTerm->value)
            ->with(['subject', 'class'])
            ->limit(5)
            ->get();

        // Fetch fee status
        $feeStatus = FeePayment::where('student_id', $student->id)
            ->where('session_id', $currentSession->id)
            ->where('term', $currentTerm->value)
            ->first();

        $sessions = AcademicSession::orderBy('year', 'desc')->get();
        $terms = TermEnum::cases();

        return view('student.dashboard', compact(
            'student',
            'currentSession',
            'currentTerm',
            'currentClass',
            'recentResults',
            'feeStatus',
            'sessions',
            'terms'
        ));
    }

    /**
     * Display all results for the student.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function viewResults(Request $request)
    {
        $student = $this->getAuthenticatedStudent();
        [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);

        if (!$student || !$currentSession || !$currentTerm) {
            return redirect()->route('student.dashboard')->with('error', 'Unable to load results.');
        }

        $sessionId = $request->input('session_id', $currentSession->id);
        $term = $request->input('term', $currentTerm->value);

        $results = Result::where('student_id', $student->id)
            ->where('session_id', $sessionId)
            ->where('term', $term)
            ->with(['subject', 'class'])
            ->get();

        $termSummary = StudentTermSummary::where('student_id', $student->id)
            ->where('session_id', $sessionId)
            ->where('term', $term)
            ->first();

        $sessions = AcademicSession::orderBy('year', 'desc')->get();
        $terms = TermEnum::cases();

        return view('student.results', compact(
            'student',
            'results',
            'termSummary',
            'sessions',
            'terms',
            'currentSession',
            'currentTerm'
        ));
    }

    /**
     * Display fee payment status.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function viewFeeStatus(Request $request)
    {
        $student = $this->getAuthenticatedStudent();
        [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);

        if (!$student || !$currentSession || !$currentTerm) {
            return redirect()->route('student.dashboard')->with('error', 'Unable to load fee status.');
        }

        $sessionId = $request->input('session_id', $currentSession->id);
        $term = $request->input('term', $currentTerm->value);

        $feePayments = FeePayment::where('student_id', $student->id)
            ->where('session_id', $sessionId)
            ->where('term', $term)
            ->get();

        $sessions = AcademicSession::orderBy('year', 'desc')->get();
        $terms = TermEnum::cases();

        return view('student.fee_status', compact(
            'student',
            'feePayments',
            'sessions',
            'terms',
            'currentSession',
            'currentTerm'
        ));
    }

    /**
     * Display student profile.
     *
     * @return \Illuminate\View\View
     */
    public function viewProfile()
    {
        $student = $this->getAuthenticatedStudent();
        [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);

        if (!$student || !$currentSession || !$currentTerm) {
            return redirect()->route('student.dashboard')->with('error', 'Unable to load profile.');
        }

        $classHistory = $student->classHistory()
            ->with(['class', 'session'])
            ->orderBy('join_date', 'desc')
            ->get();

        return view('student.profile', compact(
            'student',
            'currentSession',
            'currentTerm',
            'classHistory'
        ));
    }

    /**
     * Display print-friendly results view.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function printResults(Request $request)
    {
        $user = Auth::user();

        // Ensure the user is authenticated and has the student role
        if (!$user || !$user->hasRole('student')) {
            Log::warning('Unauthorized attempt to print results by user ID: ' . ($user->id ?? 'unknown'));
            return redirect()->route('student.dashboard')->withErrors(['error' => 'Unauthorized access.']);
        }

        // Fetch the student record
        $student = $user->student;
        if (!$student) {
            Log::warning('No student record found for user ID: ' . $user->id);
            return redirect()->route('student.dashboard')->withErrors(['error' => 'Student profile not found.']);
        }

        // Get the current academic session and term
        [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
        if (!$currentSession || !$currentTerm) {
            Log::warning('No current academic session or term found for student ID: ' . $student->id);
            return redirect()->route('student.dashboard')->withErrors(['error' => 'No active academic session or term.']);
        }

        // Get session and term from request, default to current session and term
        $sessionId = $request->input('session_id', $currentSession->id);
        $term = $request->input('term', $currentTerm->value);

        // Validate session and term
        $session = AcademicSession::find($sessionId);
        if (!$session) {
            Log::warning('Invalid session ID provided: ' . $sessionId);
            return redirect()->route('student.dashboard')->withErrors(['error' => 'Invalid academic session.']);
        }

        try {
            $term = TermEnum::from($term);
        } catch (\ValueError $e) {
            Log::warning('Invalid term provided: ' . $term);
            return redirect()->route('student.dashboard')->withErrors(['error' => 'Invalid term selected.']);
        }

        // Fetch results for the selected session and term
        $results = $student->results()
            ->where('session_id', $session->id)
            ->where('term', $term->value)
            ->with(['subject', 'class'])
            ->get();

        if ($results->isEmpty()) {
            Log::info('No results found for student ID: ' . $student->id . ' in session: ' . $session->year . ', term: ' . $term->value);
            return redirect()->route('student.dashboard')->withErrors(['error' => 'No results available for the selected session and term.']);
        }

        // Fetch term summary
        $termSummary = StudentTermSummary::where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->where('term', $term->value)
            ->first();

        // Get the current class for the selected session and term
        $currentClass = $student->getCurrentClass($session->id, $term->value);

        // Prepare data for the print view
        $data = [
            'student' => $student,
            'results' => $results,
            'termSummary' => $termSummary,
            'session' => $session,
            'term' => $term,
            'currentClass' => $currentClass,
            'date' => now()->format('F j, Y'),
        ];

        // Log for debugging
        Log::info('Rendering print view for student ID: ' . $student->id, [
            'session_id' => $session->id,
            'term' => $term->value,
        ]);

        // Render print view
        return view('student.pdf.student_results_print', $data);
    }


    /**
     * Download student results as PDF.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function downloadResults(Request $request)
    {
        $user = Auth::user();

        // Ensure the user is authenticated and has the student role
        if (!$user || !$user->hasRole('student')) {
            Log::warning('Unauthorized attempt to download results by user ID: ' . ($user->id ?? 'unknown'));
            return redirect()->route('student.dashboard')->withErrors(['error' => 'Unauthorized access.']);
        }

        // Fetch the student record
        $student = $user->student;
        if (!$student) {
            Log::warning('No student record found for user ID: ' . $user->id);
            return redirect()->route('student.dashboard')->withErrors(['error' => 'Student profile not found.']);
        }

        // Get the current academic session and term
        [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
        if (!$currentSession || !$currentTerm) {
            Log::warning('No current academic session or term found for student ID: ' . $student->id);
            return redirect()->route('student.dashboard')->withErrors(['error' => 'No active academic session or term.']);
        }

        // Get session and term from request, default to current session and term
        $sessionId = $request->input('session_id', $currentSession->id);
        $term = $request->input('term', $currentTerm->value);

        // Validate session and term
        $session = AcademicSession::find($sessionId);
        if (!$session) {
            Log::warning('Invalid session ID provided: ' . $sessionId);
            return redirect()->route('student.dashboard')->withErrors(['error' => 'Invalid academic session.']);
        }

        try {
            $term = TermEnum::from($term);
        } catch (\ValueError $e) {
            Log::warning('Invalid term provided: ' . $term);
            return redirect()->route('student.dashboard')->withErrors(['error' => 'Invalid term selected.']);
        }

        // Fetch results for the selected session and term
        $results = $student->results()
            ->where('session_id', $session->id)
            ->where('term', $term->value)
            ->with(['subject', 'class'])
            ->get();

        if ($results->isEmpty()) {
            Log::info('No results found for student ID: ' . $student->id . ' in session: ' . $session->year . ', term: ' . $term->value);
            return redirect()->route('student.dashboard')->withErrors(['error' => 'No results available for the selected session and term.']);
        }

        // Fetch term summary
        $termSummary = StudentTermSummary::where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->where('term', $term->value)
            ->first();

        // Get the current class for the selected session and term
        $currentClass = $student->getCurrentClass($session->id, $term->value);

        // Prepare data for the PDF view
        $data = [
            'student' => $student,
            'results' => $results,
            'termSummary' => $termSummary,
            'session' => $session,
            'term' => $term,
            'currentClass' => $currentClass,
            'date' => now()->format('F j, Y'),
        ];

        // Generate and download the PDF
        // return Pdf::view('student.pdf.student_results', $data)
        //     ->format('A4')
        //     ->name('results_' . $student->reg_no . '_' . str_replace('/', '-', $session->year) . '_' . $term->value . '.pdf')
        //     ->download();

        return Pdf::view('student.pdf.student_results', $data)
            ->format('a4')
            ->margins(9, 9, 9, 9)
            ->download('results_' . $student->reg_no . '_' . str_replace('/', '-', $session->year) . '_' . $term->value . '.pdf');
    }
}

