<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Result;
use App\Models\StudentTermSummary;
use App\Models\FeePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Enums\TermEnum;

class AdminBaseController extends Controller
{
    /**
     * Get the current academic session and term.
     *
     * @param bool $usePreference
     * @return array [session, term]
     */
    protected function getCurrentSessionAndTerm($usePreference = false)
    {
        return AcademicSession::getCurrentSessionAndTerm($usePreference);
    }

    /**
     * Log an activity to the audit log.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function logActivity($message, array $context = [])
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $message,
            'timestamp' => Carbon::now(),
        ]);
    }

    /**
     * Build the base query for students with class joins.
     *
     * @param AcademicSession $currentSession
     * @param string|null $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getStudentsQuery($currentSession, $term = null)
    {
        $termOrder = [
            TermEnum::FIRST->value => 1,
            TermEnum::SECOND->value => 2,
            TermEnum::THIRD->value => 3
        ];

        $targetTermOrder = $termOrder[$term] ?? 1;

        return Student::select('students.*', 'classes.name as class_name', 'classes.hierarchy')
            ->leftJoin('student_class_history', function ($join) use ($currentSession, $targetTermOrder) {
                $join->on('students.id', '=', 'student_class_history.student_id')
                    ->where('student_class_history.session_id', '=', $currentSession->id)
                    ->where('student_class_history.is_active', '=', true)
                    ->whereNull('student_class_history.leave_date')
                    ->whereRaw(
                        'CASE student_class_history.start_term 
                                WHEN ? THEN 1 WHEN ? THEN 2 WHEN ? THEN 3 ELSE 1 END <= ?',
                        [TermEnum::FIRST->value, TermEnum::SECOND->value, TermEnum::THIRD->value, $targetTermOrder]
                    )
                    ->whereRaw(
                        '(student_class_history.end_term IS NULL OR 
                                CASE student_class_history.end_term 
                                WHEN ? THEN 1 WHEN ? THEN 2 WHEN ? THEN 3 ELSE 4 END >= ?)',
                        [TermEnum::FIRST->value, TermEnum::SECOND->value, TermEnum::THIRD->value, $targetTermOrder]
                    );
            })
            ->leftJoin('classes', 'student_class_history.class_id', '=', 'classes.id')
            ->with(['feePayments', 'classHistory']);
    }

    /**
     * Apply filters to the students query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $studentsQuery
     * @param string $enrollmentStatus
     * @param string $feeStatus
     * @param string $approvalStatus
     * @param AcademicSession $currentSession
     * @param TermEnum $currentTerm
     * @param string|null $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyFiltersToStudentsQuery($studentsQuery, $enrollmentStatus, $feeStatus, $approvalStatus, $currentSession, $currentTerm, $term = null)
    {
        $termOrder = [
            TermEnum::FIRST->value => 1,
            TermEnum::SECOND->value => 2,
            TermEnum::THIRD->value => 3
        ];

        $targetTermOrder = $termOrder[$term] ?? $termOrder[$currentTerm->value];

        if ($enrollmentStatus === 'active') {
            $studentsQuery->where('student_class_history.is_active', true)
                ->whereNull('student_class_history.leave_date')
                ->where('student_class_history.session_id', $currentSession->id)
                ->whereRaw(
                    'CASE student_class_history.start_term 
                                        WHEN ? THEN 1 WHEN ? THEN 2 WHEN ? THEN 3 ELSE 1 END <= ?',
                    [TermEnum::FIRST->value, TermEnum::SECOND->value, TermEnum::THIRD->value, $targetTermOrder]
                )
                ->whereRaw(
                    '(student_class_history.end_term IS NULL OR 
                                        CASE student_class_history.end_term 
                                        WHEN ? THEN 1 WHEN ? THEN 2 WHEN ? THEN 3 ELSE 4 END >= ?)',
                    [TermEnum::FIRST->value, TermEnum::SECOND->value, TermEnum::THIRD->value, $targetTermOrder]
                );
        } elseif ($enrollmentStatus === 'inactive') {
            $studentsQuery->where(function ($query) use ($currentSession, $targetTermOrder) {
                $query->whereNotExists(function ($subQuery) use ($currentSession, $targetTermOrder) {
                    $subQuery->select(DB::raw(1))
                        ->from('student_class_history as sch')
                        ->whereColumn('sch.student_id', 'students.id')
                        ->where('sch.session_id', $currentSession->id)
                        ->where('sch.is_active', true)
                        ->whereNull('sch.leave_date')
                        ->whereRaw(
                            'CASE sch.start_term WHEN ? THEN 1 WHEN ? THEN 2 WHEN ? THEN 3 ELSE 1 END <= ?',
                            [TermEnum::FIRST->value, TermEnum::SECOND->value, TermEnum::THIRD->value, $targetTermOrder]
                        )
                        ->whereRaw(
                            '(sch.end_term IS NULL OR CASE sch.end_term WHEN ? THEN 1 WHEN ? THEN 2 WHEN ? THEN 3 ELSE 4 END >= ?)',
                            [TermEnum::FIRST->value, TermEnum::SECOND->value, TermEnum::THIRD->value, $targetTermOrder]
                        );
                })
                    ->orWhere(function ($query) use ($currentSession, $targetTermOrder) {
                        $query->whereExists(function ($subQuery) use ($currentSession, $targetTermOrder) {
                            $subQuery->select(DB::raw(1))
                                ->from('student_class_history as sch2')
                                ->whereColumn('sch2.student_id', 'students.id')
                                ->where('sch2.session_id', $currentSession->id)
                                ->where('sch2.is_active', false)
                                ->orWhereNotNull('sch2.leave_date')
                                ->whereRaw(
                                    'CASE sch2.end_term WHEN ? THEN 1 WHEN ? THEN 2 WHEN ? THEN 3 ELSE 0 END <= ?',
                                    [TermEnum::FIRST->value, TermEnum::SECOND->value, TermEnum::THIRD->value, $targetTermOrder]
                                );
                        });
                    });
            });
        }

        if ($feeStatus) {
            $studentsQuery->leftJoin('fee_payments', function ($join) use ($currentSession, $term) {
                $join->on('fee_payments.student_id', '=', 'students.id')
                    ->where('fee_payments.session_id', '=', $currentSession->id)
                    ->where('fee_payments.term', '=', $term);
            });

            if ($feeStatus === 'paid') {
                $studentsQuery->where('fee_payments.has_paid_fee', true);
            } elseif ($feeStatus === 'unpaid') {
                $studentsQuery->where(function ($query) {
                    $query->where('fee_payments.has_paid_fee', false)
                        ->orWhereNull('fee_payments.id');
                });
            }
        }

        if ($approvalStatus) {
            $approved = $approvalStatus === 'approved';
            $studentsQuery->where('students.approved', $approved);
        }

        return $studentsQuery;
    }

    /**
     * Group students by class name and sort.
     *
     * @param array|Collection $students
     * @return Collection
     */
    protected function groupStudentsByClass($students)
    {
        return collect($students)->groupBy(function ($item) {
            return $item->class_name ?? 'Unassigned';
        })->map(function ($group, $className) {
            return $group->sortBy(function ($student) {
                return strtolower($student->first_name) . strtolower($student->last_name);
            })->values();
        })->sortBy(function ($group, $className) {
            $hierarchy = Classes::where('name', $className)->first()?->hierarchy ?? 999;
            return $hierarchy;
        });
    }

    /**
     * Calculate grade based on total score.
     *
     * @param float $total
     * @return string
     */
    protected function calculateGrade($total)
    {
        if ($total >= 95)
            return "A+";
        if ($total >= 80)
            return "A";
        if ($total >= 70)
            return "B+";
        if ($total >= 65)
            return "B";
        if ($total >= 60)
            return "C+";
        if ($total >= 50)
            return "C";
        if ($total >= 40)
            return "D";
        if ($total >= 30)
            return "E";
        return "F";
    }

    /**
     * Generate remark based on total score.
     *
     * @param float $total
     * @return string
     */
    protected function generateRemark($total)
    {
        if ($total >= 95)
            return "Outstanding";
        if ($total >= 80)
            return "Excellent";
        if ($total >= 70)
            return "Very Good";
        if ($total >= 65)
            return "Good";
        if ($total >= 60)
            return "Credit";
        if ($total >= 50)
            return "Credit";
        if ($total >= 40)
            return "Poor";
        if ($total >= 30)
            return "Very Poor";
        return "Failed";
    }

    /**
     * Get pass/excellent thresholds for a class hierarchy.
     *
     * @param int $classHierarchy
     * @return array ['pass' => int, 'excellent' => int]
     */
    protected function getThreshold($classHierarchy)
    {
        if ($classHierarchy <= 3) {
            return ['pass' => 50, 'excellent' => 80];
        } elseif ($classHierarchy <= 6) {
            return ['pass' => 60, 'excellent' => 85];
        } else {
            return ['pass' => 65, 'excellent' => 90];
        }
    }

    /**
     * Generate principal's remark based on average and thresholds.
     *
     * @param float $average
     * @param array $threshold
     * @return string
     */
    protected function generatePrincipalRemark($average, $threshold)
    {
        if ($average >= $threshold['excellent']) {
            return "Excellent performance, keep it up!";
        } elseif ($average >= $threshold['pass']) {
            return "Good effort, aim higher next term.";
        } else {
            return "Needs improvement, please seek academic support.";
        }
    }
}
