<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\User;
use App\Models\StudentClassHistory;
use App\Models\FeePayment;
use App\Models\Classes;
use App\Models\AcademicSession;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Enums\TermEnum;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

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
                    'date_registered' => Carbon::now('Africa/Lagos'),
                ]);

                $student->update(['reg_no' => Student::generateRegNo($currentSession->year)]);

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
                    'reg_no' => $student->reg_no,
                    'class_id' => $validated['class_id'],
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
        try {
            [$currentSession, $defaultTerm] = $this->getCurrentSessionAndTerm(true);
            if (!$currentSession || !$defaultTerm) {
                return redirect()->route('admin.manage_academic_sessions')->with('error', 'No current session or term set. Please set one first.');
            }

            $termInput = $request->input('term', $defaultTerm->value);
            $currentTerm = TermEnum::tryFrom($termInput) ?? $defaultTerm;

            $enrollmentStatus = $request->input('enrollment_status', 'active');
            $feeStatus = $request->input('fee_status', '');
            $approvalStatus = $request->input('approval_status', '');

            $query = Student::query()->with([
                'classHistory' => function ($q) use ($currentSession, $currentTerm) {
                    $q->where('session_id', $currentSession->id);
                },
                'classHistory.class',
                'feePayments'
            ]);

            if ($enrollmentStatus === 'active') {
                $query->whereHas('classHistory', function ($q) use ($currentSession, $currentTerm) {
                    $q->where('session_id', $currentSession->id)
                        ->where('is_active', true)
                        ->whereNull('leave_date')
                        ->where('start_term', '<=', $currentTerm->value)
                        ->where(function ($q) use ($currentTerm) {
                            $q->whereNull('end_term')
                                ->orWhere('end_term', '>=', $currentTerm->value);
                        });
                });
            } elseif ($enrollmentStatus === 'inactive') {
                $query->whereDoesntHave('classHistory', function ($q) use ($currentSession, $currentTerm) {
                    $q->where('session_id', $currentSession->id)
                        ->where('is_active', true)
                        ->whereNull('leave_date')
                        ->where('start_term', '<=', $currentTerm->value)
                        ->where(function ($q) use ($currentTerm) {
                            $q->whereNull('end_term')
                                ->orWhere('end_term', '>=', $currentTerm->value);
                        });
                })->orWhereHas('classHistory', function ($q) use ($currentSession, $currentTerm) {
                    $q->where('session_id', $currentSession->id)
                        ->where('is_active', false)
                        ->whereNotNull('leave_date');
                });
            }

            if ($feeStatus) {
                $query->whereHas('feePayments', function ($q) use ($currentSession, $currentTerm, $feeStatus) {
                    $q->where('session_id', $currentSession->id)
                        ->where('term', $currentTerm->value)
                        ->where('has_paid_fee', $feeStatus === 'paid');
                }, $feeStatus === 'unpaid' ? '<' : '>=', 1);
            }

            if ($approvalStatus) {
                $query->where('approved', $approvalStatus === 'approved');
            }

            $paginatedStudents = $query->paginate(10);
            $studentsClasses = $paginatedStudents->groupBy(function ($student) use ($currentSession, $currentTerm) {
                $classHistory = $student->classHistory
                    ->where('session_id', $currentSession->id)
                    ->firstWhere(function ($history) use ($currentSession, $currentTerm) {
                        return $history->isActiveInTerm($currentSession->id, $currentTerm->value);
                    });

                return $classHistory && $classHistory->class
                    ? $classHistory->class->name
                    : ($student->getCurrentClass($currentSession->id, $currentTerm) ?? 'Unassigned');
            });

            $statsResponse = $this->getStats($request);
            $stats = $statsResponse instanceof JsonResponse ? $statsResponse->getData(true) : [];

            if ($request->ajax()) {
                return view('admin.students.pagination', compact('studentsClasses', 'currentSession', 'currentTerm', 'action', 'enrollmentStatus', 'feeStatus', 'approvalStatus'))->render();
            }

            return view('admin.students.view_students', compact('studentsClasses', 'paginatedStudents', 'currentSession', 'currentTerm', 'action', 'enrollmentStatus', 'feeStatus', 'approvalStatus', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error in index: ' . $e->getMessage());
            return redirect()->route('admin.manage_academic_sessions')->with('error', 'An error occurred while fetching students. Please ensure a current session is set.');
        }
    }

    public function edit(Request $request, Student $student)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        if (!$currentSession || !$currentTerm) {
            return redirect()->route('admin.manage_academic_sessions')->with('error', 'No current session or term set. Please set one first.');
        }

        $classes = Classes::orderBy('hierarchy')->get();
        $termChoices = collect(TermEnum::cases())->map(function ($term) {
            return (object) ['value' => $term->value, 'label' => $term->value];
        });
        $classChoices = $classes->map(function ($class) {
            return (object) ['id' => $class->id, 'name' => $class->name];
        });

        $currentClassHistory = StudentClassHistory::where('student_id', $student->id)
            ->where('session_id', $currentSession->id)
            ->where('is_active', true)
            ->whereNull('leave_date')
            ->first();

        $currentClassId = $currentClassHistory ? $currentClassHistory->class_id : null;
        $currentStartTerm = $currentClassHistory ? $currentClassHistory->start_term : $currentTerm->value;
        $currentClassName = $currentClassHistory && $currentClassHistory->class
            ? $currentClassHistory->class->name
            : ($student->getCurrentClass($currentSession->id, $currentTerm) ?? 'Unassigned');

        Log::debug("Edit student {$student->reg_no}: ", [
            'student_id' => $student->id,
            'session_id' => $currentSession->id,
            'current_term' => $currentTerm->value,
            'current_class_id' => $currentClassId,
            'current_start_term' => $currentStartTerm,
            'current_class_name' => $currentClassName
        ]);

        return view('admin.students.edit_student', compact(
            'student',
            'classChoices',
            'termChoices',
            'currentSession',
            'currentTerm',
            'currentClassId',
            'currentStartTerm',
            'currentClassName'
        ));
    }

    public function update(Request $request, Student $student)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        if (!$currentSession || !$currentTerm) {
            return redirect()->route('admin.manage_academic_sessions')->with('error', 'No current session or term set. Please set one first.');
        }

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

            if ($currentClassHistory && ($currentClassHistory->class_id != $validated['class_id'] || $currentClassHistory->start_term != $validated['start_term'])) {
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
                'student_id' => $student->id,
                'class_id' => $validated['class_id'],
                'start_term' => $validated['start_term'],
            ]);

            return redirect()->route('admin.students', ['action' => 'view_students'])->with('success', 'Student updated successfully!');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Database error updating student {$student->id}: " . $e->getMessage());
            return back()->with('error', 'Database error occurred.');
        }
    }

    public function destroy(Student $student)
    {
        try {
            DB::beginTransaction();

            $regNo = $student->reg_no;
            $studentId = $student->id;
            $userId = $student->user_id;

            StudentClassHistory::where('student_id', $studentId)->forceDelete();
            FeePayment::where('student_id', $studentId)->forceDelete();
            Result::where('student_id', $studentId)->forceDelete();

            if ($userId) {
                User::where('id', $userId)->forceDelete();
            }

            $student->forceDelete();

            $this->logActivity("Permanently deleted student: {$regNo}", [
                'student_id' => $studentId,
                'reg_no' => $regNo,
                'user_id' => $userId,
                'timestamp' => Carbon::now('Africa/Lagos')->toDateTimeString(),
            ]);

            DB::commit();

            return back()->with('success', 'Student permanently deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Database error permanently deleting student {$student->id}: " . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Database error occurred during deletion.');
        }
    }

    public function studentReenroll(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        if (!$currentSession || !$currentTerm) {
            return redirect()->route('admin.manage_academic_sessions')->with('error', 'No current session or term set. Please set one first.');
        }

        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'class_id' => 'required|exists:classes,id',
                    'start_term' => 'required|in:First,Second,Third',
                ]);

                $termOrder = [
                    TermEnum::FIRST->value => 1,
                    TermEnum::SECOND->value => 2,
                    TermEnum::THIRD->value => 3,
                ];

                $startTermOrder = $termOrder[$validated['start_term']] ?? 1;
                $currentTermOrder = $termOrder[$currentTerm->value] ?? 1;

                if ($startTermOrder < $currentTermOrder) {
                    return back()->with('error', 'Cannot re-enroll in a past term.');
                }

                $currentClassHistory = StudentClassHistory::where('student_id', $studentId)
                    ->where('session_id', $currentSession->id)
                    ->where('is_active', true)
                    ->whereNull('leave_date')
                    ->where('start_term', '<=', $validated['start_term'])
                    ->where(function ($q) use ($validated) {
                        $q->whereNull('end_term')
                            ->orWhere('end_term', '>=', $validated['start_term']);
                    })
                    ->first();

                if ($currentClassHistory) {
                    return back()->with('error', 'Student is already enrolled in the current session and term.');
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
                    'class_id' => $validated['class_id'],
                    'start_term' => $validated['start_term'],
                    'session_id' => $currentSession->id,
                    'timestamp' => Carbon::now('Africa/Lagos')->toDateTimeString(),
                ]);

                return redirect()->route('admin.students', ['action' => 'view_students'])->with('success', 'Student re-enrolled successfully!');

            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                Log::error("Database error re-enrolling student {$studentId}: " . $e->getMessage());
                return back()->with('error', 'Database error occurred: ' . $e->getMessage());
            }
        }

        $classes = Classes::orderBy('hierarchy')->get();
        $termChoices = collect(TermEnum::cases())->map(function ($term) {
            return (object) ['value' => $term->value, 'label' => $term->value];
        });

        return view('admin.students.reenroll_student', compact('student', 'classes', 'termChoices', 'currentSession', 'currentTerm'));
    }

    public function markAsLeft(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        if (!$currentSession || !$currentTerm) {
            return redirect()->route('admin.manage_academic_sessions')->with('error', 'No current session or term set. Please set one first.');
        }

        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'session_id' => 'required|exists:academic_sessions,id',
                    'term' => 'required|in:First,Second,Third',
                ]);

                $classHistory = StudentClassHistory::where('student_id', $studentId)
                    ->where('session_id', $validated['session_id'])
                    ->where('is_active', true)
                    ->whereNull('leave_date')
                    ->first();

                if (!$classHistory) {
                    return back()->with('error', 'Student is not active in the specified session. Check if the student is enrolled.');
                }

                $termOrder = [
                    TermEnum::FIRST->value => 1,
                    TermEnum::SECOND->value => 2,
                    TermEnum::THIRD->value => 3,
                ];

                $startOrder = $termOrder[$classHistory->start_term] ?? 1;
                $leaveOrder = $termOrder[$validated['term']] ?? 1;

                if ($startOrder < $leaveOrder) {
                    $previousTerm = array_search($leaveOrder - 1, $termOrder);
                    $classHistory->update([
                        'end_term' => $previousTerm,
                        'is_active' => true,
                        'leave_date' => null,
                    ]);

                    StudentClassHistory::create([
                        'student_id' => $student->id,
                        'session_id' => $validated['session_id'],
                        'class_id' => $classHistory->class_id,
                        'start_term' => $validated['term'],
                        'join_date' => Carbon::now('Africa/Lagos'),
                        'is_active' => false,
                        'leave_date' => Carbon::now('Africa/Lagos'),
                        'end_term' => $validated['term'],
                    ]);
                } else {
                    $classHistory->markAsLeft($validated['term']);
                }

                $this->logActivity("Marked student as left: {$student->reg_no}", [
                    'student_id' => $student->id,
                    'session_id' => $validated['session_id'],
                    'term' => $validated['term'],
                    'timestamp' => Carbon::now('Africa/Lagos')->toDateTimeString(),
                ]);

                return redirect()->route('admin.students', ['action' => 'view_students'])->with('success', 'Student marked as left successfully!');

            } catch (ValidationException $e) {
                Log::error("Validation error marking student {$studentId} as left: " . json_encode($e->errors()));
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                Log::error("Database error marking student {$studentId} as left: " . $e->getMessage());
                return back()->with('error', 'Database error occurred: ' . $e->getMessage());
            }
        }

        $sessions = AcademicSession::orderBy('year', 'desc')->get();
        if ($sessions->isEmpty()) {
            return redirect()->route('admin.manage_academic_sessions')->with('error', 'No academic sessions available. Please create a session first.');
        }

        $termChoices = collect(TermEnum::cases())->map(function ($term) {
            return (object) ['value' => $term->value, 'label' => $term->value];
        });

        return view('admin.students.mark_as_left', compact('student', 'sessions', 'termChoices', 'currentSession', 'currentTerm'));
    }

    public function toggleFeeStatus(Request $request, $studentId)
    {
        try {
            $validated = $request->validate([
                'session_id' => 'required|exists:academic_sessions,id',
                'term' => 'required|in:First,Second,Third',
            ]);

            $student = Student::findOrFail($studentId);
            $feePayment = FeePayment::where('student_id', $studentId)
                ->where('session_id', $validated['session_id'])
                ->where('term', $validated['term'])
                ->first();

            if ($feePayment) {
                $feePayment->update(['has_paid_fee' => !$feePayment->has_paid_fee]);
            } else {
                FeePayment::create([
                    'student_id' => $studentId,
                    'session_id' => $validated['session_id'],
                    'term' => $validated['term'],
                    'has_paid_fee' => true,
                ]);
            }

            $this->logActivity("Toggled fee status for student {$student->reg_no}", [
                'student_id' => $studentId,
                'term' => $validated['term'],
                'has_paid_fee' => $feePayment ? !$feePayment->has_paid_fee : true,
            ]);

            return response()->json(['success' => true, 'message' => 'Fee status updated successfully']);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            Log::error("Error toggling fee status for student {$studentId}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating fee status'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function toggleApprovalStatus(Request $request, $studentId)
    {
        try {
            $student = Student::findOrFail($studentId);
            $student->update(['approved' => !$student->approved]);

            $this->logActivity("Toggled approval status for student {$student->reg_no}", [
                'student_id' => $student->id,
                'approved' => $student->approved,
            ]);

            return response()->json(['success' => true, 'message' => 'Approval status updated successfully']);

        } catch (\Exception $e) {
            Log::error("Error toggling approval status for student {$studentId}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating approval status'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getStats(Request $request)
    {
        try {
            $currentSession = AcademicSession::where('is_current', true)->firstOrFail();
            $currentTerm = TermEnum::tryFrom($request->input('term', TermEnum::FIRST->value)) ?? TermEnum::FIRST;

            $totalStudents = Student::whereHas('classHistory', function ($query) use ($currentSession, $currentTerm) {
                $query->where('session_id', $currentSession->id)
                    ->where('start_term', '<=', $currentTerm->value)
                    ->where(function ($q) use ($currentTerm) {
                        $q->whereNull('end_term')
                            ->orWhere('end_term', '>=', $currentTerm->value);
                    })
                    ->where('is_active', true)
                    ->whereNull('leave_date');
            })->count();

            $approvedStudents = Student::whereHas('classHistory', function ($query) use ($currentSession, $currentTerm) {
                $query->where('session_id', $currentSession->id)
                    ->where('start_term', '<=', $currentTerm->value)
                    ->where(function ($q) use ($currentTerm) {
                        $q->whereNull('end_term')
                            ->orWhere('end_term', '>=', $currentTerm->value);
                    })
                    ->where('is_active', true)
                    ->whereNull('leave_date');
            })->where('approved', true)->count();

            $feesPaid = FeePayment::where('session_id', $currentSession->id)
                ->where('term', $currentTerm->value)
                ->where('has_paid_fee', true)
                ->count();

            $feesNotPaid = Student::whereHas('classHistory', function ($query) use ($currentSession, $currentTerm) {
                $query->where('session_id', $currentSession->id)
                    ->where('start_term', '<=', $currentTerm->value)
                    ->where(function ($q) use ($currentTerm) {
                        $q->whereNull('end_term')
                            ->orWhere('end_term', '>=', $currentTerm->value);
                    })
                    ->where('is_active', true)
                    ->whereNull('leave_date');
            })->whereDoesntHave('feePayments', function ($query) use ($currentSession, $currentTerm) {
                $query->where('session_id', $currentSession->id)
                    ->where('term', $currentTerm->value)
                    ->where('has_paid_fee', true);
            })->count();

            return response()->json([
                'total_students' => $totalStudents,
                'approved_students' => $approvedStudents,
                'fees_paid' => $feesPaid,
                'fees_not_paid' => $feesNotPaid,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching stats: ' . $e->getMessage());
            return response()->json([
                'total_students' => 0,
                'approved_students' => 0,
                'fees_paid' => 0,
                'fees_not_paid' => 0,
            ], 500);
        }
    }

    public function searchStudents(Request $request, string $action)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error("Error searching students: " . $e->getMessage());
            return back()->with('error', 'Error searching students.');
        }
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
                'message_length' => strlen($message),
            ]);

            return view('admin.students.print_message', compact('students', 'message'));

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error printing student message: " . $e->getMessage());
            return back()->with('error', 'Error generating print message.');
        }
    }
}
