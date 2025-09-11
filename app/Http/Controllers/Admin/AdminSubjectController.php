<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subject;
use App\Models\Classes;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AdminSubjectController extends AdminBaseController
{
    public function manageSubjects(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'name' => 'required|string|max:50|unique:subjects',
                    'code' => 'required|string|max:10|unique:subjects',
                    'description' => 'nullable|string|max:255',
                ]);

                Subject::create($validated);

                $this->logActivity("Subject created: {$validated['name']}", ['subject_id' => DB::getPdo()->lastInsertId()]);

                return back()->with('success', "Subject '{$validated['name']}' created successfully!");

            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                Log::error("Database error creating subject: " . $e->getMessage());
                return back()->with('error', 'Database error occurred.');
            }
        }

        $subjects = Subject::orderBy('name')->paginate(10);

        return view('admin.subjects.manage_subjects', compact('subjects'));
    }

    public function edit(Request $request, Subject $subject)
    {
        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'name' => 'required|string|max:50|unique:subjects,name,' . $subject->id,
                    'code' => 'required|string|max:10|unique:subjects,code,' . $subject->id,
                    'description' => 'nullable|string|max:255',
                    'deactivated' => 'boolean',
                ]);

                $subject->update($validated);

                $this->logActivity("Updated subject: {$subject->name}", ['subject_id' => $subject->id]);

                return back()->with('success', 'Subject updated successfully!');

            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                Log::error("Database error updating subject {$subject->id}: " . $e->getMessage());
                return back()->with('error', 'Database error occurred.');
            }
        }

        return view('admin.subjects.edit_subject', compact('subject'));
    }

    public function destroy(Request $request, Subject $subject)
    {
        $results = Result::where('subject_id', $subject->id)->count();

        if ($results > 0) {
            return back()->with('error', 'Cannot delete subject with associated results.');
        }

        try {
            $subjectName = $subject->name;
            $subject->delete();

            $this->logActivity("Deleted subject: {$subjectName}", ['subject_id' => $subject->id]);

            return back()->with('success', 'Subject deleted successfully!');

        } catch (\Exception $e) {
            Log::error("Database error deleting subject {$subject->id}: " . $e->getMessage());
            return back()->with('error', 'Database error occurred.');
        }
    }

    public function assignSubjectToClass(Request $request)
    {
        try {
            $validated = $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'class_id' => 'required|exists:classes,id',
            ]);

            $class = Classes::findOrFail($validated['class_id']);
            $subject = Subject::findOrFail($validated['subject_id']);

            $class->subjects()->syncWithoutDetaching([$validated['subject_id']]);

            $this->logActivity("Assigned subject {$subject->name} to class {$class->name}", [
                'subject_id' => $subject->id,
                'class_id' => $class->id
            ]);

            return back()->with('success', "Subject {$subject->name} assigned to {$class->name} successfully!");

        } catch (\Exception $e) {
            Log::error("Error assigning subject to class: " . $e->getMessage());
            return back()->with('error', 'Error assigning subject.');
        }
    }

    public function removeSubjectFromClass(Request $request)
    {
        try {
            $validated = $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'class_id' => 'required|exists:classes,id',
            ]);

            $class = Classes::findOrFail($validated['class_id']);
            $subject = Subject::findOrFail($validated['subject_id']);

            $class->subjects()->detach($validated['subject_id']);

            $this->logActivity("Removed subject {$subject->name} from class {$class->name}", [
                'subject_id' => $subject->id,
                'class_id' => $class->id
            ]);

            return back()->with('success', "Subject {$subject->name} removed from {$class->name} successfully!");

        } catch (\Exception $e) {
            Log::error("Error removing subject from class: " . $e->getMessage());
            return back()->with('error', 'Error removing subject.');
        }
    }

    public function editSubjectAssignment(Request $request, string $className)
    {
        $class = Classes::where('name', urldecode($className))->firstOrFail();
        $subjects = Subject::orderBy('name')->get();

        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'subject_ids' => 'nullable|array',
                    'subject_ids.*' => 'exists:subjects,id',
                ]);

                $class->subjects()->sync($validated['subject_ids'] ?? []);

                $this->logActivity("Updated subject assignments for class {$class->name}", [
                    'class_id' => $class->id,
                    'subject_ids' => $validated['subject_ids'] ?? []
                ]);

                return back()->with('success', 'Subject assignments updated successfully!');

            } catch (\Exception $e) {
                Log::error("Error updating subject assignments for class {$class->id}: " . $e->getMessage());
                return back()->with('error', 'Error updating subject assignments.');
            }
        }

        $assignedSubjects = $class->subjects->pluck('id')->toArray();

        return view('admin.subjects.edit_subject_assignment', compact('class', 'subjects', 'assignedSubjects'));
    }

    public function mergeSubjects(Request $request)
    {
        $fullyCommonSubjects = ['Mathematics', 'English Language'];

        try {
            $validated = $request->validate([
                'source_subject_id' => 'required|exists:subjects,id',
                'target_subject_id' => 'required|exists:subjects,id|different:source_subject_id',
            ]);

            $sourceSubject = Subject::findOrFail($validated['source_subject_id']);
            $targetSubject = Subject::findOrFail($validated['target_subject_id']);

            if (in_array($sourceSubject->name, $fullyCommonSubjects) || in_array($targetSubject->name, $fullyCommonSubjects)) {
                return back()->with('error', 'Cannot merge common subjects like Mathematics or English Language.');
            }

            DB::transaction(function () use ($sourceSubject, $targetSubject) {
                Result::where('subject_id', $sourceSubject->id)
                    ->update(['subject_id' => $targetSubject->id]);

                $targetSubject->classes()->syncWithoutDetaching($sourceSubject->classes->pluck('id')->toArray());
                $sourceSubject->classes()->detach();

                $sourceSubject->delete();

                $this->logActivity("Merged subject {$sourceSubject->name} into {$targetSubject->name}", [
                    'source_subject_id' => $sourceSubject->id,
                    'target_subject_id' => $targetSubject->id
                ]);
            });

            return back()->with('success', "Subject {$sourceSubject->name} merged into {$targetSubject->name} successfully!");

        } catch (\Exception $e) {
            Log::error("Error merging subjects: " . $e->getMessage());
            return back()->with('error', 'Error merging subjects.');
        }
    }
}
