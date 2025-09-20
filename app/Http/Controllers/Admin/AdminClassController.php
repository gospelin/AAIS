<?php

namespace App\Http\Controllers\Admin;

use App\Models\Classes;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Models\FeePayment;
use App\Models\AcademicSession;
use App\Enums\TermEnum;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AdminClassController extends AdminBaseController
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('manage_classes');

        $classes = Classes::orderBy('hierarchy')->paginate(5);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.classes.class_table', compact('classes'))->render(),
                'pagination' => $classes->links('vendor.pagination.bootstrap-5')->render(),
            ]);
        }
        return view('admin.classes.manage_classes', compact('classes'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage_classes');

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50|unique:classes,name',
                'section' => 'nullable|string|max:20',
                'hierarchy' => 'required|integer|min:1|unique:classes,hierarchy',
            ]);

            $class = Classes::create($validated);
            $this->logActivity("Class created: {$validated['name']}", ['class_id' => $class->id]);

            return redirect()->route('admin.classes.index')->with('success', "Class '{$validated['name']}' created successfully!");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error("Integrity error creating class: " . $e->getMessage());
            return back()->with('error', 'Creation failed: Duplicate name or hierarchy.')->withInput();
        } catch (\Exception $e) {
            Log::error("Database error creating class: " . $e->getMessage());
            return back()->with('error', 'Database error occurred.')->withInput();
        }
    }

    public function edit($id)
    {
        $this->authorize('manage_classes');

        try {
            $class = Classes::findOrFail($id);
            return view('admin.classes.edit_class', compact('class'));
        } catch (\Exception $e) {
            Log::error("Error loading edit class view for class {$id}: " . $e->getMessage());
            return redirect()->route('admin.classes.index')->with('error', 'Error loading class edit form.');
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('manage_classes');

        try {
            $class = Classes::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:50|unique:classes,name,' . $id,
                'section' => 'nullable|string|max:20',
                'hierarchy' => 'required|integer|min:1|unique:classes,hierarchy,' . $id,
            ]);

            $class->update($validated);

            $this->logActivity("Class updated: {$validated['name']}", ['class_id' => $class->id]);

            return redirect()->route('admin.classes.index')->with('success', "Class '{$validated['name']}' updated successfully!");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error("Integrity error updating class {$id}: " . $e->getMessage());
            return back()->with('error', 'Update failed: Duplicate name or hierarchy.')->withInput();
        } catch (\Exception $e) {
            Log::error("Database error updating class {$id}: " . $e->getMessage());
            return back()->with('error', 'Database error occurred.')->withInput();
        }
    }

    public function destroy($id)
    {
        $this->authorize('manage_classes');

        try {
            $class = Classes::findOrFail($id);

            $activeStudents = StudentClassHistory::where('class_id', $class->id)
                ->where('is_active', true)
                ->whereNull('leave_date')
                ->count();

            if ($activeStudents > 0) {
                return redirect()->route('admin.classes.index')->with('error', 'Cannot delete class with active student enrollments.');
            }

            $className = $class->name;
            $class->delete();

            $this->logActivity("Class deleted: {$className}", ['class_id' => $class->id]);

            return redirect()->route('admin.classes.index')->with('success', "Class '{$className}' deleted successfully!");
        } catch (\Exception $e) {
            Log::error("Database error deleting class {$id}: " . $e->getMessage());
            return redirect()->route('admin.classes.index')->with('error', 'Failed to delete class. Please try again.');
        }
    }

    public function delete($id)
    {
        $this->authorize('manage_classes');

        try {
            $class = Classes::findOrFail($id);
            return view('admin.classes.delete_class', compact('class'));
        } catch (\Exception $e) {
            Log::error("Error loading delete class view for class {$id}: " . $e->getMessage());
            return redirect()->route('admin.classes.index')->with('error', 'Error loading class deletion form.');
        }
    }

    public function selectClass(Request $request, string $action)
    {
        $this->authorize('manage_classes');

        try {
            [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            $classes = Classes::orderBy('hierarchy')->get();

            if (!$currentSession || !$currentTerm) {
                return view('admin.classes.select_class', [
                    'classes' => $classes,
                    'action' => $action,
                    'currentSession' => null,
                    'currentTerm' => null,
                ])->with('error', 'No current academic session or term set. Please configure them first.');
            }

            if ($request->isMethod('post')) {
                $validated = $request->validate([
                    'class_id' => 'required|exists:classes,id',
                ]);

                $class = Classes::findOrFail($validated['class_id']);
                Session::put('selected_class_id', $validated['class_id']);

                return redirect()->route('admin.students_by_class', [
                    'className' => urlencode($class->name),
                    'action' => $action,
                    'session_id' => $currentSession->id,
                    'term' => $currentTerm->value,
                ]);
            }

            return view('admin.classes.select_class', [
                'classes' => $classes,
                'action' => $action,
                'currentSession' => $currentSession,
                'currentTerm' => $currentTerm,
            ]);
        } catch (\Exception $e) {
            Log::error("Error in selectClass: " . $e->getMessage());
            return redirect()->route('admin.classes.index')->with('error', 'Error selecting class: ' . $e->getMessage());
        }
    }

    public function studentsByClass(Request $request, string $className, string $action)
    {
        $this->authorize('manage_classes');

        try {
            [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            if (!$currentSession || !$currentTerm) {
                return redirect()->route('admin.select_class', ['action' => $action])
                    ->with('error', 'No current academic session or term set. Please set one in the session management.');
            }

            $sessionId = $request->query('session_id', $currentSession->id);
            $selectedSession = AcademicSession::find($sessionId);
            if (!$selectedSession) {
                return redirect()->route('admin.select_class', ['action' => $action])
                    ->with('error', 'Invalid academic session selected.');
            }

            $nextSession = $selectedSession->getNextSession();

            $termInput = $request->query('term', $currentTerm->value);
            $currentTerm = TermEnum::tryFrom($termInput) ?? $currentTerm;

            $class = Classes::where('name', urldecode($className))->firstOrFail();
            $enrollmentStatus = $request->query('enrollment_status', 'active');
            $feeStatus = $request->query('fee_status', '');
            $approvalStatus = $request->query('approval_status', '');

            $studentsQuery = $this->getStudentsQuery($selectedSession, $currentTerm->value)
                ->where('classes.id', $class->id);

            $studentsQuery = $this->applyFiltersToStudentsQuery(
                $studentsQuery,
                $enrollmentStatus,
                $feeStatus,
                $approvalStatus,
                $selectedSession,
                $currentTerm,
                $currentTerm->value
            );

            $students = $studentsQuery->orderBy('students.first_name')
                ->orderBy('students.last_name')
                ->paginate(5)
                ->appends([
                    'session_id' => $sessionId,
                    'term' => $termInput,
                    'enrollment_status' => $enrollmentStatus,
                    'fee_status' => $feeStatus,
                    'approval_status' => $approvalStatus,
                ]);

            $statsResponse = $this->getStats($request, $class->id);
            $stats = $statsResponse instanceof JsonResponse ? $statsResponse->getData(true) : [];

            $sessions = AcademicSession::orderBy('year', 'desc')->get();
            $termChoices = collect(TermEnum::cases())->map(function ($term) {
                return (object) ['value' => $term->value, 'label' => $term->value];
            });

            $allClasses = Classes::orderBy('hierarchy')->get();

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.classes.pagination', compact(
                        'students',
                        'class',
                        'selectedSession',
                        'currentTerm',
                        'action',
                        'enrollmentStatus',
                        'feeStatus',
                        'approvalStatus',
                        'sessions',
                        'termChoices',
                        'nextSession',
                        'allClasses'
                    ))->render(),
                    'pagination' => $students->links('vendor.pagination.bootstrap-5')->render(),
                ]);
            }

            return view('admin.classes.students_by_class', compact(
                'students',
                'class',
                'selectedSession',
                'currentTerm',
                'action',
                'enrollmentStatus',
                'feeStatus',
                'approvalStatus',
                'stats',
                'sessions',
                'termChoices',
                'nextSession',
                'allClasses'
            ));
        } catch (\Exception $e) {
            Log::error("Error in studentsByClass for class {$className}: " . $e->getMessage());
            return redirect()->route('admin.select_class', ['action' => $action])
                ->with('error', 'Error fetching students for class: ' . $e->getMessage());
        }
    }

    public function suggestNextClass($classId)
    {
        $this->authorize('manage_classes');

        try {
            $currentClass = Classes::findOrFail($classId);
            $nextClass = Classes::getNextClass($currentClass->hierarchy);

            if ($nextClass) {
                return response()->json([
                    'success' => true,
                    'class_id' => $nextClass->id,
                    'class_name' => $nextClass->name,
                ]);
            }

            return response()->json([
                'success' => false,
                'class_id' => null,
                'message' => 'No class with a higher hierarchy exists.',
            ]);
        } catch (\Exception $e) {
            Log::error("Error suggesting next class for class {$classId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'class_id' => null,
                'message' => 'Error fetching next class.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function suggestPreviousClass($classId)
    {
        $this->authorize('manage_classes');

        try {
            $currentClass = Classes::findOrFail($classId);
            $previousClass = Classes::getPreviousClass($currentClass->hierarchy);

            if ($previousClass) {
                return response()->json([
                    'success' => true,
                    'class_id' => $previousClass->id,
                    'class_name' => $previousClass->name,
                ]);
            }

            return response()->json([
                'success' => false,
                'class_id' => null,
                'message' => 'No class with a lower hierarchy exists.',
            ]);
        } catch (\Exception $e) {
            Log::error("Error suggesting previous class for class {$classId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'class_id' => null,
                'message' => 'Error fetching previous class.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function promoteStudent(Request $request, string $className, $studentId, string $action)
    {
        $this->authorize('manage_classes');

        try {
            [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            if (!$currentSession || !$currentTerm) {
                return redirect()->route('admin.students_by_class', ['className' => urlencode($className), 'action' => $action])
                    ->with('error', 'No current academic session or term set.');
            }

            $validated = $request->validate([
                'session_id' => 'required|exists:academic_sessions,id',
                'term' => 'required|in:First,Second,Third',
                'promotion_session_id' => 'required|exists:academic_sessions,id',
                'target_class_id' => 'required|exists:classes,id',
            ]);

            $selectedSession = AcademicSession::findOrFail($validated['session_id']);
            $selectedTerm = TermEnum::tryFrom($validated['term']) ?? $currentTerm;
            $student = Student::findOrFail($studentId);
            $currentClass = Classes::where('name', urldecode($className))->firstOrFail();
            $targetClass = Classes::findOrFail($validated['target_class_id']);
            $targetSession = AcademicSession::findOrFail($validated['promotion_session_id']);

            // Log initial context
            Log::info("Promoting student {$student->reg_no}", [
                'student_id' => $studentId,
                'current_class' => $currentClass->name,
                'current_session' => $currentSession->year,
                'filtered_session' => $selectedSession->year,
                'modal_selected_session' => $targetSession->year,
                'target_class' => $targetClass->name,
                'term' => $selectedTerm->value,
            ]);

            // Validate target class hierarchy
            if ($targetClass->hierarchy <= $currentClass->hierarchy) {
                return redirect()->route('admin.students_by_class', [
                    'className' => urlencode($currentClass->name),
                    'action' => $action,
                    'session_id' => $validated['session_id'],
                    'term' => $validated['term'],
                ])->with('error', 'Target class must have a higher hierarchy for promotion.');
            }

            DB::beginTransaction();

            $currentHistory = StudentClassHistory::where('student_id', $studentId)
                ->where('session_id', $selectedSession->id)
                ->where('class_id', $currentClass->id)
                ->where('is_active', true)
                ->whereNull('leave_date')
                ->where('start_term', '<=', $selectedTerm->value)
                ->where(function ($q) use ($selectedTerm) {
                    $q->whereNull('end_term')
                        ->orWhere('end_term', '>=', $selectedTerm->value);
                })
                ->first();

            // Check session comparisons
            $isCurrentSessionSame = $currentSession->id === $targetSession->id;
            $isFilteredSessionSame = $selectedSession->id === $targetSession->id;
            $isSameSession = $isCurrentSessionSame && $isFilteredSessionSame;

            Log::info("Session comparison for student {$student->reg_no}", [
                'is_current_session_same_as_modal' => $isCurrentSessionSame ? 'yes' : 'no',
                'is_filtered_session_same_as_modal' => $isFilteredSessionSame ? 'yes' : 'no',
                'is_same_session' => $isSameSession ? 'yes' : 'no',
            ]);

            if ($isSameSession) {
                // Same session: Update the existing record
                if ($currentHistory) {
                    $currentHistory->update([
                        'class_id' => $targetClass->id,
                        'start_term' => $selectedTerm->value,
                        'join_date' => Carbon::now('Africa/Lagos'),
                    ]);
                    Log::info("Updated existing record for student {$student->reg_no} to {$targetClass->name} in session {$targetSession->year}", [
                        'student_id' => $studentId,
                        'previous_class_id' => $currentClass->id,
                        'new_class_id' => $targetClass->id,
                        'session_id' => $targetSession->id,
                    ]);
                } else {
                    // Create new record if none exists
                    StudentClassHistory::create([
                        'student_id' => $student->id,
                        'session_id' => $targetSession->id,
                        'class_id' => $targetClass->id,
                        'start_term' => $selectedTerm->value,
                        'join_date' => Carbon::now('Africa/Lagos'),
                        'is_active' => true,
                    ]);
                    Log::info("Created new record for student {$student->reg_no} in {$targetClass->name} for session {$targetSession->year} (no existing record)", [
                        'student_id' => $studentId,
                        'new_class_id' => $targetClass->id,
                        'session_id' => $targetSession->id,
                    ]);
                }

                // Verify previous class record is removed
                $previousRecordExists = StudentClassHistory::where('student_id', $studentId)
                    ->where('session_id', $selectedSession->id)
                    ->where('class_id', $currentClass->id)
                    ->where('is_active', true)
                    ->whereNull('leave_date')
                    ->exists();
                Log::info("Previous class record check for {$student->reg_no} in {$currentClass->name} (session {$selectedSession->year}): " . ($previousRecordExists ? 'exists' : 'does not exist'));
            } else {
                // Different session: Preserve current record, create new one
                if ($currentHistory) {
                    // Keep the current record active for the selected session
                    Log::info("Preserving active record for student {$student->reg_no} in {$currentClass->name} for session {$selectedSession->year}", [
                        'student_id' => $studentId,
                        'class_id' => $currentClass->id,
                        'session_id' => $selectedSession->id,
                    ]);
                } else {
                    Log::info("No active record found for student {$student->reg_no} in {$currentClass->name} for session {$selectedSession->year}");
                }

                // Check if a record already exists for the target session and class
                $existingTargetRecord = StudentClassHistory::where('student_id', $studentId)
                    ->where('session_id', $targetSession->id)
                    ->where('class_id', $targetClass->id)
                    ->where('is_active', true)
                    ->whereNull('leave_date')
                    ->first();

                if (!$existingTargetRecord) {
                    StudentClassHistory::create([
                        'student_id' => $student->id,
                        'session_id' => $targetSession->id,
                        'class_id' => $targetClass->id,
                        'start_term' => TermEnum::FIRST->value, // Start from First term for new session
                        'join_date' => Carbon::now('Africa/Lagos'),
                        'is_active' => true,
                    ]);
                    Log::info("Created new record for student {$student->reg_no} in {$targetClass->name} for session {$targetSession->year}", [
                        'student_id' => $studentId,
                        'new_class_id' => $targetClass->id,
                        'session_id' => $targetSession->id,
                    ]);
                } else {
                    Log::info("Record already exists for student {$student->reg_no} in {$targetClass->name} for session {$targetSession->year}, no new record created", [
                        'student_id' => $studentId,
                        'class_id' => $targetClass->id,
                        'session_id' => $targetSession->id,
                    ]);
                }

                // Verify previous class record still exists
                $previousRecordExists = StudentClassHistory::where('student_id', $studentId)
                    ->where('session_id', $selectedSession->id)
                    ->where('class_id', $currentClass->id)
                    ->where('is_active', true)
                    ->whereNull('leave_date')
                    ->exists();
                Log::info("Previous class record check for {$student->reg_no} in {$currentClass->name} (session {$selectedSession->year}): " . ($previousRecordExists ? 'exists' : 'does not exist'));
            }

            $this->logActivity("Promoted student {$student->reg_no} to {$targetClass->name} in session {$targetSession->year}", [
                'student_id' => $student->id,
                'new_class_id' => $targetClass->id,
                'session_id' => $targetSession->id,
                'term' => $selectedTerm->value,
            ]);

            DB::commit();

            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($currentClass->name),
                'action' => $action,
                'session_id' => $validated['session_id'],
                'term' => $validated['term'],
            ])->with('success', "Student promoted to {$targetClass->name} in session {$targetSession->year} successfully!");
        } catch (ValidationException $e) {
            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($className),
                'action' => $action,
                'session_id' => $request->input('session_id', $currentSession->id),
                'term' => $request->input('term', $currentTerm->value),
            ])->withErrors($e->errors())->with('error', 'Validation error occurred.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error promoting student {$studentId}: " . $e->getMessage());
            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($className),
                'action' => $action,
                'session_id' => $request->input('session_id', $currentSession->id),
                'term' => $request->input('term', $currentTerm->value),
            ])->with('error', 'Error promoting student: ' . $e->getMessage());
        }
    }

    public function demoteStudent(Request $request, string $className, $studentId, string $action)
    {
        $this->authorize('manage_classes');

        try {
            [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            if (!$currentSession || !$currentTerm) {
                return redirect()->route('admin.students_by_class', ['className' => urlencode($className), 'action' => $action])
                    ->with('error', 'No current academic session or term set.');
            }

            $validated = $request->validate([
                'session_id' => 'required|exists:academic_sessions,id',
                'term' => 'required|in:First,Second,Third',
                'promotion_session_id' => 'required|exists:academic_sessions,id',
                'target_class_id' => 'required|exists:classes,id',
            ]);

            $selectedSession = AcademicSession::findOrFail($validated['session_id']);
            $selectedTerm = TermEnum::tryFrom($validated['term']) ?? $currentTerm;
            $student = Student::findOrFail($studentId);
            $currentClass = Classes::where('name', urldecode($className))->firstOrFail();
            $targetClass = Classes::findOrFail($validated['target_class_id']);
            $targetSession = AcademicSession::findOrFail($validated['promotion_session_id']);

            // Log initial context
            Log::info("Demoting student {$student->reg_no}", [
                'student_id' => $studentId,
                'current_class' => $currentClass->name,
                'current_session' => $currentSession->year,
                'filtered_session' => $selectedSession->year,
                'modal_selected_session' => $targetSession->year,
                'target_class' => $targetClass->name,
                'term' => $selectedTerm->value,
            ]);

            // Validate target class hierarchy
            if ($targetClass->hierarchy >= $currentClass->hierarchy) {
                return redirect()->route('admin.students_by_class', [
                    'className' => urlencode($currentClass->name),
                    'action' => $action,
                    'session_id' => $validated['session_id'],
                    'term' => $validated['term'],
                ])->with('error', 'Target class must have a lower hierarchy for demotion.');
            }

            DB::beginTransaction();

            $currentHistory = StudentClassHistory::where('student_id', $studentId)
                ->where('session_id', $selectedSession->id)
                ->where('class_id', $currentClass->id)
                ->where('is_active', true)
                ->whereNull('leave_date')
                ->where('start_term', '<=', $selectedTerm->value)
                ->where(function ($q) use ($selectedTerm) {
                    $q->whereNull('end_term')
                        ->orWhere('end_term', '>=', $selectedTerm->value);
                })
                ->first();

            // Check session comparisons
            $isCurrentSessionSame = $currentSession->id === $targetSession->id;
            $isFilteredSessionSame = $selectedSession->id === $targetSession->id;
            $isSameSession = $isCurrentSessionSame && $isFilteredSessionSame;

            Log::info("Session comparison for student {$student->reg_no}", [
                'is_current_session_same_as_modal' => $isCurrentSessionSame ? 'yes' : 'no',
                'is_filtered_session_same_as_modal' => $isFilteredSessionSame ? 'yes' : 'no',
                'is_same_session' => $isSameSession ? 'yes' : 'no',
            ]);

            if ($isSameSession) {
                // Same session: Update the existing record
                if ($currentHistory) {
                    $currentHistory->update([
                        'class_id' => $targetClass->id,
                        'start_term' => $selectedTerm->value,
                        'join_date' => Carbon::now('Africa/Lagos'),
                    ]);
                    Log::info("Updated existing record for student {$student->reg_no} to {$targetClass->name} in session {$targetSession->year}", [
                        'student_id' => $studentId,
                        'previous_class_id' => $currentClass->id,
                        'new_class_id' => $targetClass->id,
                        'session_id' => $targetSession->id,
                    ]);
                } else {
                    // Create new record if none exists
                    StudentClassHistory::create([
                        'student_id' => $student->id,
                        'session_id' => $targetSession->id,
                        'class_id' => $targetClass->id,
                        'start_term' => $selectedTerm->value,
                        'join_date' => Carbon::now('Africa/Lagos'),
                        'is_active' => true,
                    ]);
                    Log::info("Created new record for student {$student->reg_no} in {$targetClass->name} for session {$targetSession->year} (no existing record)", [
                        'student_id' => $studentId,
                        'new_class_id' => $targetClass->id,
                        'session_id' => $targetSession->id,
                    ]);
                }

                // Verify previous class record is removed
                $previousRecordExists = StudentClassHistory::where('student_id', $studentId)
                    ->where('session_id', $selectedSession->id)
                    ->where('class_id', $currentClass->id)
                    ->where('is_active', true)
                    ->whereNull('leave_date')
                    ->exists();
                Log::info("Previous class record check for {$student->reg_no} in {$currentClass->name} (session {$selectedSession->year}): " . ($previousRecordExists ? 'exists' : 'does not exist'));
            } else {
                // Different session: Preserve current record, create new one
                if ($currentHistory) {
                    // Keep the current record active for the selected session
                    Log::info("Preserving active record for student {$student->reg_no} in {$currentClass->name} for session {$selectedSession->year}", [
                        'student_id' => $studentId,
                        'class_id' => $currentClass->id,
                        'session_id' => $selectedSession->id,
                    ]);
                } else {
                    Log::info("No active record found for student {$student->reg_no} in {$currentClass->name} for session {$selectedSession->year}");
                }

                // Check if a record already exists for the target session and class
                $existingTargetRecord = StudentClassHistory::where('student_id', $studentId)
                    ->where('session_id', $targetSession->id)
                    ->where('class_id', $targetClass->id)
                    ->where('is_active', true)
                    ->whereNull('leave_date')
                    ->first();

                if (!$existingTargetRecord) {
                    StudentClassHistory::create([
                        'student_id' => $student->id,
                        'session_id' => $targetSession->id,
                        'class_id' => $targetClass->id,
                        'start_term' => TermEnum::FIRST->value, // Start from First term for new session
                        'join_date' => Carbon::now('Africa/Lagos'),
                        'is_active' => true,
                    ]);
                    Log::info("Created new record for student {$student->reg_no} in {$targetClass->name} for session {$targetSession->year}", [
                        'student_id' => $studentId,
                        'new_class_id' => $targetClass->id,
                        'session_id' => $targetSession->id,
                    ]);
                } else {
                    Log::info("Record already exists for student {$student->reg_no} in {$targetClass->name} for session {$targetSession->year}, no new record created", [
                        'student_id' => $studentId,
                        'class_id' => $targetClass->id,
                        'session_id' => $targetSession->id,
                    ]);
                }

                // Verify previous class record still exists
                $previousRecordExists = StudentClassHistory::where('student_id', $studentId)
                    ->where('session_id', $selectedSession->id)
                    ->where('class_id', $currentClass->id)
                    ->where('is_active', true)
                    ->whereNull('leave_date')
                    ->exists();
                Log::info("Previous class record check for {$student->reg_no} in {$currentClass->name} (session {$selectedSession->year}): " . ($previousRecordExists ? 'exists' : 'does not exist'));
            }

            $this->logActivity("Demoted student {$student->reg_no} to {$targetClass->name} in session {$targetSession->year}", [
                'student_id' => $student->id,
                'new_class_id' => $targetClass->id,
                'session_id' => $targetSession->id,
                'term' => $selectedTerm->value,
            ]);

            DB::commit();

            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($currentClass->name),
                'action' => $action,
                'session_id' => $validated['session_id'],
                'term' => $validated['term'],
            ])->with('success', "Student demoted to {$targetClass->name} in session {$targetSession->year} successfully!");
        } catch (ValidationException $e) {
            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($className),
                'action' => $action,
                'session_id' => $request->input('session_id', $currentSession->id),
                'term' => $request->input('term', $currentTerm->value),
            ])->withErrors($e->errors())->with('error', 'Validation error occurred.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error demoting student {$studentId}: " . $e->getMessage());
            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($className),
                'action' => $action,
                'session_id' => $request->input('session_id', $currentSession->id),
                'term' => $request->input('term', $currentTerm->value),
            ])->with('error', 'Error demoting student: ' . $e->getMessage());
        }
    }

    public function bulkPromoteStudents(Request $request, string $className, string $action)
    {
        $this->authorize('manage_classes');

        try {
            [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            if (!$currentSession || !$currentTerm) {
                return redirect()->route('admin.students_by_class', ['className' => urlencode($className), 'action' => $action])
                    ->with('error', 'No current academic session or term set.');
            }

            // Validate input, ensuring student_ids is an array
            $validated = $request->validate([
                'session_id' => 'required|exists:academic_sessions,id',
                'term' => 'required|in:First,Second,Third',
                'promotion_session_id' => 'required|exists:academic_sessions,id',
                'target_class_id' => 'required|exists:classes,id',
                'student_ids' => 'required|array|min:1',
                'student_ids.*' => 'exists:students,id',
            ]);

            $selectedSession = AcademicSession::findOrFail($validated['session_id']);
            $selectedTerm = TermEnum::tryFrom($validated['term']) ?? $currentTerm;
            $currentClass = Classes::where('name', urldecode($className))->firstOrFail();
            $targetClass = Classes::findOrFail($validated['target_class_id']);
            $targetSession = AcademicSession::findOrFail($validated['promotion_session_id']);

            // Validate target class hierarchy
            if ($targetClass->hierarchy <= $currentClass->hierarchy) {
                return redirect()->route('admin.students_by_class', [
                    'className' => urlencode($currentClass->name),
                    'action' => $action,
                    'session_id' => $validated['session_id'],
                    'term' => $validated['term'],
                ])->with('error', 'Target class must have a higher hierarchy for promotion.');
            }

            DB::beginTransaction();

            $successCount = 0;
            $failedStudents = [];

            // Process each student ID
            foreach ($validated['student_ids'] as $studentId) {
                try {
                    $student = Student::findOrFail($studentId);

                    Log::info("Bulk promoting student {$student->reg_no}", [
                        'student_id' => $studentId,
                        'current_class' => $currentClass->name,
                        'current_session' => $currentSession->year,
                        'filtered_session' => $selectedSession->year,
                        'target_session' => $targetSession->year,
                        'target_class' => $targetClass->name,
                        'term' => $selectedTerm->value,
                        'is_same_session' => ($currentSession->id === $targetSession->id && $selectedSession->id === $targetSession->id) ? 'yes' : 'no',
                    ]);

                    $currentHistory = StudentClassHistory::where('student_id', $studentId)
                        ->where('session_id', $selectedSession->id)
                        ->where('class_id', $currentClass->id)
                        ->where('is_active', true)
                        ->whereNull('leave_date')
                        ->where('start_term', '<=', $selectedTerm->value)
                        ->where(function ($q) use ($selectedTerm) {
                            $q->whereNull('end_term')
                                ->orWhere('end_term', '>=', $selectedTerm->value);
                        })
                        ->first();

                    $isSameSession = $currentSession->id === $targetSession->id && $selectedSession->id === $targetSession->id;

                    if ($isSameSession) {
                        if ($currentHistory) {
                            $currentHistory->update([
                                'class_id' => $targetClass->id,
                                'start_term' => $selectedTerm->value,
                                'join_date' => Carbon::now('Africa/Lagos'),
                            ]);
                            Log::info("Updated existing record for student {$student->reg_no} to {$targetClass->name} in session {$targetSession->year}", [
                                'student_id' => $studentId,
                                'previous_class_id' => $currentClass->id,
                                'new_class_id' => $targetClass->id,
                                'session_id' => $targetSession->id,
                            ]);
                        } else {
                            StudentClassHistory::create([
                                'student_id' => $student->id,
                                'session_id' => $targetSession->id,
                                'class_id' => $targetClass->id,
                                'start_term' => $selectedTerm->value,
                                'join_date' => Carbon::now('Africa/Lagos'),
                                'is_active' => true,
                            ]);
                            Log::info("Created new record for student {$student->reg_no} in {$targetClass->name} for session {$targetSession->year} (no existing record)", [
                                'student_id' => $studentId,
                                'new_class_id' => $targetClass->id,
                                'session_id' => $targetSession->id,
                            ]);
                        }
                    } else {
                        if ($currentHistory) {
                            Log::info("Preserving active record for student {$student->reg_no} in {$currentClass->name} for session {$selectedSession->year}", [
                                'student_id' => $studentId,
                                'class_id' => $currentClass->id,
                                'session_id' => $selectedSession->id,
                            ]);
                        }

                        $existingTargetRecord = StudentClassHistory::where('student_id', $studentId)
                            ->where('session_id', $targetSession->id)
                            ->where('class_id', $targetClass->id)
                            ->where('is_active', true)
                            ->whereNull('leave_date')
                            ->first();

                        if (!$existingTargetRecord) {
                            StudentClassHistory::create([
                                'student_id' => $student->id,
                                'session_id' => $targetSession->id,
                                'class_id' => $targetClass->id,
                                'start_term' => TermEnum::FIRST->value,
                                'join_date' => Carbon::now('Africa/Lagos'),
                                'is_active' => true,
                            ]);
                            Log::info("Created new record for student {$student->reg_no} in {$targetClass->name} for session {$targetSession->year}", [
                                'student_id' => $studentId,
                                'new_class_id' => $targetClass->id,
                                'session_id' => $targetSession->id,
                            ]);
                        } else {
                            Log::info("Record already exists for student {$student->reg_no} in {$targetClass->name} for session {$targetSession->year}, no new record created", [
                                'student_id' => $studentId,
                                'class_id' => $targetClass->id,
                                'session_id' => $targetSession->id,
                            ]);
                        }
                    }

                    $this->logActivity("Bulk promoted student {$student->reg_no} to {$targetClass->name} in session {$targetSession->year}", [
                        'student_id' => $student->id,
                        'new_class_id' => $targetClass->id,
                        'session_id' => $targetSession->id,
                        'term' => $selectedTerm->value,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    Log::error("Error promoting student {$student->reg_no}: " . $e->getMessage());
                    $failedStudents[] = $student->reg_no;
                }
            }

            DB::commit();

            $message = "Successfully promoted {$successCount} student(s) to {$targetClass->name} in session {$targetSession->year}.";
            if (!empty($failedStudents)) {
                $message .= " Failed for: " . implode(', ', $failedStudents);
            }

            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($currentClass->name),
                'action' => $action,
                'session_id' => $validated['session_id'],
                'term' => $validated['term'],
            ])->with('success', $message);
        } catch (ValidationException $e) {
            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($className),
                'action' => $action,
                'session_id' => $request->input('session_id', $currentSession->id),
                'term' => $request->input('term', $currentTerm->value),
            ])->withErrors($e->errors())->with('error', 'Validation error occurred.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error bulk promoting students: " . $e->getMessage());
            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($className),
                'action' => $action,
                'session_id' => $request->input('session_id', $currentSession->id),
                'term' => $request->input('term', $currentTerm->value),
            ])->with('error', 'Error bulk promoting students: ' . $e->getMessage());
        }
    }

    public function bulkDemoteStudents(Request $request, string $className, string $action)
    {
        $this->authorize('manage_classes');

        try {
            [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            if (!$currentSession || !$currentTerm) {
                return redirect()->route('admin.students_by_class', ['className' => urlencode($className), 'action' => $action])
                    ->with('error', 'No current academic session or term set.');
            }

            // Validate input, ensuring student_ids is an array
            $validated = $request->validate([
                'session_id' => 'required|exists:academic_sessions,id',
                'term' => 'required|in:First,Second,Third',
                'promotion_session_id' => 'required|exists:academic_sessions,id',
                'target_class_id' => 'required|exists:classes,id',
                'student_ids' => 'required|array|min:1',
                'student_ids.*' => 'exists:students,id',
            ]);

            $selectedSession = AcademicSession::findOrFail($validated['session_id']);
            $selectedTerm = TermEnum::tryFrom($validated['term']) ?? $currentTerm;
            $currentClass = Classes::where('name', urldecode($className))->firstOrFail();
            $targetClass = Classes::findOrFail($validated['target_class_id']);
            $targetSession = AcademicSession::findOrFail($validated['promotion_session_id']);

            // Validate target class hierarchy
            if ($targetClass->hierarchy >= $currentClass->hierarchy) {
                return redirect()->route('admin.students_by_class', [
                    'className' => urlencode($currentClass->name),
                    'action' => $action,
                    'session_id' => $validated['session_id'],
                    'term' => $validated['term'],
                ])->with('error', 'Target class must have a lower hierarchy for demotion.');
            }

            DB::beginTransaction();

            $successCount = 0;
            $failedStudents = [];

            // Process each student ID
            foreach ($validated['student_ids'] as $studentId) {
                try {
                    $student = Student::findOrFail($studentId);

                    Log::info("Bulk demoting student {$student->reg_no}", [
                        'student_id' => $studentId,
                        'current_class' => $currentClass->name,
                        'current_session' => $currentSession->year,
                        'filtered_session' => $selectedSession->year,
                        'target_session' => $targetSession->year,
                        'target_class' => $targetClass->name,
                        'term' => $selectedTerm->value,
                        'is_same_session' => ($currentSession->id === $targetSession->id && $selectedSession->id === $targetSession->id) ? 'yes' : 'no',
                    ]);

                    $currentHistory = StudentClassHistory::where('student_id', $studentId)
                        ->where('session_id', $selectedSession->id)
                        ->where('class_id', $currentClass->id)
                        ->where('is_active', true)
                        ->whereNull('leave_date')
                        ->where('start_term', '<=', $selectedTerm->value)
                        ->where(function ($q) use ($selectedTerm) {
                            $q->whereNull('end_term')
                                ->orWhere('end_term', '>=', $selectedTerm->value);
                        })
                        ->first();

                    $isSameSession = $currentSession->id === $targetSession->id && $selectedSession->id === $targetSession->id;

                    if ($isSameSession) {
                        if ($currentHistory) {
                            $currentHistory->update([
                                'class_id' => $targetClass->id,
                                'start_term' => $selectedTerm->value,
                                'join_date' => Carbon::now('Africa/Lagos'),
                            ]);
                            Log::info("Updated existing record for student {$student->reg_no} to {$targetClass->name} in session {$targetSession->year}", [
                                'student_id' => $studentId,
                                'previous_class_id' => $currentClass->id,
                                'new_class_id' => $targetClass->id,
                                'session_id' => $targetSession->id,
                            ]);
                        } else {
                            StudentClassHistory::create([
                                'student_id' => $student->id,
                                'session_id' => $targetSession->id,
                                'class_id' => $targetClass->id,
                                'start_term' => $selectedTerm->value,
                                'join_date' => Carbon::now('Africa/Lagos'),
                                'is_active' => true,
                            ]);
                            Log::info("Created new record for student {$student->reg_no} in {$targetClass->name} for session {$targetSession->year} (no existing record)", [
                                'student_id' => $studentId,
                                'new_class_id' => $targetClass->id,
                                'session_id' => $targetSession->id,
                            ]);
                        }
                    } else {
                        if ($currentHistory) {
                            Log::info("Preserving active record for student {$student->reg_no} in {$currentClass->name} for session {$selectedSession->year}", [
                                'student_id' => $studentId,
                                'class_id' => $currentClass->id,
                                'session_id' => $selectedSession->id,
                            ]);
                        }

                        $existingTargetRecord = StudentClassHistory::where('student_id', $studentId)
                            ->where('session_id', $targetSession->id)
                            ->where('class_id', $targetClass->id)
                            ->where('is_active', true)
                            ->whereNull('leave_date')
                            ->first();

                        if (!$existingTargetRecord) {
                            StudentClassHistory::create([
                                'student_id' => $student->id,
                                'session_id' => $targetSession->id,
                                'class_id' => $targetClass->id,
                                'start_term' => TermEnum::FIRST->value,
                                'join_date' => Carbon::now('Africa/Lagos'),
                                'is_active' => true,
                            ]);
                            Log::info("Created new record for student {$student->reg_no} in {$targetClass->name} for session {$targetSession->year}", [
                                'student_id' => $studentId,
                                'new_class_id' => $targetClass->id,
                                'session_id' => $targetSession->id,
                            ]);
                        } else {
                            Log::info("Record already exists for student {$student->reg_no} in {$targetClass->name} for session {$targetSession->year}, no new record created", [
                                'student_id' => $studentId,
                                'class_id' => $targetClass->id,
                                'session_id' => $targetSession->id,
                            ]);
                        }
                    }

                    $this->logActivity("Bulk demoted student {$student->reg_no} to {$targetClass->name} in session {$targetSession->year}", [
                        'student_id' => $student->id,
                        'new_class_id' => $targetClass->id,
                        'session_id' => $targetSession->id,
                        'term' => $selectedTerm->value,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    Log::error("Error demoting student {$student->reg_no}: " . $e->getMessage());
                    $failedStudents[] = $student->reg_no;
                }
            }

            DB::commit();

            $message = "Successfully demoted {$successCount} student(s) to {$targetClass->name} in session {$targetSession->year}.";
            if (!empty($failedStudents)) {
                $message .= " Failed for: " . implode(', ', $failedStudents);
            }

            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($currentClass->name),
                'action' => $action,
                'session_id' => $validated['session_id'],
                'term' => $validated['term'],
            ])->with('success', $message);
        } catch (ValidationException $e) {
            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($className),
                'action' => $action,
                'session_id' => $request->input('session_id', $currentSession->id),
                'term' => $request->input('term', $currentTerm->value),
            ])->withErrors($e->errors())->with('error', 'Validation error occurred.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error bulk demoting students: " . $e->getMessage());
            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($className),
                'action' => $action,
                'session_id' => $request->input('session_id', $currentSession->id),
                'term' => $request->input('term', $currentTerm->value),
            ])->with('error', 'Error bulk demoting students: ' . $e->getMessage());
        }
    }

    public function deleteStudentClassRecord(Request $request, string $className, $studentId, string $action)
    {
        $this->authorize('manage_classes');

        try {
            [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            if (!$currentSession || !$currentTerm) {
                return redirect()->route('admin.students_by_class', ['className' => urlencode($className), 'action' => $action])
                    ->with('error', 'No current academic session or term set.');
            }

            $sessionId = $request->input('session_id', $currentSession->id);
            $selectedSession = AcademicSession::findOrFail($sessionId);
            $termInput = $request->input('term', $currentTerm->value);
            $selectedTerm = TermEnum::tryFrom($termInput) ?? $currentTerm;

            $student = Student::findOrFail($studentId);
            $class = Classes::where('name', urldecode($className))->firstOrFail();

            DB::beginTransaction();

            $history = StudentClassHistory::where('student_id', $studentId)
                ->where('session_id', $selectedSession->id)
                ->where('class_id', $class->id)
                ->where('is_active', true)
                ->whereNull('leave_date')
                ->where('start_term', '<=', $selectedTerm->value)
                ->where(function ($q) use ($selectedTerm) {
                    $q->whereNull('end_term')
                        ->orWhere('end_term', '>=', $selectedTerm->value);
                })
                ->first();

            if ($history) {
                $history->update([
                    'is_active' => false,
                    'leave_date' => Carbon::now('Africa/Lagos'),
                    'end_term' => $selectedTerm->value,
                ]);

                $this->logActivity("Deleted class record for student {$student->reg_no} in {$class->name}", [
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'session_id' => $selectedSession->id,
                    'term' => $selectedTerm->value,
                ]);

                DB::commit();

                return redirect()->route('admin.students_by_class', [
                    'className' => urlencode($class->name),
                    'action' => $action,
                    'session_id' => $sessionId,
                    'term' => $termInput,
                ])->with('success', 'Student class record deleted successfully!');
            }

            DB::commit();
            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($class->name),
                'action' => $action,
                'session_id' => $sessionId,
                'term' => $termInput,
            ])->with('error', 'No active class record found for this student.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting class record for student {$studentId}: " . $e->getMessage());
            return redirect()->route('admin.students_by_class', [
                'className' => urlencode($className),
                'action' => $action,
                'session_id' => $sessionId,
                'term' => $termInput,
            ])->with('error', 'Error deleting class record: ' . $e->getMessage());
        }
    }

    public function searchStudentsByClass(Request $request, string $className, string $action)
    {
        $this->authorize('manage_classes');

        try {
            [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            if (!$currentSession || !$currentTerm) {
                return redirect()->route('admin.select_class', ['action' => $action])
                    ->with('error', 'No current academic session or term set.');
            }

            $sessionId = $request->input('session_id', $currentSession->id);
            $selectedSession = AcademicSession::findOrFail($sessionId);

            $nextSession = $selectedSession->getNextSession();

            $termInput = $request->input('term', $currentTerm->value);
            $currentTerm = TermEnum::tryFrom($termInput) ?? $currentTerm;

            $class = Classes::where('name', urldecode($className))->firstOrFail();
            $studentsQuery = $this->getStudentsQuery($selectedSession, $currentTerm->value)
                ->where('classes.id', $class->id);

            $query = $request->input('query', '');
            if ($query) {
                $studentsQuery->where(function ($q) use ($query) {
                    $q->whereRaw('LOWER(students.first_name) LIKE ?', ['%' . strtolower($query) . '%'])
                        ->orWhereRaw('LOWER(students.last_name) LIKE ?', ['%' . strtolower($query) . '%'])
                        ->orWhere('students.reg_no', 'LIKE', '%' . $query . '%');
                });
            }

            $enrollmentStatus = $request->input('enrollment_status', 'active');
            $feeStatus = $request->input('fee_status', '');
            $approvalStatus = $request->input('approval_status', '');

            $studentsQuery = $this->applyFiltersToStudentsQuery(
                $studentsQuery,
                $enrollmentStatus,
                $feeStatus,
                $approvalStatus,
                $selectedSession,
                $currentTerm,
                $currentTerm->value
            );

            $students = $studentsQuery->orderBy('students.first_name')
                ->orderBy('students.last_name')
                ->paginate(5)
                ->appends([
                    'session_id' => $sessionId,
                    'term' => $termInput,
                    'enrollment_status' => $enrollmentStatus,
                    'fee_status' => $feeStatus,
                    'approval_status' => $approvalStatus,
                    'query' => $query,
                ]);

            $statsResponse = $this->getStats($request, $class->id);
            $stats = $statsResponse instanceof JsonResponse ? $statsResponse->getData(true) : [];

            $sessions = AcademicSession::orderBy('year', 'desc')->get();
            $termChoices = collect(TermEnum::cases())->map(function ($term) {
                return (object) ['value' => $term->value, 'label' => $term->value];
            });

            $allClasses = Classes::orderBy('hierarchy')->get();

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.classes.pagination', compact(
                        'students',
                        'class',
                        'selectedSession',
                        'currentTerm',
                        'action',
                        'enrollmentStatus',
                        'feeStatus',
                        'approvalStatus',
                        'sessions',
                        'termChoices',
                        'nextSession',
                        'allClasses'
                    ))->render(),
                    'pagination' => $students->links('vendor.pagination.bootstrap-5')->render(),
                ]);
            }

            return view('admin.classes.search_results', compact(
                'students',
                'class',
                'action',
                'selectedSession',
                'currentTerm',
                'query',
                'enrollmentStatus',
                'feeStatus',
                'approvalStatus',
                'stats',
                'sessions',
                'termChoices',
                'nextSession',
                'allClasses'
            ));
        } catch (\Exception $e) {
            Log::error("Error searching students for class {$className}: " . $e->getMessage());
            return redirect()->route('admin.select_class', ['action' => $action])
                ->with('error', 'Error searching students: ' . $e->getMessage());
        }
    }

    public function toggleFeeStatus(Request $request, $studentId)
    {
        $this->authorize('manage_classes');

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

            $newStatus = !$feePayment || !$feePayment->has_paid_fee;

            if ($feePayment) {
                $feePayment->update(['has_paid_fee' => $newStatus]);
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
                'session_id' => $validated['session_id'],
                'term' => $validated['term'],
                'has_paid_fee' => $newStatus,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fee status updated successfully',
                'new_status' => $newStatus ? 'paid' : 'unpaid'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            Log::error("Error toggling fee status for student {$studentId}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating fee status'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function toggleApprovalStatus(Request $request, $studentId)
    {
        $this->authorize('manage_classes');

        try {
            $student = Student::findOrFail($studentId);
            $student->update(['approved' => !$student->approved]);

            $this->logActivity("Toggled approval status for student {$student->reg_no}", [
                'student_id' => $student->id,
                'approved' => $student->approved,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Approval status updated successfully',
                'new_status' => $student->approved ? 'approved' : 'unapproved'
            ]);
        } catch (\Exception $e) {
            Log::error("Error toggling approval status for student {$studentId}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating approval status'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getStats(Request $request, $classId)
    {
        try {
            $sessionId = $request->input('session_id', AcademicSession::where('is_current', true)->firstOrFail()->id);
            $currentTerm = TermEnum::tryFrom($request->input('term', TermEnum::FIRST->value)) ?? TermEnum::FIRST;

            $totalStudents = Student::whereHas('classHistory', function ($query) use ($sessionId, $currentTerm, $classId) {
                $query->where('session_id', $sessionId)
                    ->where('class_id', $classId)
                    ->where('start_term', '<=', $currentTerm->value)
                    ->where(function ($q) use ($currentTerm) {
                        $q->whereNull('end_term')
                            ->orWhere('end_term', '>=', $currentTerm->value);
                    })
                    ->where('is_active', true)
                    ->whereNull('leave_date');
            })->count();

            $approvedStudents = Student::whereHas('classHistory', function ($query) use ($sessionId, $currentTerm, $classId) {
                $query->where('session_id', $sessionId)
                    ->where('class_id', $classId)
                    ->where('start_term', '<=', $currentTerm->value)
                    ->where(function ($q) use ($currentTerm) {
                        $q->whereNull('end_term')
                            ->orWhere('end_term', '>=', $currentTerm->value);
                    })
                    ->where('is_active', true)
                    ->whereNull('leave_date');
            })->where('approved', true)->count();

            $feesPaid = FeePayment::where('session_id', $sessionId)
                ->where('term', $currentTerm->value)
                ->where('has_paid_fee', true)
                ->whereIn('student_id', function ($query) use ($sessionId, $currentTerm, $classId) {
                    $query->select('student_id')
                        ->from('student_class_history')
                        ->where('session_id', $sessionId)
                        ->where('class_id', $classId)
                        ->where('start_term', '<=', $currentTerm->value)
                        ->where(function ($q) use ($currentTerm) {
                            $q->whereNull('end_term')
                                ->orWhere('end_term', '>=', $currentTerm->value);
                        })
                        ->where('is_active', true)
                        ->whereNull('leave_date');
                })->count();

            $feesNotPaid = Student::whereHas('classHistory', function ($query) use ($sessionId, $currentTerm, $classId) {
                $query->where('session_id', $sessionId)
                    ->where('class_id', $classId)
                    ->where('start_term', '<=', $currentTerm->value)
                    ->where(function ($q) use ($currentTerm) {
                        $q->whereNull('end_term')
                            ->orWhere('end_term', '>=', $currentTerm->value);
                    })
                    ->where('is_active', true)
                    ->whereNull('leave_date');
            })->whereDoesntHave('feePayments', function ($query) use ($sessionId, $currentTerm) {
                $query->where('session_id', $sessionId)
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
            Log::error("Error fetching stats for class {$classId}: " . $e->getMessage());
            return response()->json([
                'total_students' => 0,
                'approved_students' => 0,
                'fees_paid' => 0,
                'fees_not_paid' => 0,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
