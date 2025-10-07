<?php

namespace App\Http\Controllers\Student;

use App\Models\Result;
use App\Models\StudentTermSummary;
use App\Models\FeePayment;
use App\Models\AcademicSession;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Enums\TermEnum;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;

class StudentController extends StudentBaseController
{
    /**
     * Display the student dashboard with fee payment check.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function dashboard()
    {
        $student = $this->getAuthenticatedStudent();
        [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);

        if (!$student || !$currentSession || !$currentTerm) {
            Log::error('Dashboard failed to load', [
                'student_id' => $student->id ?? null,
                'session_id' => $currentSession->id ?? null,
                'term' => $currentTerm->value ?? null
            ]);
            return redirect()->route('student.login')->with('error', 'Unable to load dashboard.');
        }

        // Check fee payment status
        $feeStatus = FeePayment::where('student_id', $student->id)
            ->where('session_id', $currentSession->id)
            ->where('term', $currentTerm->value)
            ->first();

        $hasPaidFees = $feeStatus && $feeStatus->has_paid_fee;

        // Fetch current class
        $currentClass = $student->getCurrentClass($currentSession->id, $currentTerm);

        // Initialize default values for results-related data
        $recentResults = collect();
        $performanceData = ['labels' => [], 'averages' => []];
        $performanceSlope = 0;
        $bestSubject = null;
        $subjectData = ['labels' => [], 'averages' => []];
        $weakSubjects = collect();

        if ($hasPaidFees) {
            try {
                // Fetch recent results, ordered by subject name
                $recentResults = Result::where('student_id', $student->id)
                    ->where('session_id', $currentSession->id)
                    ->where('term', $currentTerm->value)
                    ->with(['subject', 'class'])
                    ->join('subjects', 'results.subject_id', '=', 'subjects.id')
                    ->where('subjects.deactivated', false)
                    ->orderBy('subjects.name')
                    ->limit(5)
                    ->get();

                // Fetch sessions for performance data
                $sessions = AcademicSession::orderBy('year', 'asc')->get();
                $terms = TermEnum::cases();

                // Performance trends (term averages across sessions)
                foreach ($sessions as $session) {
                    foreach ($terms as $term) {
                        $summary = StudentTermSummary::where('student_id', $student->id)
                            ->where('session_id', $session->id)
                            ->where('term', $term->value)
                            ->first();
                        if ($summary && is_numeric($summary->term_average)) {
                            $label = Str::ascii($session->year . ' ' . $term->label());
                            $performanceData['labels'][] = $label;
                            $performanceData['averages'][] = floatval($summary->term_average);
                        }
                    }
                }

                // Log performance data for debugging
                Log::debug('Performance data', [
                    'student_id' => $student->id,
                    'performance_data' => $performanceData
                ]);

                // Calculate performance slope
                $performanceSlope = $this->calculatePerformanceSlope($performanceData['averages']);

                // Best-performing subject in current session
                $bestSubject = Result::where('student_id', $student->id)
                    ->where('session_id', $currentSession->id)
                    ->join('subjects', 'results.subject_id', '=', 'subjects.id')
                    ->where('subjects.deactivated', false)
                    ->groupBy('subject_id', 'subjects.name')
                    ->selectRaw('subjects.name, AVG(total) as average')
                    ->orderBy('average', 'desc')
                    ->first();

                // Subject performance for current term, filtered by current class
                $currentEnrollment = $student->getCurrentEnrollment();
                if ($currentEnrollment) {
                    $classId = $currentEnrollment->class_id;
                    $results = Result::where('student_id', $student->id)
                        ->where('session_id', $currentSession->id)
                        ->where('term', $currentTerm->value)
                        ->where('results.class_id', $classId)
                        ->with('subject')
                        ->join('class_subject', 'results.subject_id', '=', 'class_subject.subject_id')
                        ->where('class_subject.class_id', $classId)
                        ->join('subjects', 'results.subject_id', '=', 'subjects.id')
                        ->where('subjects.deactivated', false)
                        ->select('results.*')
                        ->get()
                        ->groupBy('subject_id');

                    Log::info('Subject performance query results', [
                        'student_id' => $student->id,
                        'session_id' => $currentSession->id,
                        'term' => $currentTerm->value,
                        'class_id' => $classId,
                        'result_count' => $results->count()
                    ]);

                    foreach ($results as $subjectId => $subjectResults) {
                        $subject = $subjectResults->first()->subject;
                        if ($subject && !$subject->deactivated) {
                            $avg = floatval($subjectResults->avg('total'));
                            $subjectData['labels'][] = Str::ascii($subject->name);
                            $subjectData['averages'][] = $avg;
                        }
                    }
                }

                // Log subject data for debugging
                Log::debug('Subject data', [
                    'student_id' => $student->id,
                    'subject_data' => $subjectData
                ]);

                // Areas needing improvement (subjects with average < 60%)
                $weakSubjects = Result::where('student_id', $student->id)
                    ->where('session_id', $currentSession->id)
                    ->join('subjects', 'results.subject_id', '=', 'subjects.id')
                    ->where('subjects.deactivated', false)
                    ->groupBy('subject_id', 'subjects.name')
                    ->selectRaw('subjects.name, AVG(total) as average')
                    ->having('average', '<', 60)
                    ->get();
            } catch (\Exception $e) {
                Log::error('Error generating dashboard data', [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $performanceData = ['labels' => [], 'averages' => []];
                $subjectData = ['labels' => [], 'averages' => []];
            }
        }

        return view('student.dashboard', compact(
            'student',
            'currentSession',
            'currentTerm',
            'currentClass',
            'recentResults',
            'feeStatus',
            'hasPaidFees',
            'performanceData',
            'performanceSlope',
            'bestSubject',
            'subjectData',
            'weakSubjects'
        ));
    }

    /**
     * Calculate the performance slope based on term averages.
     *
     * @param array $averages
     * @return float
     */
    private function calculatePerformanceSlope($averages)
    {
        if (count($averages) < 2) {
            return 0;
        }
        $n = count($averages);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumXX = 0;
        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumY += $averages[$i];
            $sumXY += $i * $averages[$i];
            $sumXX += $i * $i;
        }
        $denominator = ($n * $sumXX - $sumX * $sumX);
        return $denominator != 0 ? ($n * $sumXY - $sumX * $sumY) / $denominator : 0;
    }

    /**
     * Display all results for the student, with fee payment check.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function viewResults(Request $request)
    {
        $student = $this->getAuthenticatedStudent();
        [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);

        if (!$student || !$currentSession || !$currentTerm) {
            return redirect()->route('student.dashboard')->with('error', 'Unable to load results.');
        }

        // Check fee payment status
        $feeStatus = FeePayment::where('student_id', $student->id)
            ->where('session_id', $currentSession->id)
            ->where('term', $currentTerm->value)
            ->first();

        if (!$feeStatus || !$feeStatus->has_paid_fee) {
            Log::warning('Unauthorized attempt to view results due to unpaid fees', [
                'student_id' => $student->id,
                'session_id' => $currentSession->id,
                'term' => $currentTerm->value
            ]);
            return redirect()->route('student.dashboard')->with('error', 'Please pay your fees to view results.');
        }

        $sessionId = $request->input('session_id', $currentSession->id);
        $term = $request->input('term', $currentTerm->value);

        $results = Result::where('student_id', $student->id)
            ->where('session_id', $sessionId)
            ->where('term', $term)
            ->with(['subject', 'class'])
            ->join('subjects', 'results.subject_id', '=', 'subjects.id')
            ->orderBy('subjects.name')
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
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
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
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
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
     * Update student profile picture.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfilePicture(Request $request)
    {
        $student = $this->getAuthenticatedStudent();

        if (!$student) {
            Log::warning('Unauthorized attempt to update profile picture', [
                'user_id' => Auth::id() ?? 'unknown'
            ]);
            return redirect()->route('student.dashboard')->with('error', 'Unable to update profile picture.');
        }

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png|max:2048', // 2MB max
        ]);

        try {
            // Delete existing profile picture if it exists
            if ($student->profile_pic && Storage::disk('public')->exists('profiles/' . $student->profile_pic)) {
                Storage::disk('public')->delete('profiles/' . $student->profile_pic);
            }

            // Store new profile picture
            $file = $request->file('profile_picture');
            $filename = 'student_' . $student->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profiles', $filename, 'public');

            // Update student record
            $student->update(['profile_pic' => $filename]);

            Log::info('Profile picture updated successfully', [
                'student_id' => $student->id,
                'filename' => $filename
            ]);

            return redirect()->route('student.profile')->with('success', 'Profile picture updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update profile picture', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('student.profile')->with('error', 'Failed to update profile picture. Please try again.');
        }
    }

    /**
     * Delete student profile picture.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteProfilePicture()
    {
        $student = $this->getAuthenticatedStudent();

        if (!$student) {
            Log::warning('Unauthorized attempt to delete profile picture', [
                'user_id' => Auth::id() ?? 'unknown'
            ]);
            return redirect()->route('student.dashboard')->with('error', 'Unable to delete profile picture.');
        }

        try {
            if ($student->profile_pic && Storage::disk('public')->exists('profiles/' . $student->profile_pic)) {
                Storage::disk('public')->delete('profiles/' . $student->profile_pic);
                $student->update(['profile_pic' => null]);

                Log::info('Profile picture deleted successfully', [
                    'student_id' => $student->id
                ]);

                return redirect()->route('student.profile')->with('success', 'Profile picture deleted successfully.');
            }

            return redirect()->route('student.profile')->with('info', 'No profile picture to delete.');
        } catch (\Exception $e) {
            Log::error('Failed to delete profile picture', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('student.profile')->with('error', 'Failed to delete profile picture. Please try again.');
        }
    }

    /**
     * Download student results as PDF, with fee payment check.
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

        // Check fee payment status
        $feeStatus = FeePayment::where('student_id', $student->id)
            ->where('session_id', $currentSession->id)
            ->where('term', $currentTerm->value)
            ->first();

        if (!$feeStatus || !$feeStatus->has_paid_fee) {
            Log::warning('Unauthorized attempt to download results due to unpaid fees', [
                'student_id' => $student->id,
                'session_id' => $currentSession->id,
                'term' => $currentTerm->value
            ]);
            return redirect()->route('student.dashboard')->with('error', 'Please pay your fees to download results.');
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
            ->join('subjects', 'results.subject_id', '=', 'subjects.id')
            ->orderBy('subjects.name')
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

        // Generate the download URL
        $downloadUrl = route('student.results.download', [
            'session_id' => $session->id,
            'term' => $term->value
        ]);

        // Generate QR code
        $renderer = new ImageRenderer(
            new RendererStyle(120, 5),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeImage = 'data:image/png;base64,' . base64_encode($writer->writeString($downloadUrl));

        // Prepare data for the PDF view
        $data = [
            'student' => $student,
            'results' => $results,
            'termSummary' => $termSummary,
            'session' => $session,
            'term' => $term,
            'currentClass' => $currentClass,
            'date' => now()->format('F j, Y'),
            'school_logo' => public_path('images/school_logo.png'),
            'signature' => public_path('images/signature.png'),
            'downloadUrl' => $downloadUrl,
            'qrCodeImage' => $qrCodeImage,
        ];

        // Log for debugging
        Log::info('Generating PDF with driver: dompdf', [
            'student_id' => $student->id,
            'session_id' => $session->id,
            'term' => $term->value,
        ]);

        // Sanitize filename
        $filename = Str::title(Str::slug($student->first_name, '_')) . '_' .
            Str::title(Str::slug($student->last_name, '_')) . '_' .
            Str::title(Str::slug($term->label(), '_')) . '_' .
            Str::slug(str_replace('/', '_', $session->year), '_') . '_Result.pdf';

        // Generate and download the PDF
        $pdf = Pdf::loadView('student.pdf.student_results', $data)
            ->setPaper('a4')
            ->setOption('margin-top', 0)
            ->setOption('margin-right', 9)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 9)
            ->setOption('enable-local-file-access', true);

        return $pdf->download($filename);
    }
}
