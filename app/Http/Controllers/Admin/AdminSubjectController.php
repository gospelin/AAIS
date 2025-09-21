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
                // Handle deactivation
                if ($request->has('deactivate_subject_id')) {
                    $validated = $request->validate([
                        'deactivate_subject_id' => 'required|exists:subjects,id',
                    ]);

                    $subject = Subject::findOrFail($validated['deactivate_subject_id']);
                    $subject->deactivated = true;
                    $subject->save();
                    $this->logActivity("Deactivated subject: {$subject->name}", ['subject_id' => $subject->id]);

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'message' => "Subject '{$subject->name}' deactivated.",
                        ]);
                    }

                    return back()->with('warning', "Subject '{$subject->name}' deactivated.");
                }

                // Handle deletion
                if ($request->has('delete_subject_id')) {
                    $validated = $request->validate([
                        'delete_subject_id' => 'required|exists:subjects,id',
                    ]);

                    $subject = Subject::findOrFail($validated['delete_subject_id']);
                    Result::where('subject_id', $subject->id)->delete();
                    $subjectName = $subject->name;
                    $subject->delete();
                    $this->logActivity("Deleted subject: {$subjectName}", ['subject_id' => $subject->id]);

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'message' => "Subject '{$subjectName}' and associated scores deleted successfully!"
                        ]);
                    }

                    return back()->with('success', "Subject '{$subjectName}' and associated scores deleted successfully!");
                }

                // Handle bulk creation
                $validated = $request->validate([
                    'names' => 'required|string',
                    'section' => 'required|array',
                    'section.*' => 'string|max:50',
                ]);

                $names = array_map('trim', explode(',', $validated['names']));
                $sections = $validated['section'];
                $createdSubjects = [];

                DB::transaction(function () use ($names, $sections, &$createdSubjects) {
                    foreach ($names as $name) {
                        if (empty($name))
                            continue;
                        foreach ($sections as $section) {
                            if (!Subject::where('name', $name)->where('section', $section)->where('deactivated', false)->exists()) {
                                $subject = Subject::create([
                                    'name' => $name,
                                    'section' => $section,
                                ]);
                                $createdSubjects[] = $subject;
                                $this->logActivity("Subject created: {$subject->name}", ['subject_id' => $subject->id]);
                            } else {
                                throw ValidationException::withMessages([
                                    'names' => "Subject '{$name}' already exists in section '{$section}'."
                                ]);
                            }
                        }
                    }
                });

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => count($createdSubjects) . ' subject(s) added successfully!',
                    ]);
                }

                return back()->with('success', count($createdSubjects) . ' subject(s) added successfully!');

            } catch (ValidationException $e) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $e->errors()['names'][0] ?? 'Validation error.'], 422);
                }
                return back()->with('warning', $e->errors()['names'][0] ?? 'Validation error.')->withInput();
            } catch (\Exception $e) {
                Log::error("Database error: " . $e->getMessage());
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Database error occurred.'], 500);
                }
                return back()->with('error', 'Database error occurred.');
            }
        }

        $subjects = Subject::orderBy('section')->orderBy('name')->get();
        return view('admin.subjects.manage_subjects', compact('subjects'));
    }

    public function edit(Request $request, Subject $subject)
    {
        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'name' => 'required|string|max:50',
                    'section' => 'required|string|max:50',
                    'deactivated' => 'boolean',
                ]);

                $newName = $validated['name'];
                $newSection = $validated['section'];
                $deactivated = $validated['deactivated'] ?? false;

                DB::transaction(function () use ($newName, $newSection, $deactivated, $subject) {
                    if (
                        Subject::where('name', $newName)
                            ->where('section', $newSection)
                            ->where('id', '!=', $subject->id)
                            ->where('deactivated', false)
                            ->exists()
                    ) {
                        throw ValidationException::withMessages([
                            'name' => "Subject '{$newName}' already exists in section '{$newSection}'."
                        ]);
                    }

                    $subject->update([
                        'name' => $newName,
                        'section' => $newSection,
                        'deactivated' => $deactivated,
                    ]);
                    $this->logActivity("Updated subject: {$newName} in section {$newSection}", ['subject_id' => $subject->id]);
                });

                return redirect()->route('admin.subjects.manage')->with('success', "Subject '{$newName}' updated successfully!");

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
        try {
            Result::where('subject_id', $subject->id)->delete();
            $subjectName = $subject->name;
            $subject->delete();
            $this->logActivity("Deleted subject: {$subjectName}", ['subject_id' => $subject->id]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Subject '{$subjectName}' and associated scores deleted successfully!"
                ]);
            }

            return redirect()->route('admin.subjects.manage')->with('success', "Subject '{$subjectName}' and associated scores deleted successfully!");

        } catch (\Exception $e) {
            Log::error("Database error deleting subject {$subject->id}: " . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Database error occurred.'], 500);
            }
            return back()->with('error', 'Database error occurred.');
        }
    }

    public function assignSubjectToClass(Request $request)
    {
        $classesWithSubjects = Classes::with('subjects')->orderBy('hierarchy')->orderBy('name')->get()->mapWithKeys(function ($class) {
            return [$class->name => $class->subjects];
        });

        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'subject_ids' => 'required|array',
                    'subject_ids.*' => 'exists:subjects,id',
                    'class_ids' => 'required|array',
                    'class_ids.*' => 'exists:classes,id',
                ]);

                $changesMade = false;
                DB::transaction(function () use ($validated, &$changesMade) {
                    foreach ($validated['class_ids'] as $classId) {
                        $class = Classes::findOrFail($classId);
                        foreach ($validated['subject_ids'] as $subjectId) {
                            if (!$class->subjects()->where('subject_id', $subjectId)->exists()) {
                                $class->subjects()->attach($subjectId);
                                $changesMade = true;
                                $subject = Subject::find($subjectId);
                                $this->logActivity("Assigned subject {$subject->name} to class {$class->name}", [
                                    'subject_id' => $subjectId,
                                    'class_id' => $class->id
                                ]);
                            }
                        }
                    }
                });

                $message = $changesMade
                    ? "Assigned " . count($validated['subject_ids']) . " subject(s) to " . count($validated['class_ids']) . " class(es) successfully!"
                    : "No new assignments were made; all selected subjects were already assigned.";

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message
                    ]);
                }

                return back()->with($changesMade ? 'success' : 'warning', $message);

            } catch (ValidationException $e) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'errors' => $e->errors()], 422);
                }
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                Log::error("Error assigning subjects to classes: " . $e->getMessage());
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Database error occurred.'], 500);
                }
                return back()->with('error', 'Database error occurred.');
            }
        }

        return view('admin.subjects.assign_subject_to_class', compact('classesWithSubjects'));
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

            if ($class->subjects()->where('subject_id', $validated['subject_id'])->exists()) {
                $class->subjects()->detach($validated['subject_id']);
                $this->logActivity("Removed subject {$subject->name} from class {$class->name}", [
                    'subject_id' => $validated['subject_id'],
                    'class_id' => $class->id
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Removed {$subject->name} from {$class->name} successfully."
                    ]);
                }

                return back()->with('success', "Removed {$subject->name} from {$class->name} successfully.");
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "{$subject->name} was not assigned to {$class->name}."
                ]);
            }

            return back()->with('warning', "{$subject->name} was not assigned to {$class->name}.");

        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error removing subject {$validated['subject_id']} from class {$validated['class_id']}: " . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Database error occurred.'], 500);
            }
            return back()->with('error', 'Database error occurred.');
        }
    }

    public function editSubjectAssignment(Request $request, string $className)
    {
        $class = Classes::where('name', urldecode($className))->firstOrFail();
        $subjects = Subject::orderBy('name')->get();
        $assignedSubjects = $class->subjects->pluck('id')->toArray();

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

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Updated subjects for {$class->name} successfully."
                    ]);
                }

                return redirect()->route('admin.subjects.assign')->with('success', "Updated subjects for {$class->name} successfully.");

            } catch (ValidationException $e) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'errors' => $e->errors()], 422);
                }
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                Log::error("Error updating subject assignments for class {$class->id}: " . $e->getMessage());
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Database error occurred.'], 500);
                }
                return back()->with('error', 'Database error occurred.');
            }
        }

        return view('admin.subjects.edit_subject_assignment', compact('class', 'subjects', 'assignedSubjects'));
    }

    public function getClassSubjects(Classes $class)
    {
        return response()->json([
            'subjects' => $class->subjects->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'section' => $subject->section,
                ];
            })
        ]);
    }
}
