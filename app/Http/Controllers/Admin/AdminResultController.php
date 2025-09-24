<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\ManageResultsRequest;
use App\Http\Requests\UpdateResultFieldRequest;
use App\Models\AcademicSession;
use App\Models\Classes;
use App\Models\Result;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Models\StudentTermSummary;
use App\Models\Subject;
use App\Enums\TermEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Notifications\ResultNotification;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

class AdminResultController extends AdminBaseController
{
    /**
     * Display the broadsheet for a class.
     */
    public function broadsheet(Request $request, string $className)
    {
        try {
            // Initialize session and term
            $currentSession = null;
            $currentTerm = null;
            $sessionId = $request->query('session_id');
            $termValue = $request->query('term');

            if ($sessionId) {
                $currentSession = AcademicSession::find($sessionId);
            }
            if ($termValue && in_array($termValue, array_column(TermEnum::cases(), 'value'))) {
                $currentTerm = TermEnum::from($termValue);
            }

            if (!$currentSession || !$currentTerm) {
                [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            }

            if (!$currentSession || !$currentTerm) {
                return redirect()->route('admin.select_class', ['action' => $request->query('action', 'generate_broadsheet')])
                    ->with('error', 'No current session or term available.');
            }

            $class = Classes::where('name', urldecode($className))->firstOrFail();
            $students = $this->getStudentsQuery($currentSession, $currentTerm->value)
                ->where('classes.id', $class->id)
                ->get();

            $includeDeactivated = $currentSession->year === '2023/2024';
            $subjects = $this->getSubjectsByClassId($class->id, $includeDeactivated);

            $action = $request->query('action', 'generate_broadsheet');

            $broadsheetData = $students->map(function ($student) use ($currentSession, $currentTerm, $subjects, $class) {
                $results = Result::where([
                    'student_id' => $student->id,
                    'session_id' => $currentSession->id,
                    'term' => $currentTerm->value,
                    'class_id' => $class->id,
                ])->get()->keyBy('subject_id');

                $termSummary = StudentTermSummary::where([
                    'student_id' => $student->id,
                    'session_id' => $currentSession->id,
                    'term' => $currentTerm->value,
                    'class_id' => $class->id,
                ])->first();

                return [
                    'student' => $student,
                    'results' => $results,
                    'termSummary' => $termSummary,
                ];
            });

            $subjectAverages = $this->calculateSubjectAverages($class->id, $currentSession->id, $currentTerm->value, $subjects);

            return view('admin.results.broadsheet', [
                'class' => $class,
                'subjects' => $subjects,
                'broadsheetData' => $broadsheetData,
                'subjectAverages' => $subjectAverages,
                'currentSession' => $currentSession,
                'currentTerm' => $currentTerm,
                'action' => $action,
                'school_name' => config('app.name', 'Aunty Anne\'s International School'),
            ]);
        } catch (\Exception $e) {
            Log::error("Error in broadsheet: {$e->getMessage()}", [
                'class_name' => $className,
                'exception' => $e->getTraceAsString(),
            ]);
            return redirect()->route('admin.select_class', ['action' => $request->query('action', 'generate_broadsheet')])
                ->with('error', 'Error loading broadsheet.');
        }
    }

    /**
     * Update broadsheet results via form submission.
     */
    public function updateBroadsheet(Request $request, string $className)
    {
        try {
            // Initialize session and term
            $currentSession = null;
            $currentTerm = null;
            $sessionId = $request->query('session_id');
            $termValue = $request->query('term');

            if ($sessionId) {
                $currentSession = AcademicSession::find($sessionId);
            }
            if ($termValue && in_array($termValue, array_column(TermEnum::cases(), 'value'))) {
                $currentTerm = TermEnum::from($termValue);
            }

            if (!$currentSession || !$currentTerm) {
                [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            }

            if (!$currentSession || !$currentTerm) {
                return redirect()->route('admin.select_class', ['action' => $request->query('action', 'generate_broadsheet')])
                    ->with('error', 'No current session or term available.');
            }

            $class = Classes::where('name', urldecode($className))->firstOrFail();

            // Validate the request data
            $validated = $request->validate([
                'results' => 'required|array',
                'results.*' => 'required|array',
                'results.*.*.class_assessment' => 'nullable|numeric|min:0|max:20',
                'results.*.*.summative_test' => 'nullable|numeric|min:0|max:20',
                'results.*.*.exam' => 'nullable|numeric|min:0|max:60',
                'results.*.position' => 'nullable|string|max:20',
                'next_term_begins' => 'nullable|string',
                'date_issued' => 'nullable|string',
            ]);

            DB::transaction(function () use ($validated, $currentSession, $currentTerm, $class) {
                $nextTermBegins = $validated['next_term_begins'] ?? null;
                $dateIssued = $validated['date_issued'] ?? null;

                foreach ($validated['results'] as $studentId => $studentData) {
                    // Verify student enrollment
                    $enrollment = StudentClassHistory::where([
                        'student_id' => $studentId,
                        'session_id' => $currentSession->id,
                        'class_id' => $class->id,
                        'is_active' => true,
                    ])->whereNull('leave_date')->first();

                    if (!$enrollment) {
                        Log::warning("Student {$studentId} not enrolled in class {$class->id} for session {$currentSession->id}");
                        continue;
                    }

                    $grandTotal = 0;
                    $subjectCount = 0;

                    // Process subject results
                    foreach ($studentData as $subjectId => $resultData) {
                        // Skip non-subject fields like 'position'
                        if (!is_numeric($subjectId)) {
                            continue;
                        }

                        $classAssessment = isset($resultData['class_assessment']) && $resultData['class_assessment'] !== '' ? (int)$resultData['class_assessment'] : null;
                        $summativeTest = isset($resultData['summative_test']) && $resultData['summative_test'] !== '' ? (int)$resultData['summative_test'] : null;
                        $exam = isset($resultData['exam']) && $resultData['exam'] !== '' ? (int)$resultData['exam'] : null;
                        $position = isset($studentData['position']) && $studentData['position'] !== '' ? $studentData['position'] : null;

                        $data = [
                            'student_id' => $studentId,
                            'subject_id' => $subjectId,
                            'class_id' => $class->id,
                            'session_id' => $currentSession->id,
                            'term' => $currentTerm->value,
                            'class_assessment' => $classAssessment,
                            'summative_test' => $summativeTest,
                            'exam' => $exam,
                            'next_term_begins' => $nextTermBegins,
                            'date_issued' => $dateIssued,
                            'position' => $position,
                        ];

                        $result = $this->saveResult($studentId, $subjectId, $currentTerm->value, $currentSession->id, $data, $class->id);

                        if ($result && $result->total !== null) {
                            $grandTotal += $result->total;
                            $subjectCount++;
                        }
                    }

                    // Update term summary
                    $termAverage = $subjectCount > 0 ? $grandTotal / $subjectCount : null;
                    $cumulativeAverage = $this->calculateCumulativeAverage($studentId, $currentTerm->value, $currentSession->id);
                    $lastTerm = $this->getLastTerm($currentTerm->value);
                    $lastTermResults = $lastTerm ? Result::where([
                        'student_id' => $studentId,
                        'term' => $lastTerm,
                        'session_id' => $currentSession->id,
                        'class_id' => $class->id,
                    ])->get() : [];
                    [$lastTermAverage] = $this->calculateAverage($lastTermResults);
                    $lastTermAverage = $lastTermAverage ? round($lastTermAverage, 1) : null;

                    $student = Student::findOrFail($studentId);
                    $principalRemark = $termAverage !== null ? $this->generatePrincipalRemark($termAverage, $student) : null;
                    $teacherRemark = $termAverage !== null ? $this->generateTeacherRemark($termAverage, $student) : null;

                    StudentTermSummary::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'session_id' => $currentSession->id,
                            'term' => $currentTerm->value,
                            'class_id' => $class->id,
                        ],
                        [
                            'grand_total' => $grandTotal > 0 ? $grandTotal : null,
                            'term_average' => $termAverage ? round($termAverage, 1) : null,
                            'cumulative_average' => $cumulativeAverage ? round($cumulativeAverage, 1) : null,
                            'last_term_average' => $lastTermAverage,
                            'subjects_offered' => $subjectCount,
                            'principal_remark' => $principalRemark,
                            'teacher_remark' => $teacherRemark,
                            'next_term_begins' => $nextTermBegins,
                            'date_issued' => $dateIssued,
                            'position' => $position,
                        ]
                    );

                    $this->logActivity("Updated broadsheet results for student {$studentId}", [
                        'student_id' => $studentId,
                        'term' => $currentTerm->value,
                        'class_id' => $class->id,
                    ]);
                }
            });

            return redirect()->route('admin.broadsheet', [
                'className' => $className,
                'action' => $request->query('action', 'generate_broadsheet'),
                'session_id' => $currentSession->id,
                'term' => $currentTerm->value,
            ])->with('success', 'Broadsheet updated successfully!');

        } catch (ValidationException $e) {
            Log::error("Validation error in updateBroadsheet: " . json_encode($e->errors()), [
                'class_name' => $className,
                'request_data' => $request->all(),
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error updating broadsheet for class {$className}: {$e->getMessage()}", [
                'class_name' => $className,
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Error updating broadsheet.');
        }
    }

    /**
     * Display the manage results page for a student in a specific class.
     */
    public function manageResults(string $className, $studentId, string $action)
    {
        try {
            // Initialize session and term
            $currentSession = null;
            $currentTerm = null;

            // Check for query parameters first
            $sessionId = request()->query('session_id');
            $termValue = request()->query('term');

            if ($sessionId) {
                $currentSession = AcademicSession::find($sessionId);
            }
            if ($termValue && in_array($termValue, array_column(TermEnum::cases(), 'value'))) {
                $currentTerm = TermEnum::from($termValue);
            }

            // Fall back to getCurrentSessionAndTerm if query parameters are missing or invalid
            if (!$currentSession || !$currentTerm) {
                [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            }

            // Redirect if no valid session or term
            if (!$currentSession || !$currentTerm) {
                return redirect()->route('admin.students_by_class', [
                    'className' => $className,
                    'action' => $action,
                    'session_id' => $sessionId,
                    'term' => $termValue
                ])->with('error', 'No current session or term available.');
            }

            $class = Classes::where('name', urldecode($className))->firstOrFail();
            $student = Student::findOrFail($studentId);

            // Verify enrollment
            $enrollment = StudentClassHistory::where([
                'student_id' => $student->id,
                'session_id' => $currentSession->id,
                'class_id' => $class->id,
                'is_active' => true,
            ])->whereNull('leave_date')->first();

            if (!$enrollment) {
                return redirect()->route('admin.students_by_class', [
                    'className' => $className,
                    'action' => $action,
                ])->with('error', 'Student not enrolled in this class.');
            }

            // Get subjects (include deactivated for 2023/2024 session)
            $includeDeactivated = $currentSession->year === '2023/2024';
            $subjects = $this->getSubjectsByClassId($class->id, $includeDeactivated);

            // Fetch results
            $results = Result::where([
                'student_id' => $studentId,
                'session_id' => $currentSession->id,
                'term' => $currentTerm->value,
                'class_id' => $class->id,
            ])->get()->keyBy('subject_id');

            $termSummary = StudentTermSummary::where([
                'student_id' => $studentId,
                'session_id' => $currentSession->id,
                'term' => $currentTerm->value,
                'class_id' => $class->id,
            ])->first();

            return view('admin.results.manage_results', [
                'student' => $student,
                'class' => $class,
                'subjects' => $subjects,
                'results' => $results,
                'termSummary' => $termSummary,
                'currentSession' => $currentSession,
                'currentTerm' => $currentTerm,
                'action' => $action,
                'school_name' => config('app.school_name', 'Aunty Anne\'s International School'),
            ]);
        } catch (\Exception $e) {
            Log::error("Error in manageResults: {$e->getMessage()}");
            return redirect()->route('admin.students_by_class', ['className' => $className, 'action' => $action])
                ->with('error', 'Error loading results page.');
        }
    }

    /**
     * Update student results via form submission.
     */
    public function updateResult(ManageResultsRequest $request, string $className, $studentId, string $action)
    {
        try {
            // Initialize session and term
            $currentSession = null;
            $currentTerm = null;

            // Check for query parameters first
            $sessionId = request()->query('session_id');
            $termValue = request()->query('term');

            if ($sessionId) {
                $currentSession = AcademicSession::find($sessionId);
            }
            if ($termValue && in_array($termValue, array_column(TermEnum::cases(), 'value'))) {
                $currentTerm = TermEnum::from($termValue);
            }

            // Fall back to getCurrentSessionAndTerm if parameters are missing or invalid
            if (!$currentSession || !$currentTerm) {
                [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            }

            if (!$currentSession || !$currentTerm) {
                return redirect()->route('admin.students_by_class', [
                    'className' => $className,
                    'action' => $action,
                    'session_id' => $sessionId,
                    'term' => $termValue
                ])->with('error', 'No current session or term available.');
            }

            $class = Classes::where('name', urldecode($className))->firstOrFail();
            $student = Student::findOrFail($studentId);

            // Verify enrollment
            $enrollment = StudentClassHistory::where([
                'student_id' => $student->id,
                'session_id' => $currentSession->id,
                'class_id' => $class->id,
                'is_active' => true,
            ])->whereNull('leave_date')->first();

            if (!$enrollment) {
                return redirect()->route('admin.students_by_class', [
                    'className' => $className,
                    'action' => $action,
                ])->with('error', 'Student not enrolled in this class.');
            }

            DB::transaction(function () use ($request, $student, $currentSession, $currentTerm, $class) {
                $this->updateResultsHelper(
                    $student,
                    $currentTerm->value,
                    $currentSession->id,
                    $request->validated(),
                    $class->id
                );

                $this->logActivity("Updated results for student {$student->id}", [
                    'student_id' => $student->id,
                    'term' => $currentTerm->value,
                    'class_id' => $class->id,
                ]);
            });

            return redirect()->route('admin.manage_result', [
                'className' => $className,
                'studentId' => $studentId,
                'action' => $action,
            ])->with('success', 'Results updated successfully!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error updating results for student {$studentId}: {$e->getMessage()}");
            return back()->with('error', 'Error updating results.');
        }
    }

    /**
     * Update a specific result field via AJAX.
     */
    public function updateResultField(UpdateResultFieldRequest $request)
    {
        try {
            // Initialize session and term
            $currentSession = null;
            $currentTerm = null;

            // Check for query or input parameters first
            $sessionId = $request->input('session_id') ?? request()->query('session_id');
            $termValue = $request->input('term') ?? request()->query('term');

            if ($sessionId) {
                $currentSession = AcademicSession::find($sessionId);
            }
            if ($termValue && in_array($termValue, array_column(TermEnum::cases(), 'value'))) {
                $currentTerm = TermEnum::from($termValue);
            }

            // Fall back to getCurrentSessionAndTerm if parameters are missing or invalid
            if (!$currentSession || !$currentTerm) {
                [$currentSession, $currentTerm] = $this->getCurrentSessionAndTerm(true);
            }

            if (!$currentSession || !$currentTerm) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active session or term found.',
                ], 400);
            }

            $data = $request->validated();

            // Validate subject_id for non-class-wide fields
            $classWideFields = ['next_term_begins', 'date_issued', 'position'];
            if (!in_array($data['field'], $classWideFields) && (empty($data['subject_id']) || !is_numeric($data['subject_id']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Valid subject ID is required for this field.',
                ], 422);
            }

            // Map field to database column
            $fieldMap = [
                'class_assessment' => 'class_assessment',
                'summative_test' => 'summative_test',
                'exam' => 'exam',
                'next_term_begins' => 'next_term_begins',
                'date_issued' => 'date_issued',
                'position' => 'position',
            ];
            $data['field'] = $fieldMap[$data['field']] ?? $data['field'];

            $saveData = [
                'student_id' => $data['student_id'],
                'subject_id' => $data['subject_id'] ?? null,
                'class_id' => $data['class_id'],
                'session_id' => $currentSession->id,
                'term' => $currentTerm->value,
                $data['field'] => $data['value'],
                'next_term_begins' => $data['next_term_begins'] ?? null,
                'date_issued' => $data['date_issued'] ?? null,
                'position' => $data['position'] ?? null,
            ];

            $result = $this->saveResult(
                $data['student_id'],
                $data['subject_id'] ?? null,
                $currentTerm->value,
                $currentSession->id,
                $saveData,
                $data['class_id']
            );

            // Update class-wide fields if provided
            if (isset($data['next_term_begins']) || isset($data['date_issued']) || isset($data['position'])) {
                $this->saveClassWideFields(
                    $data['class_id'],
                    $currentSession->id,
                    $currentTerm->value,
                    $saveData
                );
            }

            $this->logActivity("Updated result field {$data['field']} for student {$data['student_id']}" . ($data['subject_id'] ? " in subject {$data['subject_id']}" : ""), [
                'student_id' => $data['student_id'],
                'subject_id' => $data['subject_id'] ?? null,
                'class_id' => $data['class_id'],
                'field' => $data['field'],
            ]);

            // Fetch updated term summary
            $termSummary = StudentTermSummary::where([
                'student_id' => $data['student_id'],
                'session_id' => $currentSession->id,
                'term' => $currentTerm->value,
                'class_id' => $data['class_id'],
            ])->first();

            return response()->json([
                'success' => true,
                'message' => 'Result saved successfully.',
                'result_id' => $result ? $result->id : null,
                'class_assessment' => $result ? $result->class_assessment : null,
                'summative_test' => $result ? $result->summative_test : null,
                'exam' => $result ? $result->exam : null,
                'total' => $result ? $result->total : null,
                'grade' => $result ? $result->grade : null,
                'remark' => $result ? $result->remark : null,
                'principal_remark' => $termSummary ? $termSummary->principal_remark : null,
                'teacher_remark' => $termSummary ? $termSummary->teacher_remark : null,
                'grand_total' => $termSummary ? $termSummary->grand_total : null,
                'term_average' => $termSummary ? $termSummary->term_average : null,
                'cumulative_average' => $termSummary ? $termSummary->cumulative_average : null,
                'subjects_offered' => $termSummary ? $termSummary->subjects_offered : null,
                'position' => $termSummary ? $termSummary->position : null,
                'next_term_begins' => $termSummary ? $termSummary->next_term_begins : null,
                'date_issued' => $termSummary ? $termSummary->date_issued : null,
            ]);
        } catch (ValidationException $e) {
            Log::error("Validation error in updateResultField: " . json_encode($e->errors()), [
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error in updateResultField: {$e->getMessage()}", [
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred.',
            ], 500);
        }
    }

    /**
     * Get subjects assigned to a class.
     */
    protected function getSubjectsByClassId($classId, $includeDeactivated = false)
    {
        try {
            $query = Subject::whereHas('classes', function ($q) use ($classId) {
                $q->where('class_subject.class_id', $classId);
            })->orderBy('name');

            if (!$includeDeactivated) {
                $query->where('deactivated', false);
            }

            return $query->get();
        } catch (\Exception $e) {
            Log::error("Error fetching subjects for class {$classId}: {$e->getMessage()}");
            return collect([]);
        }
    }

    /**
     * Populate form data with existing results (not used in current view).
     */
    protected function populateFormWithResults($subjects, $results)
    {
        $formData = [];
        foreach ($subjects as $subject) {
            $result = $results->get($subject->id);
            $formData[] = [
                'subject_id' => $subject->id,
                'class_assessment' => $result && $result->class_assessment !== null ? $result->class_assessment : '',
                'summative_test' => $result && $result->summative_test !== null ? $result->summative_test : '',
                'exam' => $result && $result->exam !== null ? $result->exam : '',
                'total' => $result && $result->total !== null ? $result->total : '',
                'grade' => $result && $result->grade ? $result->grade : '',
                'remark' => $result && $result->remark ? $result->remark : '',
            ];
        }
        return $formData;
    }

    /**
     * Get the previous term.
     */
    protected function getLastTerm($currentTerm)
    {
        $termOrder = [
            TermEnum::FIRST->value => 0,
            TermEnum::SECOND->value => 1,
            TermEnum::THIRD->value => 2,
        ];
        $currentIdx = $termOrder[$currentTerm] ?? null;
        if ($currentIdx === null || $currentIdx === 0) {
            return null;
        }
        return array_keys($termOrder)[$currentIdx - 1];
    }

    /**
     * Calculate average score and subject count.
     */
    protected function calculateAverage($results)
    {
        $uniqueResults = collect($results)->unique('subject_id')->values();
        $nonZeroResults = $uniqueResults->filter(fn($r) => ($r->total ?? 0) > 0)->pluck('total');
        $totalSum = $nonZeroResults->sum();
        $count = $nonZeroResults->count();
        return [$count > 0 ? $totalSum / $count : 0, $count];
    }

    /**
     * Calculate cumulative average.
     */
    protected function calculateCumulativeAverage($studentId, $term, $sessionId)
    {
        try {
            $termSummaries = StudentTermSummary::where([
                'student_id' => $studentId,
                'session_id' => $sessionId,
            ])->get();

            $termAverages = $termSummaries->mapWithKeys(function ($summary) {
                return [$summary->term->value => $summary->term_average];
            })->filter(fn($avg) => $avg !== null && $avg > 0)->toArray();

            if (empty($termAverages)) {
                return null;
            }

            $average = array_sum($termAverages) / count($termAverages);
            return round($average, 1);
        } catch (\Exception $e) {
            Log::error("Error calculating cumulative average for student {$studentId}: {$e->getMessage()}", [
                'student_id' => $studentId,
                'session_id' => $sessionId,
                'term' => $term,
                'exception' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Save a single result.
     */
    protected function saveResult($studentId, $subjectId, $term, $sessionId, $data, $classId)
    {
        DB::beginTransaction();
        try {
            $student = Student::findOrFail($studentId);
            // Retrieve existing result to preserve unchanged fields
            $existingResult = $subjectId ? Result::where([
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'term' => $term,
                'session_id' => $sessionId,
                'class_id' => $classId,
            ])->first() : null;

            // Use existing values if new ones are not provided
            $classAssessment = isset($data['class_assessment']) ? ($data['class_assessment'] !== '' ? (int)$data['class_assessment'] : null) : ($existingResult ? $existingResult->class_assessment : null);
            $summativeTest = isset($data['summative_test']) ? ($data['summative_test'] !== '' ? (int)$data['summative_test'] : null) : ($existingResult ? $existingResult->summative_test : null);
            $exam = isset($data['exam']) ? ($data['exam'] !== '' ? (int)$data['exam'] : null) : ($existingResult ? $existingResult->exam : null);
            $position = isset($data['position']) && $data['position'] !== '' ? $data['position'] : null;
            $nextTermBegins = isset($data['next_term_begins']) && $data['next_term_begins'] !== '' ? $data['next_term_begins'] : null;
            $dateIssued = isset($data['date_issued']) && $data['date_issued'] !== '' ? $data['date_issued'] : null;

            // Validate subject_id for result updates
            if ($subjectId && !is_numeric($subjectId)) {
                Log::warning("Invalid subject_id provided in saveResult: {$subjectId}", [
                    'student_id' => $studentId,
                    'term' => $term,
                    'session_id' => $sessionId,
                    'class_id' => $classId,
                    'data' => $data,
                ]);
                throw new \Exception('Invalid subject ID provided.');
            }

            $result = null;
            if ($subjectId) {
                // Use 0 for calculations if fields are null
                $calcClassAssessment = $classAssessment ?? 0;
                $calcSummativeTest = $summativeTest ?? 0;
                $calcExam = $exam ?? 0;
                $total = $calcClassAssessment + $calcSummativeTest + $calcExam;
                $grade = $total > 0 ? $this->calculateGrade($total) : null;
                $remark = $total > 0 ? $this->generateRemark($total) : null;

                // Only save if at least one field is non-null or total is non-zero
                if ($classAssessment !== null || $summativeTest !== null || $exam !== null) {
                    $result = Result::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'subject_id' => $subjectId,
                            'term' => $term,
                            'session_id' => $sessionId,
                            'class_id' => $classId,
                        ],
                        [
                            'class_assessment' => $classAssessment,
                            'summative_test' => $summativeTest,
                            'exam' => $exam,
                            'total' => $total > 0 ? $total : null,
                            'grade' => $grade,
                            'remark' => $remark,
                        ]
                    );
                } else {
                    // Delete result if all fields are null
                    Result::where([
                        'student_id' => $studentId,
                        'subject_id' => $subjectId,
                        'term' => $term,
                        'session_id' => $sessionId,
                        'class_id' => $classId,
                    ])->delete();
                }
            }

            // Update term summary
            [$grandTotal, $average, $cumulativeAverage, $lastTermAverage, $subjectsOffered] = $this->calculateResults($studentId, $term, $sessionId, $classId);
            $newThreshold = $this->getThresholdValue($average);

            $termSummary = StudentTermSummary::where([
                'student_id' => $studentId,
                'term' => $term,
                'session_id' => $sessionId,
                'class_id' => $classId,
            ])->first();

            $principalRemark = $average !== null ? $this->generatePrincipalRemark($average, $student) : null;
            $teacherRemark = $average !== null ? $this->generateTeacherRemark($average, $student) : null;

            if ($termSummary) {
                $currentThreshold = $this->getThresholdValue($termSummary->term_average);
                $termSummary->update([
                    'grand_total' => $grandTotal > 0 ? $grandTotal : null,
                    'term_average' => $average ? round($average, 1) : null,
                    'cumulative_average' => $cumulativeAverage ? round($cumulativeAverage, 1) : null,
                    'last_term_average' => $lastTermAverage ? round($lastTermAverage, 1) : null,
                    'subjects_offered' => $subjectsOffered,
                    'principal_remark' => $currentThreshold !== $newThreshold || !$termSummary->principal_remark ? $principalRemark : $termSummary->principal_remark,
                    'teacher_remark' => $currentThreshold !== $newThreshold || !$termSummary->teacher_remark ? $teacherRemark : $termSummary->teacher_remark,
                    'position' => $position,
                    'next_term_begins' => $nextTermBegins,
                    'date_issued' => $dateIssued,
                ]);
            } else if ($grandTotal > 0 || $subjectsOffered > 0) {
                $termSummary = StudentTermSummary::create([
                    'student_id' => $studentId,
                    'term' => $term,
                    'session_id' => $sessionId,
                    'class_id' => $classId,
                    'grand_total' => $grandTotal > 0 ? $grandTotal : null,
                    'term_average' => $average ? round($average, 1) : null,
                    'cumulative_average' => $cumulativeAverage ? round($cumulativeAverage, 1) : null,
                    'last_term_average' => $lastTermAverage ? round($lastTermAverage, 1) : null,
                    'subjects_offered' => $subjectsOffered,
                    'principal_remark' => $principalRemark,
                    'teacher_remark' => $teacherRemark,
                    'position' => $position,
                    'next_term_begins' => $nextTermBegins,
                    'date_issued' => $dateIssued,
                ]);
            }

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in saveResult: {$e->getMessage()}", [
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'term' => $term,
                'session_id' => $sessionId,
                'class_id' => $classId,
                'data' => $data,
                'existing_result' => $existingResult ? $existingResult->toArray() : null,
                'exception' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Update results with form data.
     */
    protected function updateResultsHelper($student, $term, $sessionId, $form, $classId)
    {
        try {
            $results = $form['results'] ?? [];
            $nextTermBegins = $form['next_term_begins'] ?? null;
            $dateIssued = $form['date_issued'] ?? null;
            $position = $form['position'] ?? null;

            foreach ($results as $resultForm) {
                $subjectId = (int)$resultForm['subject_id'];
                $data = [
                    'class_assessment' => isset($resultForm['class_assessment']) && $resultForm['class_assessment'] !== '' ? (int)$resultForm['class_assessment'] : null,
                    'summative_test' => isset($resultForm['summative_test']) && $resultForm['summative_test'] !== '' ? (int)$resultForm['summative_test'] : null,
                    'exam' => isset($resultForm['exam']) && $resultForm['exam'] !== '' ? (int)$resultForm['exam'] : null,
                    'next_term_begins' => $nextTermBegins,
                    'date_issued' => $dateIssued,
                    'position' => $position,
                ];
                $this->saveResult($student->id, $subjectId, $term, $sessionId, $data, $classId);
            }
        } catch (\Exception $e) {
            Log::error("Error in updateResultsHelper: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Save class-wide fields (next_term_begins, date_issued, position).
     */
    protected function saveClassWideFields($classId, $sessionId, $term, $data)
    {
        try {
            $nextTermBegins = isset($data['next_term_begins']) && $data['next_term_begins'] !== '' ? $data['next_term_begins'] : null;
            $dateIssued = isset($data['date_issued']) && $data['date_issued'] !== '' ? $data['date_issued'] : null;
            $position = isset($data['position']) && $data['position'] !== '' ? $data['position'] : null;

            $studentClassHistories = StudentClassHistory::where([
                'session_id' => $sessionId,
                'class_id' => $classId,
                'is_active' => true,
            ])->whereNull('leave_date')->get();

            $studentIds = $studentClassHistories->pluck('student_id')->toArray();
            $termSummaries = StudentTermSummary::whereIn('student_id', $studentIds)
                ->where([
                    'term' => $term,
                    'session_id' => $sessionId,
                    'class_id' => $classId,
                ])->get();

            $updated = false;
            foreach ($termSummaries as $termSummary) {
                $updateData = [];
                if ($nextTermBegins !== null) {
                    $updateData['next_term_begins'] = $nextTermBegins;
                    $updated = true;
                }
                if ($dateIssued !== null) {
                    $updateData['date_issued'] = $dateIssued;
                    $updated = true;
                }
                if ($position !== null) {
                    $updateData['position'] = $position;
                    $updated = true;
                }
                if ($updated) {
                    $termSummary->update($updateData);
                }
            }

            return $updated ? $termSummaries->count() : 0;
        } catch (\Exception $e) {
            Log::error("Error in saveClassWideFields: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Calculate student results and aggregates.
     */
    protected function calculateResults($studentId, $term, $sessionId, $classId)
    {
        try {
            $results = Result::where([
                'student_id' => $studentId,
                'term' => $term,
                'session_id' => $sessionId,
                'class_id' => $classId,
            ])->get();

            $grandTotal = $results->sum(fn($r) => $r->total ?? 0);
            [$average, $subjectsOffered] = $this->calculateAverage($results);
            $average = $average ? round($average, 1) : null;
            $cumulativeAverage = $this->calculateCumulativeAverage($studentId, $term, $sessionId);
            $lastTerm = $this->getLastTerm($term);
            $lastTermResults = $lastTerm ? Result::where([
                'student_id' => $studentId,
                'term' => $lastTerm,
                'session_id' => $sessionId,
                'class_id' => $classId,
            ])->get() : [];
            [$lastTermAverage] = $this->calculateAverage($lastTermResults);
            $lastTermAverage = $lastTermAverage ? round($lastTermAverage, 1) : null;

            return [$grandTotal, $average, $cumulativeAverage, $lastTermAverage, $subjectsOffered];
        } catch (\Exception $e) {
            Log::error("Error in calculateResults: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Download broadsheet as Excel file.
     */
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
            $sheet->setCellValue($column . '1', $subject->name . ' (C/A)');
            $column++;
            $sheet->setCellValue($column . '1', $subject->name . ' (S/T)');
            $column++;
            $sheet->setCellValue($column . '1', $subject->name . ' (Exam)');
            $column++;
            $sheet->setCellValue($column . '1', $subject->name . ' (Total)');
            $column++;
            $sheet->setCellValue($column . '1', $subject->name . ' (Grade)');
            $column++;
        }
        $sheet->setCellValue($column . '1', 'Grand Total');
        $column++;
        $sheet->setCellValue($column . '1', 'Term Average');
        $column++;
        $sheet->setCellValue($column . '1', 'Cumulative Average');
        $column++;
        $sheet->setCellValue($column . '1', 'Position');
        $column++;
        $sheet->setCellValue($column . '1', 'Principal Remark');
        $column++;
        $sheet->setCellValue($column . '1', 'Teacher Remark');

        // Data
        $row = 2;
        foreach ($students as $student) {
            $results = Result::where('student_id', $student->id)
                ->where('session_id', $currentSession->id)
                ->where('term', $currentTerm->value)
                ->whereIn('subject_id', $subjects->pluck('id'))
                ->where('class_id', $class->id)
                ->get()
                ->keyBy('subject_id');

            $termSummary = StudentTermSummary::where('student_id', $student->id)
                ->where('session_id', $currentSession->id)
                ->where('term', $currentTerm->value)
                ->where('class_id', $class->id)
                ->first();

            $sheet->setCellValue('A' . $row, $student->reg_no);
            $sheet->setCellValue('B' . $row, $student->first_name . ' ' . $student->last_name);
            $column = 'C';
            foreach ($subjects as $subject) {
                $result = $results->get($subject->id);
                $sheet->setCellValue($column . $row, $result && $result->class_assessment !== null ? $result->class_assessment : '-');
                $column++;
                $sheet->setCellValue($column . $row, $result && $result->summative_test !== null ? $result->summative_test : '-');
                $column++;
                $sheet->setCellValue($column . $row, $result && $result->exam !== null ? $result->exam : '-');
                $column++;
                $sheet->setCellValue($column . $row, $result && $result->total !== null ? $result->total : '-');
                $column++;
                $sheet->setCellValue($column . $row, $result && $result->grade ? $result->grade : '-');
                $column++;
            }
            $sheet->setCellValue($column . $row, $termSummary && $termSummary->grand_total !== null ? $termSummary->grand_total : '-');
            $column++;
            $sheet->setCellValue($column . $row, $termSummary && $termSummary->term_average !== null ? $termSummary->term_average : '-');
            $column++;
            $sheet->setCellValue($column . $row, $termSummary && $termSummary->cumulative_average !== null ? $termSummary->cumulative_average : '-');
            $column++;
            $sheet->setCellValue($column . $row, $termSummary && $termSummary->position ? $termSummary->position : '-');
            $column++;
            $sheet->setCellValue($column . $row, $termSummary && $termSummary->principal_remark ? $termSummary->principal_remark : '-');
            $column++;
            $sheet->setCellValue($column . $row, $termSummary && $termSummary->teacher_remark ? $termSummary->teacher_remark : '-');
            $row++;
        }

        $this->logActivity("Downloaded broadsheet for class {$class->name}", [
            'class_id' => $class->id,
            'term' => $currentTerm->value,
        ]);

        $writer = new Xlsx($spreadsheet);
        $filename = Str::slug("{$class->name}-broadsheet-{$currentSession->year}-{$currentTerm->value}.xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Display the upload results form.
     */
    public function uploadResultsForm(string $className)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        $class = Classes::where('name', urldecode($className))->firstOrFail();
        $subjects = $class->subjects()->where('deactivated', false)->orderBy('name')->get();

        return view('admin.results.upload_results', compact(
            'class',
            'subjects',
            'currentSession',
            'currentTerm'
        ));
    }

    /**
     * Upload results from an Excel file.
     */
    public function uploadResults(Request $request, string $className)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        $class = Classes::where('name', urldecode($className))->firstOrFail();

        try {
            $request->validate([
                'results_file' => 'required|file|mimes:xlsx,xls|max:2048',
            ]);

            $file = $request->file('results_file');
            $reader = new XlsxReader();
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Validate headers
            $expectedHeaders = ['Reg No', 'Student Name'];
            $subjects = $class->subjects()->where('deactivated', false)->orderBy('name')->get();
            foreach ($subjects as $subject) {
                $expectedHeaders[] = $subject->name . ' Class Assessment';
                $expectedHeaders[] = $subject->name . ' Summative Test';
                $expectedHeaders[] = $subject->name . ' Exam';
            }

            $headers = array_slice($rows[0], 0, count($expectedHeaders));
            if ($headers !== $expectedHeaders) {
                throw ValidationException::withMessages(['results_file' => 'Invalid file format. Please ensure the file has the correct headers.']);
            }

            DB::transaction(function () use ($rows, $currentSession, $currentTerm, $class, $subjects) {
                $subjectMap = $subjects->pluck('id', 'name')->toArray();

                foreach (array_slice($rows, 1) as $row) {
                    $regNo = $row[0];
                    $student = Student::where('reg_no', $row[0])->first();
                    if (!$student) {
                        Log::warning("Student with reg_no {$regNo} not found, skipping row.");
                        continue;
                    }

                    $grandTotal = 0;
                    $subjectCount = 0;

                    foreach ($subjects as $index => $subject) {
                        $colOffset = 2 + ($index * 3); // Class Assessment, Summative Test, Exam for each subject
                        $classAssessment = !empty($row[$colOffset]) && is_numeric($row[$colOffset]) ? (int)$row[$colOffset] : null;
                        $summativeTest = !empty($row[$colOffset + 1]) && is_numeric($row[$colOffset + 1]) ? (int)$row[$colOffset + 1] : null;
                        $exam = !empty($row[$colOffset + 2]) && is_numeric($row[$colOffset + 2]) ? (int)$row[$colOffset + 2] : null;

                        // Validate scores
                        if ($classAssessment !== null && ($classAssessment < 0 || $classAssessment > 20)) {
                            throw ValidationException::withMessages(['results_file' => "Invalid Class Assessment score for {$regNo} in {$subject->name}"]);
                        }
                        if ($summativeTest !== null && ($summativeTest < 0 || $summativeTest > 20)) {
                            throw ValidationException::withMessages(['results_file' => "Invalid Summative Test score for {$regNo} in {$subject->name}"]);
                        }
                        if ($exam !== null && ($exam < 0 || $exam > 60)) {
                            throw ValidationException::withMessages(['results_file' => "Invalid Exam score for {$regNo} in {$subject->name}"]);
                        }

                        $total = ($classAssessment ?? 0) + ($summativeTest ?? 0) + ($exam ?? 0);
                        $grade = $total > 0 ? $this->calculateGrade($total) : null;
                        $remark = $total > 0 ? $this->generateRemark($total) : null;

                        Result::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'session_id' => $currentSession->id,
                                'term' => $currentTerm->value,
                                'subject_id' => $subjectMap[$subject->name],
                                'class_id' => $class->id,
                            ],
                            [
                                'class_assessment' => $classAssessment,
                                'summative_test' => $summativeTest,
                                'exam' => $exam,
                                'total' => $total > 0 ? $total : null,
                                'grade' => $grade,
                                'remark' => $remark,
                            ]
                        );

                        if ($total > 0) {
                            $grandTotal += $total;
                            $subjectCount++;
                        }
                    }

                    $termAverage = $subjectCount > 0 ? $grandTotal / $subjectCount : null;
                    $principalRemark = $termAverage !== null ? $this->generatePrincipalRemark($termAverage, $student) : null;
                    $teacherRemark = $termAverage !== null ? $this->generateTeacherRemark($termAverage, $student) : null;
                    $cumulativeAverage = $this->calculateCumulativeAverage($student->id, $currentTerm->value, $currentSession->id);
                    $lastTerm = $this->getLastTerm($currentTerm->value);
                    $lastTermResults = $lastTerm ? Result::where([
                        'student_id' => $student->id,
                        'term' => $lastTerm,
                        'session_id' => $currentSession->id,
                        'class_id' => $class->id,
                    ])->get() : [];
                    [$lastTermAverage] = $this->calculateAverage($lastTermResults);
                    $lastTermAverage = $lastTermAverage ? round($lastTermAverage, 1) : null;

                    StudentTermSummary::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'session_id' => $currentSession->id,
                            'term' => $currentTerm->value,
                            'class_id' => $class->id,
                        ],
                        [
                            'grand_total' => $grandTotal > 0 ? $grandTotal : null,
                            'term_average' => $termAverage ? round($termAverage, 1) : null,
                            'cumulative_average' => $cumulativeAverage ? round($cumulativeAverage, 1) : null,
                            'last_term_average' => $lastTermAverage,
                            'subjects_offered' => $subjectCount,
                            'principal_remark' => $principalRemark,
                            'teacher_remark' => $teacherRemark,
                            'next_term_begins' => null,
                            'date_issued' => now()->toDateString(),
                        ]
                    );

                    $this->logActivity("Uploaded results for student {$student->id} via spreadsheet", [
                        'student_id' => $student->id,
                        'term' => $currentTerm->value,
                        'class_id' => $class->id,
                    ]);
                }
            });

            return back()->with('success', 'Results uploaded successfully!');

        } catch (ValidationException $e) {
            Log::error("Validation error in uploadResults: " . json_encode($e->errors()), [
                'class_name' => $className,
                'request_data' => $request->all(),
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error uploading results for class {$className}: {$e->getMessage()}", [
                'class_name' => $className,
                'exception' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Error uploading results: ' . $e->getMessage());
        }
    }

    /**
     * Notify students of their results.
     */
    public function notifyStudents(Request $request, string $className)
    {
        $currentSessionData = $this->getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        $class = Classes::where('name', urldecode($className))->firstOrFail();
        $students = $this->getStudentsQuery($currentSession, $currentTerm->value)
            ->where('classes.id', $class->id)
            ->get();

        try {
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            foreach ($students as $student) {
                $termSummary = StudentTermSummary::where('student_id', $student->id)
                    ->where('session_id', $currentSession->id)
                    ->where('term', $currentTerm->value)
                    ->where('class_id', $class->id)
                    ->first();

                if ($termSummary && $student->parent_phone_number) {
                    $student->notify(new ResultNotification($termSummary, $request->message));
                }
            }

            $this->logActivity("Sent result notifications for class {$class->name}", [
                'class_id' => $class->id,
                'term' => $currentTerm->value,
            ]);

            return back()->with('success', 'Notifications sent successfully!');

        } catch (ValidationException $e) {
            Log::error("Validation error in notifyStudents: " . json_encode($e->errors()), [
                'class_name' => $className,
                'request_data' => $request->all(),
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error sending notifications for class {$className}: {$e->getMessage()}", [
                'class_name' => $className,
                'exception' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Error sending notifications.');
        }
    }
}
