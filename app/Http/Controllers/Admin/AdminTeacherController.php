<?php

namespace App\Http\Controllers\Admin;

use App\Models\Teacher;
use App\Models\User;
use App\Models\Classes;
use App\Models\Subject;
use App\Enums\TermEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class AdminTeacherController extends AdminBaseController
{
    public function manageTeachers(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'first_name' => 'required|string|max:50',
                    'middle_name' => 'nullable|string|max:50',
                    'last_name' => 'required|string|max:50',
                    'email' => 'required|email|unique:users,email',
                    'username' => 'required|string|unique:users,username|min:3|max:50',
                    'password' => 'required|string|min:8|confirmed',
                ]);

                $user = User::create([
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'username' => $validated['username'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'teacher',
                    'active' => true,
                ]);

                $user->assignRole('teacher');

                $teacher = Teacher::create([
                    'user_id' => $user->id,
                    // Add other teacher-specific fields if needed
                ]);

                $this->logActivity("Created teacher: {$user->username}", [
                    'teacher_id' => $teacher->id,
                    'email' => $user->email
                ]);

                return back()->with('success', "Teacher {$user->username} created successfully!");

            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            } catch (QueryException $e) {
                Log::error("Integrity error creating teacher: " . $e->getMessage());
                return back()->with('error', 'Creation failed: Duplicate data.');
            } catch (\Exception $e) {
                Log::error("Database error creating teacher: " . $e->getMessage());
                return back()->with('error', 'Database error occurred.');
            }
        }

        $teachers = Teacher::with('user')->orderBy('id')->paginate(10);

        return view('admin.teachers.manage_teachers', compact('teachers'));
    }

    public function edit(Request $request, $teacherId)
    {
        $teacher = Teacher::findOrFail($teacherId);
        $user = $teacher->user;

        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'first_name' => 'required|string|max:50',
                    'middle_name' => 'nullable|string|max:50',
                    'last_name' => 'required|string|max:50',
                    'email' => 'required|email|unique:users,email,' . $user->id,
                    'username' => 'required|string|unique:users,username,' . $user->id . '|min:3|max:50',
                    'password' => 'nullable|string|min:8|confirmed',
                    'active' => 'required|boolean',
                ]);

                $updateData = [
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'username' => $validated['username'],
                    'active' => $validated['active'],
                ];

                if ($validated['password']) {
                    $updateData['password'] = Hash::make($validated['password']);
                }

                $user->update($updateData);

                $this->logActivity("Updated teacher: {$user->username}", [
                    'teacher_id' => $teacher->id,
                    'email' => $user->email
                ]);

                return back()->with('success', 'Teacher updated successfully!');

            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                Log::error("Database error updating teacher {$teacherId}: " . $e->getMessage());
                return back()->with('error', 'Database error occurred.');
            }
        }

        return view('admin.teachers.edit_teacher', compact('teacher', 'user'));
    }

    public function destroy(Request $request, $teacherId)
    {
        $teacher = Teacher::findOrFail($teacherId);
        $user = $teacher->user;

        try {
            $username = $user->username;
            $teacher->delete();
            $user->delete();

            $this->logActivity("Deleted teacher: {$username}", ['teacher_id' => $teacherId]);

            return back()->with('success', 'Teacher deleted successfully!');

        } catch (\Exception $e) {
            Log::error("Database error deleting teacher {$teacherId}: " . $e->getMessage());
            return back()->with('error', 'Database error occurred.');
        }
    }

    public function assignSubjectToTeacher(Request $request)
    {
        try {
            $validated = $request->validate([
                'teacher_id' => 'required|exists:teachers,id',
                'subject_id' => 'required|exists:subjects,id',
            ]);

            $teacher = Teacher::findOrFail($validated['teacher_id']);
            $subject = Subject::findOrFail($validated['subject_id']);

            $teacher->subjects()->syncWithoutDetaching([$validated['subject_id']]);

            $this->logActivity("Assigned subject {$subject->name} to teacher {$teacher->user->username}", [
                'teacher_id' => $teacher->id,
                'subject_id' => $subject->id
            ]);

            return back()->with('success', "Subject {$subject->name} assigned to teacher successfully!");

        } catch (\Exception $e) {
            Log::error("Error assigning subject to teacher: " . $e->getMessage());
            return back()->with('error', 'Error assigning subject.');
        }
    }

    public function assignTeacherToClass(Request $request)
    {
        try {
            $validated = $request->validate([
                'teacher_id' => 'required|exists:teachers,id',
                'class_id' => 'required|exists:classes,id',
            ]);

            $teacher = Teacher::findOrFail($validated['teacher_id']);
            $class = Classes::findOrFail($validated['class_id']);

            $teacher->classes()->syncWithoutDetaching([$validated['class_id']]);

            $this->logActivity("Assigned teacher {$teacher->user->username} to class {$class->name}", [
                'teacher_id' => $teacher->id,
                'class_id' => $class->id
            ]);

            return back()->with('success', "Teacher assigned to class {$class->name} successfully!");

        } catch (\Exception $e) {
            Log::error("Error assigning teacher to class: " . $e->getMessage());
            return back()->with('error', 'Error assigning teacher.');
        }
    }

    public function removeTeacherFromClass(Request $request)
    {
        try {
            $validated = $request->validate([
                'teacher_id' => 'required|exists:teachers,id',
                'class_id' => 'required|exists:classes,id',
            ]);

            $teacher = Teacher::findOrFail($validated['teacher_id']);
            $class = Classes::findOrFail($validated['class_id']);

            $teacher->classes()->detach($validated['class_id']);

            $this->logActivity("Removed teacher {$teacher->user->username} from class {$class->name}", [
                'teacher_id' => $teacher->id,
                'class_id' => $class->id
            ]);

            return back()->with('success', "Teacher removed from class {$class->name} successfully!");

        } catch (\Exception $e) {
            Log::error("Error removing teacher from class: " . $e->getMessage());
            return back()->with('error', 'Error removing teacher.');
        }
    }
}
