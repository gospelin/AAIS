<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\User;
use App\Models\StudentClassHistory;
use App\Models\FeePayment;
use App\Models\UserSessionPreference;
use App\Models\Classes;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Enums\TermEnum;
use Symfony\Component\HttpFoundation\Response;

class AdminStudentController extends AdminBaseController
{
    public function addStudent(Request $request)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        if (!$currentSession || !$currentTerm) {
            return redirect()->route('admin.manage_academic_sessions')->with('error', 'No current session or term set. Please set one first.');
        }

        $classes = Classes::orderBy('hierarchy')->get();

        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'first_name' => 'required|string|max:50',
                    'middle_name' => 'nullable|string|max:50',
                    'last_name' => 'required|string|max:50',
                    'gender' => 'required|in:male,female',
                    'date_of_birth' => 'nullable|date',
                    'parent_name' => 'nullable|string|max:70',
                    'parent_phone_number' => 'nullable|string|min:10|max:15',
                    'address' => 'nullable|string|max:255',
                    'parent_occupation' => 'nullable|string|max:100',
                    'state_of_origin' => 'nullable|string|max:50',
                    'local_government_area' => 'nullable|string|max:50',
                    'religion' => 'nullable|string|max:50',
                    'class_id' => 'required|exists:classes,id',
                    'start_term' => 'required|in:First,Second,Third',
                ]);

                $student = Student::create([
                    'reg_no' => Student::generateRegNo($currentSession->year),
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'],
                    'last_name' => $validated['last_name'],
                    'gender' => $validated['gender'],
                    'date_of_birth' => $validated['date_of_birth'],
                    'parent_name' => $validated['parent_name'],
                    'parent_phone_number' => $validated['parent_phone_number'],
                    'address' => $validated['address'],
                    'parent_occupation' => $validated['parent_occupation'],
                    'state_of_origin' => $validated['state_of_origin'],
                    'local_government_area' => $validated['local_government_area'],
                    'religion' => $validated['religion'],
                    'approved' => false,
                ]);

                $user = User::create([
                    'username' => $student->reg_no,
                    'password' => Hash::make($student->reg_no),
                    'role' => 'student',
                    'active' => true,
                ]);

                $student->update(['user_id' => $user->id]);
                $user->assignRole('student');

                StudentClassHistory::create([
                    'student_id' => $student->id,
                    'session_id' => $currentSession->id,
                    'class_id' => $validated['class_id'],
                    'start_term' => $validated['start_term'],
                    'join_date' => Carbon::now('Africa/Lagos'),
                    'is_active' => true,
                ]);

                $this->logActivity("Student registered: {$student->reg_no}", [
                    'student_id' => $student->id,
                    'reg_no' => $student->reg_no
                ]);

                return redirect()->route('admin.add_student')->with('success', "Student registered successfully. Registration Number: {$student->reg_no}.");

            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            } catch (QueryException $e) {
                Log::error("Integrity error registering student: " . $e->getMessage());
                return back()->with('error', 'Registration failed: Duplicate data.');
            } catch (\Exception $e) {
                Log::error("Database error registering student: " . $e->getMessage());
                return back()->with('error', 'Database error occurred. Please try again.');
            }
        }

        $termChoices = collect(TermEnum::cases())->map(function ($term) {
            return (object) ['value' => $term->value, 'label' => $term->value];
        });

        $classChoices = $classes->map(function ($class) {
            return (object) ['id' => $class->id, 'name' => $class->name];
        });

        return view('admin.students.add_student', compact('classChoices', 'termChoices', 'currentTerm'));
    }

    public function index(Request $request, string $action = 'view_students')
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        if (!$currentSession || !$currentTerm) {
            return redirect()->route('admin.manage_academic_sessions')->with('error', 'No current session or term set.');
        }

        $page = $request->get('page', 1);
        $perPage = 10;
        $enrollmentStatus = $request->get('enrollment_status', 'active');
        $feeStatus = $request->get('fee_status', '');
        $approvalStatus = $request->get('approval_status', '');
        $term = $request->get('term', $currentTerm->value);

        $studentsQuery = $this->getStudentsQuery($currentSession, $term);
        $studentsQuery = $this->applyFiltersToStudentsQuery($studentsQuery, $enrollmentStatus, $feeStatus, $approvalStatus, $currentSession, $currentTerm, $term);

        $studentsQuery = $studentsQuery->orderBy('classes.hierarchy')->orderBy('students.first_name');
        $paginatedStudents = $studentsQuery->paginate($perPage, ['*'], 'page', $page);

        $studentsClasses = $this->groupStudentsByClass($paginatedStudents->items());

        $classes = Classes::orderBy('hierarchy')->get();
        $sessions = AcademicSession::orderBy('year', 'desc')->get();

        if ($request->ajax()) {
            return view('admin.students.pagination', compact(
                'studentsClasses',
                'paginatedStudents',
                'action',
                'currentSession',
                'currentTerm',
                'enrollmentStatus',
                'feeStatus',
                'approvalStatus',
                'classes',
                'sessions'
            ));
        }

        return view('admin.students.view_students', compact(
            'studentsClasses',
            'paginatedStudents',
            'currentSession',
            'currentTerm',
            'action',
            'enrollmentStatus',
            'feeStatus',
            'approvalStatus',
            'classes',
            'sessions'
        ));
    }

    public function edit(Request $request, Student $student)
    {
        $classes = Classes::orderBy('hierarchy')->get();
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'first_name' => 'required|string|max:50',
                    'middle_name' => 'nullable|string|max:50',
                    'last_name' => 'required|string|max:50',
                    'gender' => 'required|in:male,female',
                    'date_of_birth' => 'nullable|date',
                    'parent_name' => 'nullable|string|max:70',
                    'parent_phone_number' => 'nullable|string|min:10|max:15',
                    'address' => 'nullable|string|max:255',
                    'parent_occupation' => 'nullable|string|max:100',
                    'state_of_origin' => 'nullable|string|max:50',
                    'local_government_area' => 'nullable|string|max:50',
                    'religion' => 'nullable|string|max:50',
                    'class_id' => 'required|exists:classes,id',
                    'start_term' => 'required|in:First,Second,Third',
                ]);

                $student->update([
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'],
                    'last_name' => $validated['last_name'],
                    'gender' => $validated['gender'],
                    'date_of_birth' => $validated['date_of_birth'],
                    'parent_name' => $validated['parent_name'],
                    'parent_phone_number' => $validated['parent_phone_number'],
                    'address' => $validated['address'],
                    'parent_occupation' => $validated['parent_occupation'],
                    'state_of_origin' => $validated['state_of_origin'],
                    'local_government_area' => $validated['local_government_area'],
                    'religion' => $validated['religion'],
                ]);

                $currentClassHistory = StudentClassHistory::where('student_id', $student->id)
                    ->where('session_id', $currentSession->id)
                    ->where('is_active', true)
                    ->whereNull('leave_date')
                    ->first();

                if ($currentClassHistory && $currentClassHistory->class_id != $validated['class_id']) {
                    $currentClassHistory->update([
                        'is_active' => false,
                        'leave_date' => Carbon::now('Africa/Lagos'),
                        'end_term' => $currentTerm->value,
                    ]);

                    StudentClassHistory::create([
                        'student_id' => $student->id,
                        'session_id' => $currentSession->id,
                        'class_id' => $validated['class_id'],
                        'start_term' => $validated['start_term'],
                        'join_date' => Carbon::now('Africa/Lagos'),
                        'is_active' => true,
                    ]);
                } elseif (!$currentClassHistory) {
                    StudentClassHistory::create([
                        'student_id' => $student->id,
                        'session_id' => $currentSession->id,
                        'class_id' => $validated['class_id'],
                        'start_term' => $validated['start_term'],
                        'join_date' => Carbon::now('Africa/Lagos'),
                        'is_active' => true,
                    ]);
                }

                $this->logActivity("Updated student: {$student->reg_no}", [
                    'student_id' => $student->id
                ]);

                return back()->with('success', 'Student updated successfully!');

            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                Log::error("Database error updating student {$student->id}: " . $e->getMessage());
                return back()->with('error', 'Database error occurred.');
            }
        }

        $termChoices = collect(TermEnum::cases())->map(function ($term) {
            return (object) ['value' => $term->value, 'label' => $term->value];
        });

        $classChoices = $classes->map(function ($class) {
            return (object) ['id' => $class->id, 'name' => $class->name];
        });

        return view('admin.students.edit_student', compact('student', 'classChoices', 'termChoices', 'currentTerm'));
    }

    public function destroy(Student $student)
    {
        try {
            $regNo = $student->reg_no;
            $user = User::find($student->user_id);
            $user?->delete();
            $student->delete();

            $this->logActivity("Deleted student: {$regNo}", ['student_id' => $student->id]);

            return back()->with('success', 'Student deleted successfully!');

        } catch (\Exception $e) {
            Log::error("Database error deleting student {$student->id}: " . $e->getMessage());
            return back()->with('error', 'Database error occurred.');
        }
    }

    public function studentReenroll(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        try {
            $validated = $request->validate([
                'class_id' => 'required|exists:classes,id',
                'start_term' => 'required|in:First,Second,Third',
            ]);

            $currentClassHistory = StudentClassHistory::where('student_id', $studentId)
                ->where('session_id', $currentSession->id)
                ->where('is_active', true)
                ->whereNull('leave_date')
                ->first();

            if ($currentClassHistory) {
                return back()->with('error', 'Student is already enrolled in the current session.');
            }

            StudentClassHistory::create([
                'student_id' => $student->id,
                'session_id' => $currentSession->id,
                'class_id' => $validated['class_id'],
                'start_term' => $validated['start_term'],
                'join_date' => Carbon::now('Africa/Lagos'),
                'is_active' => true,
            ]);

            $this->logActivity("Re-enrolled student: {$student->reg_no}", [
                'student_id' => $student->id,
                'class_id' => $validated['class_id']
            ]);

            return back()->with('success', 'Student re-enrolled successfully!');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error("Database error re-enrolling student {$studentId}: " . $e->getMessage());
            return back()->with('error', 'Database error occurred.');
        }
    }

    public function approveStudent(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        try {
            $student->update(['approved' => true]);

            $this->logActivity("Approved student: {$student->reg_no}", ['student_id' => $studentId]);

            return back()->with('success', 'Student approved successfully!');

        } catch (\Exception $e) {
            Log::error("Database error approving student {$studentId}: " . $e->getMessage());
            return back()->with('error', 'Database error occurred.');
        }
    }

    public function toggleFeeStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'session_id' => 'required|exists:sessions,id',
                'term' => 'required|in:First,Second,Third',
            ]);

            $feePayment = FeePayment::where('student_id', $validated['student_id'])
                ->where('session_id', $validated['session_id'])
                ->where('term', $validated['term'])
                ->first();

            if ($feePayment) {
                $feePayment->update(['has_paid_fee' => !$feePayment->has_paid_fee]);
            } else {
                FeePayment::create([
                    'student_id' => $validated['student_id'],
                    'session_id' => $validated['session_id'],
                    'term' => $validated['term'],
                    'has_paid_fee' => true,
                ]);
            }

            $this->logActivity("Toggled fee status for student {$validated['student_id']}", [
                'student_id' => $validated['student_id'],
                'term' => $validated['term']
            ]);

            return response()->json(['success' => true, 'message' => 'Fee status updated successfully']);

        } catch (\Exception $e) {
            Log::error("Error toggling fee status: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating fee status'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function toggleApprovalStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
            ]);

            $student = Student::findOrFail($validated['student_id']);
            $student->update(['approved' => !$student->approved]);

            $this->logActivity("Toggled approval status for student {$student->reg_no}", [
                'student_id' => $student->id,
                'approved' => $student->approved
            ]);

            return response()->json(['success' => true, 'message' => 'Approval status updated successfully']);

        } catch (\Exception $e) {
            Log::error("Error toggling approval status: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating approval status'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getStats(Request $request)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];

        $totalStudents = Student::whereHas('classHistory', function ($query) use ($currentSession) {
            $query->where('session_id', $currentSession->id)
                  ->where('is_active', true)
                  ->whereNull('leave_date');
        })->count();

        $totalClasses = Classes::count();

        return response()->json([
            'totalStudents' => $totalStudents,
            'totalClasses' => $totalClasses,
        ]);
    }

    public function searchStudents(Request $request, string $action)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        $query = $request->input('query', '');
        $studentsQuery = $this->getStudentsQuery($currentSession, $currentTerm->value);

        if ($query) {
            $studentsQuery->where(function ($q) use ($query) {
                $q->whereRaw('LOWER(students.first_name) LIKE ?', ['%' . strtolower($query) . '%'])
                  ->orWhereRaw('LOWER(students.last_name) LIKE ?', ['%' . strtolower($query) . '%'])
                  ->orWhere('students.reg_no', 'LIKE', '%' . $query . '%');
            });
        }

        $students = $studentsQuery->orderBy('classes.hierarchy')->orderBy('students.first_name')->get();
        $studentsClasses = $this->groupStudentsByClass($students);

        return view('admin.students.search_results', compact('studentsClasses', 'action', 'currentSession', 'currentTerm'));
    }

    public function printStudentMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_ids' => 'required|array',
                'student_ids.*' => 'exists:students,id',
                'message' => 'required|string|max:1000',
            ]);

            $students = Student::whereIn('id', $validated['student_ids'])->get();
            $message = $validated['message'];

            $this->logActivity("Printed message for students", [
                'student_ids' => $validated['student_ids'],
                'message_length' => strlen($message)
            ]);

            return view('admin.students.print_message', compact('students', 'message'));

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error("Error printing student message: " . $e->getMessage());
            return back()->with('error', 'Error generating print message.');
        }
    }
}
