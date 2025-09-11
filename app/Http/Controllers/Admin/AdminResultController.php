<?php

namespace App\Http\Controllers\Admin;

use App\Models\Result;
use App\Models\StudentTermSummary;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

class AdminResultController extends AdminBaseController
{
    public function manageResults(string $className, $studentId, string $action)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        $class = Classes::where('name', urldecode($className))->firstOrFail();
        $student = Student::findOrFail($studentId);
        $subjects = $class->subjects()->where('deactivated', false)->orderBy('name')->get();

        $results = Result::where('student_id', $studentId)
            ->where('session_id', $currentSession->id)
            ->where('term', $currentTerm->value)
            ->whereIn('subject_id', $subjects->pluck('id'))
            ->get()
            ->keyBy('subject_id');

        $termSummary = StudentTermSummary::where('student_id', $studentId)
            ->where('session_id', $currentSession->id)
            ->where('term', $currentTerm->value)
            ->first();

        return view('admin.results.manage_results', compact(
            'student',
            'class',
            'subjects',
            'results',
            'termSummary',
            'currentSession',
            'currentTerm',
            'action'
        ));
    }

    public function updateResult(Request $request, string $className, $studentId, string $action)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        $class = Classes::where('name', urldecode($className))->firstOrFail();
        $student = Student::findOrFail($studentId);

        try {
            $validated = $request->validate([
                'results' => 'required|array',
                'results.*.subject_id' => 'required|exists:subjects,id',
                'results.*.ca1' => 'nullable|numeric|min:0|max:20',
                'results.*.ca2' => 'nullable|numeric|min:0|max:20',
                'results.*.exam' => 'nullable|numeric|min:0|max:60',
            ]);

            DB::transaction(function () use ($validated, $studentId, $currentSession, $currentTerm, $class) {
                $totalScore = 0;
                $subjectCount = 0;

                foreach ($validated['results'] as $resultData) {
                    $total = ($resultData['ca1'] ?? 0) + ($resultData['ca2'] ?? 0) + ($resultData['exam'] ?? 0);
                    $grade = $this->calculateGrade($total);
                    $remark = $this->generateRemark($total);

                    Result::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'session_id' => $currentSession->id,
                            'term' => $currentTerm->value,
                            'subject_id' => $resultData['subject_id'],
                        ],
                        [
                            'ca1' => $resultData['ca1'] ?? null,
                            'ca2' => $resultData['ca2'] ?? null,
                            'exam' => $resultData['exam'] ?? null,
                            'total' => $total,
                            'grade' => $grade,
                            'remark' => $remark,
                        ]
                    );

                    $totalScore += $total;
                    $subjectCount++;
                }

                $average = $subjectCount > 0 ? $totalScore / $subjectCount : 0;
                $threshold = $this->getThreshold($class->hierarchy);
                $principalRemark = $this->generatePrincipalRemark($average, $threshold);

                StudentTermSummary::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'session_id' => $currentSession->id,
                        'term' => $currentTerm->value,
                    ],
                    [
                        'total_score' => $totalScore,
                        'average_score' => $average,
                        'principal_remark' => $principalRemark,
                    ]
                );

                $this->logActivity("Updated results for student {$studentId}", [
                    'student_id' => $studentId,
                    'term' => $currentTerm->value
                ]);
            });

            return back()->with('success', 'Results updated successfully!');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error updating results for student {$studentId}: " . $e->getMessage());
            return back()->with('error', 'Error updating results.');
        }
    }

    public function updateResultField(Request $request)
    {
        try {
            $validated = $request->validate([
                'result_id' => 'required|exists:results,id',
                'field' => 'required|in:ca1,ca2,exam',
                'value' => 'nullable|numeric|min:0|max:' . ($request->field === 'exam' ? 60 : 20),
            ]);

            $result = Result::findOrFail($validated['result_id']);
            $result->update([
                $validated['field'] => $validated['value'],
                'total' => ($result->ca1 ?? 0) + ($result->ca2 ?? 0) + ($result->exam ?? 0),
            ]);

            $result->grade = $this->calculateGrade($result->total);
            $result->remark = $this->generateRemark($result->total);
            $result->save();

            $this->logActivity("Updated result field {$validated['field']} for result {$result->id}", [
                'result_id' => $result->id,
                'field' => $validated['field'],
                'value' => $validated['value']
            ]);

            return response()->json(['success' => true, 'message' => 'Result field updated successfully']);

        } catch (\Exception $e) {
            Log::error("Error updating result field: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating result field'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function broadsheet(Request $request, string $className, string $action)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        $class = Classes::where('name', urldecode($className))->firstOrFail();
        $students = $this->getStudentsQuery($currentSession, $currentTerm->value)
            ->where('classes.id', $class->id)
            ->get();

        $subjects = $class->subjects()->where('deactivated', false)->orderBy('name')->get();

        $broadsheetData = $students->map(function ($student) use ($currentSession, $currentTerm, $subjects) {
            $results = Result::where('student_id', $student->id)
                ->where('session_id', $currentSession->id)
                ->where('term', $currentTerm->value)
                ->whereIn('subject_id', $subjects->pluck('id'))
                ->get()
                ->keyBy('subject_id');

            $termSummary = StudentTermSummary::where('student_id', $student->id)
                ->where('session_id', $currentSession->id)
                ->where('term', $currentTerm->value)
                ->first();

            return [
                'student' => $student,
                'results' => $results,
                'termSummary' => $termSummary,
            ];
        });

        return view('admin.results.broadsheet', compact(
            'class',
            'subjects',
            'broadsheetData',
            'currentSession',
            'currentTerm',
            'action'
        ));
    }

    public function updateBroadsheet(Request $request, string $className, string $action)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        $class = Classes::where('name', urldecode($className))->firstOrFail();

        try {
            $validated = $request->validate([
                'results' => 'required|array',
                'results.*.student_id' => 'required|exists:students,id',
                'results.*.subjects' => 'required|array',
                'results.*.subjects.*.subject_id' => 'required|exists:subjects,id',
                'results.*.subjects.*.ca1' => 'nullable|numeric|min:0|max:20',
                'results.*.subjects.*.ca2' => 'nullable|numeric|min:0|max:20',
                'results.*.subjects.*.exam' => 'nullable|numeric|min:0|max:60',
            ]);

            DB::transaction(function () use ($validated, $currentSession, $currentTerm, $class) {
                foreach ($validated['results'] as $studentResult) {
                    $studentId = $studentResult['student_id'];
                    $totalScore = 0;
                    $subjectCount = 0;

                    foreach ($studentResult['subjects'] as $resultData) {
                        $total = ($resultData['ca1'] ?? 0) + ($resultData['ca2'] ?? 0) + ($resultData['exam'] ?? 0);
                        $grade = $this->calculateGrade($total);
                        $remark = $this->generateRemark($total);

                        Result::updateOrCreate(
                            [
                                'student_id' => $studentId,
                                'session_id' => $currentSession->id,
                                'term' => $currentTerm->value,
                                'subject_id' => $resultData['subject_id'],
                            ],
                            [
                                'ca1' => $resultData['ca1'] ?? null,
                                'ca2' => $resultData['ca2'] ?? null,
                                'exam' => $resultData['exam'] ?? null,
                                'total' => $total,
                                'grade' => $grade,
                                'remark' => $remark,
                            ]
                        );

                        $totalScore += $total;
                        $subjectCount++;
                    }

                    $average = $subjectCount > 0 ? $totalScore / $subjectCount : 0;
                    $threshold = $this->getThreshold($class->hierarchy);
                    $principalRemark = $this->generatePrincipalRemark($average, $threshold);

                    StudentTermSummary::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'session_id' => $currentSession->id,
                            'term' => $currentTerm->value,
                        ],
                        [
                            'total_score' => $totalScore,
                            'average_score' => $average,
                            'principal_remark' => $principalRemark,
                        ]
                    );

                    $this->logActivity("Updated broadsheet results for student {$studentId}", [
                        'student_id' => $studentId,
                        'term' => $currentTerm->value
                    ]);
                }
            });

            return back()->with('success', 'Broadsheet updated successfully!');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error updating broadsheet for class {$className}: " . $e->getMessage());
            return back()->with('error', 'Error updating broadsheet.');
        }
    }

    public function downloadBroadsheet(string $className, string $action)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        $class = Classes::where('name', urldecode($className))->firstOrFail();
        $students = $this->getStudentsQuery($currentSession, $currentTerm->value)
            ->where('classes.id', $class->id)
            ->get();

        $subjects = $class->subjects()->where('deactivated', false)->orderBy('name')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("{$class->name} Broadsheet");

        // Headers
        $sheet->setCellValue('A1', 'Reg No');
        $sheet->setCellValue('B1', 'Student Name');
        $column = 'C';
        foreach ($subjects as $subject) {
            $sheet->setCellValue($column . '1', $subject->name);
            $column++;
        }
        $sheet->setCellValue($column . '1', 'Total');
        $column++;
        $sheet->setCellValue($column . '1', 'Average');
        $column++;
        $sheet->setCellValue($column . '1', 'Remark');

        // Data
        $row = 2;
        foreach ($students as $student) {
            $results = Result::where('student_id', $student->id)
                ->where('session_id', $currentSession->id)
                ->where('term', $currentTerm->value)
                ->whereIn('subject_id', $subjects->pluck('id'))
                ->get()
                ->keyBy('subject_id');

            $termSummary = StudentTermSummary::where('student_id', $student->id)
                ->where('session_id', $currentSession->id)
                ->where('term', $currentTerm->value)
                ->first();

            $sheet->setCellValue('A' . $row, $student->reg_no);
            $sheet->setCellValue('B' . $row, $student->first_name . ' ' . $student->last_name);
            $column = 'C';
            foreach ($subjects as $subject) {
                $result = $results->get($subject->id);
                $sheet->setCellValue($column . $row, $result ? $result->total : '-');
                $column++;
            }
            $sheet->setCellValue($column . $row, $termSummary ? $termSummary->total_score : '-');
            $column++;
            $sheet->setCellValue($column . $row, $termSummary ? $termSummary->average_score : '-');
            $column++;
            $sheet->setCellValue($column . $row, $termSummary ? $termSummary->principal_remark : '-');
            $row++;
        }

        $this->logActivity("Downloaded broadsheet for class {$class->name}", [
            'class_id' => $class->id,
            'term' => $currentTerm->value
        ]);

        $writer = new Xlsx($spreadsheet);
        $filename = Str::slug("{$class->name}-broadsheet-{$currentSession->year}-{$currentTerm->value}.xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
