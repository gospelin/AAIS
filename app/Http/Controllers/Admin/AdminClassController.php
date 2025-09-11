<?php

namespace App\Http\Controllers\Admin;

use App\Models\Classes;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Enums\TermEnum;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminClassController extends AdminBaseController
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('manage_classes');

        $classes = Classes::orderBy('hierarchy')->paginate(10);

        return view('admin.classes.manage_classes', compact('classes'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage_classes');

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50|unique:classes,name',
                'section' => 'nullable|string|max:20',
                'hierarchy' => 'required|integer|min:1|unique:classes,hierarchy', // Added min:1
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

        $class = Classes::findOrFail($id);
        return view('admin.classes.edit_class', compact('class'));
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

        $class = Classes::findOrFail($id);
        return view('admin.classes.delete_class', compact('class'));
    }
    public function selectClass(Request $request, string $action)
    {
        $classes = Classes::orderBy('hierarchy')->get();

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'class_id' => 'required|exists:classes,id',
            ]);

            $className = Classes::find($validated['class_id'])->name;

            return redirect()->route('admin.students_by_class', [
                'class_name' => urlencode($className),
                'action' => $action
            ]);
        }

        return view('admin.classes.select_class', compact('classes', 'action'));
    }

    public function studentsByClass(Request $request, string $className, string $action)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        $class = Classes::where('name', urldecode($className))->firstOrFail();
        $studentsQuery = $this->getStudentsQuery($currentSession, $currentTerm->value)
            ->where('classes.id', $class->id);

        $enrollmentStatus = $request->get('enrollment_status', 'active');
        $feeStatus = $request->get('fee_status', '');
        $approvalStatus = $request->get('approval_status', '');

        $studentsQuery = $this->applyFiltersToStudentsQuery($studentsQuery, $enrollmentStatus, $feeStatus, $approvalStatus, $currentSession, $currentTerm);

        $students = $studentsQuery->orderBy('students.first_name')->paginate(10);
        $studentsClasses = $this->groupStudentsByClass($students->items());

        return view('admin.classes.students_by_class', compact(
            'studentsClasses',
            'class',
            'action',
            'currentSession',
            'currentTerm',
            'enrollmentStatus',
            'feeStatus',
            'approvalStatus'
        ));
    }

    public function promoteStudent(Request $request, string $className, $studentId, string $action)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        $student = Student::findOrFail($studentId);
        $currentClass = Classes::where('name', urldecode($className))->firstOrFail();
        $nextClass = Classes::where('hierarchy', $currentClass->hierarchy + 1)->first();

        if (!$nextClass) {
            return redirect()->route('admin.students_by_class', ['class_name' => urlencode($currentClass->name), 'action' => $action])
                ->with('error', 'No higher class available for promotion.');
        }

        try {
            $currentHistory = StudentClassHistory::where('student_id', $studentId)
                ->where('session_id', $currentSession->id)
                ->where('class_id', $currentClass->id)
                ->where('is_active', true)
                ->whereNull('leave_date')
                ->first();

            if ($currentHistory) {
                $currentHistory->update([
                    'is_active' => false,
                    'leave_date' => Carbon::now('Africa/Lagos'),
                    'end_term' => $currentTerm->value,
                ]);
            }

            StudentClassHistory::create([
                'student_id' => $student->id,
                'session_id' => $currentSession->id,
                'class_id' => $nextClass->id,
                'start_term' => $currentTerm->value,
                'join_date' => Carbon::now('Africa/Lagos'),
                'is_active' => true,
            ]);

            $this->logActivity("Promoted student {$student->reg_no} to {$nextClass->name}", [
                'student_id' => $student->id,
                'new_class_id' => $nextClass->id
            ]);

            return redirect()->route('admin.students_by_class', ['class_name' => urlencode($currentClass->name), 'action' => $action])
                ->with('success', "Student promoted to {$nextClass->name} successfully!");
        } catch (\Exception $e) {
            Log::error("Error promoting student {$studentId}: " . $e->getMessage());
            return redirect()->route('admin.students_by_class', ['class_name' => urlencode($currentClass->name), 'action' => $action])
                ->with('error', 'Error promoting student.');
        }
    }

    public function demoteStudent(Request $request, string $className, $studentId, string $action)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        $student = Student::findOrFail($studentId);
        $currentClass = Classes::where('name', urldecode($className))->firstOrFail();
        $previousClass = Classes::where('hierarchy', $currentClass->hierarchy - 1)->first();

        if (!$previousClass) {
            return redirect()->route('admin.students_by_class', ['class_name' => urlencode($currentClass->name), 'action' => $action])
                ->with('error', 'No lower class available for demotion.');
        }

        try {
            $currentHistory = StudentClassHistory::where('student_id', $studentId)
                ->where('session_id', $currentSession->id)
                ->where('class_id', $currentClass->id)
                ->where('is_active', true)
                ->whereNull('leave_date')
                ->first();

            if ($currentHistory) {
                $currentHistory->update([
                    'is_active' => false,
                    'leave_date' => Carbon::now('Africa/Lagos'),
                    'end_term' => $currentTerm->value,
                ]);
            }

            StudentClassHistory::create([
                'student_id' => $student->id,
                'session_id' => $currentSession->id,
                'class_id' => $previousClass->id,
                'start_term' => $currentTerm->value,
                'join_date' => Carbon::now('Africa/Lagos'),
                'is_active' => true,
            ]);

            $this->logActivity("Demoted student {$student->reg_no} to {$previousClass->name}", [
                'student_id' => $student->id,
                'new_class_id' => $previousClass->id
            ]);

            return redirect()->route('admin.students_by_class', ['class_name' => urlencode($currentClass->name), 'action' => $action])
                ->with('success', "Student demoted to {$previousClass->name} successfully!");
        } catch (\Exception $e) {
            Log::error("Error demoting student {$studentId}: " . $e->getMessage());
            return redirect()->route('admin.students_by_class', ['class_name' => urlencode($currentClass->name), 'action' => $action])
                ->with('error', 'Error demoting student.');
        }
    }

    public function deleteStudentClassRecord(Request $request, string $className, $studentId, string $action)
    {
        $student = Student::findOrFail($studentId);
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];
        $class = Classes::where('name', urldecode($className))->firstOrFail();

        try {
            $history = StudentClassHistory::where('student_id', $studentId)
                ->where('session_id', $currentSession->id)
                ->where('class_id', $class->id)
                ->where('is_active', true)
                ->whereNull('leave_date')
                ->first();

            if ($history) {
                $history->update([
                    'is_active' => false,
                    'leave_date' => Carbon::now('Africa/Lagos'),
                    'end_term' => $currentTerm->value,
                ]);

                $this->logActivity("Deleted class record for student {$student->reg_no} in {$class->name}", [
                    'student_id' => $student->id,
                    'class_id' => $class->id
                ]);

                return redirect()->route('admin.students_by_class', ['class_name' => urlencode($class->name), 'action' => $action])
                    ->with('success', 'Student class record deleted successfully!');
            }

            return redirect()->route('admin.students_by_class', ['class_name' => urlencode($class->name), 'action' => $action])
                ->with('error', 'No active class record found for this student.');
        } catch (\Exception $e) {
            Log::error("Error deleting class record for student {$studentId}: " . $e->getMessage());
            return redirect()->route('admin.students_by_class', ['class_name' => urlencode($class->name), 'action' => $action])
                ->with('error', 'Error deleting class record.');
        }
    }

    public function searchStudentsByClass(Request $request, string $className, string $action)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];
        $class = Classes::where('name', urldecode($className))->firstOrFail();

        $query = $request->input('query', '');
        $studentsQuery = $this->getStudentsQuery($currentSession, $currentTerm->value)
            ->where('classes.id', $class->id);

        if ($query) {
            $studentsQuery->where(function ($q) use ($query) {
                $q->whereRaw('LOWER(students.first_name) LIKE ?', ['%' . strtolower($query) . '%'])
                  ->orWhereRaw('LOWER(students.last_name) LIKE ?', ['%' . strtolower($query) . '%'])
                  ->orWhere('students.reg_no', 'LIKE', '%' . $query . '%');
            });
        }

        $students = $studentsQuery->orderBy('students.first_name')->get();
        $studentsClasses = $this->groupStudentsByClass($students);

        return view('admin.classes.search_results', compact('studentsClasses', 'class', 'action', 'currentSession', 'currentTerm'));
    }
}
