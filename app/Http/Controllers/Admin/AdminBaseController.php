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
     * @param bool $required
     * @return array [session, term]
     */
    protected function getCurrentSessionAndTerm($required = false)
    {
        try {
            [$session, $term] = AcademicSession::getCurrentSessionAndTerm(true);

            if ($required && (!$session || !$term)) {
                throw new \Exception('No current session or term set.');
            }

            return [$session, $term];
        } catch (\Exception $e) {
            Log::error("Error fetching current session and term: " . $e->getMessage());
            return [null, null];
        }
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
            'details' => json_encode($context), // Store context in details column
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
     * Group students by class name.
     *
     * @param \Illuminate\Database\Eloquent\Collection $students
     * @return array
     */
    protected function groupStudentsByClass($students)
    {
        return $students->groupBy('class_name')->sortBy(function ($group, $className) {
            return $group->first()->hierarchy ?? 9999;
        })->toArray();
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
     * Calculate grade based on total score.
     *
     * @param float $total
     * @return string|null
     */
    protected function calculateGrade($total)
    {
        if ($total === null || $total < 0)
            return null;
        if ($total >= 95)
            return 'A+';
        if ($total >= 80)
            return 'A';
        if ($total >= 70)
            return 'B';
        if ($total >= 65)
            return 'C+';
        if ($total >= 50)
            return 'C';
        if ($total >= 40)
            return 'D';
        if ($total >= 30)
            return 'E';
        return 'F';
    }

    /**
     * Generate remark based on total score.
     *
     * @param float $total
     * @return string|null
     */
    protected function generateRemark($total)
    {
        if ($total === null || $total < 0)
            return null;
        if ($total >= 95)
            return 'Outstanding';
        if ($total >= 80)
            return 'Excellent';
        if ($total >= 70)
            return 'Very Good';
        if ($total >= 65)
            return 'Good';
        if ($total >= 60)
            return 'Credit';
        if ($total >= 50)
            return 'Pass';
        if ($total >= 40)
            return 'Poor';
        if ($total >= 30)
            return 'Very Poor';
        return 'Failed';
    }
    
    /**
     * Get threshold value for an average.
     *
     * @param float|null $average
     * @return int|null
     */
    protected function getThresholdValue($average)
    {
        if ($average === null) {
            return null;
        }
        $thresholds = [0, 30, 40, 50, 60, 65, 70, 80, 90, 95];
        foreach ($thresholds as $i => $threshold) {
            if (isset($thresholds[$i + 1]) && $average >= $threshold && $average < $thresholds[$i + 1]) {
                return $threshold;
            }
        }
        return $average >= 95 ? 95 : 0;
    }

    /**
     * Generate principal's remark based on average and student.
     *
     * @param float|null $average
     * @param \App\Models\Student|null $student
     * @return string|null
     */
    protected function generatePrincipalRemark($average, $student = null)
    {
        if ($average === null) {
            return "";
        }
        $first_name = $student ? $student->first_name : "Student";
        $last_name = $student ? $student->last_name : "";
        $full_name = trim("{$first_name} {$last_name}");

        $remarks = [
            95 => [
                "Truly exceptional, {$full_name}! Your stellar achievement is a shining example for all students at this school.",
                "Phenomenal results, {$first_name}! You’ve set a benchmark for excellence across nursery, primary, and secondary levels.",
                "Superb performance, {$full_name}! You’ve outdone yourself and brought pride to our institution.",
                "{$first_name}, your outstanding dedication has paid off brilliantly! A top-tier result worthy of celebration.",
                "Absolutely remarkable, {$full_name}! Your consistency and brilliance are unmatched in this term.",
                "{$first_name} {$last_name}, you are a star pupil! Your exceptional scores reflect your hard work and potential.",
                "An extraordinary feat, {$first_name}! Your academic prowess inspires both peers and teachers alike."
            ],
            90 => [
                "Exceptional performance, {$full_name}! You are an inspiration to others in this school, keep it up!",
                "Outstanding results, {$first_name}! You’ve set high standards that others should aspire to achieve.",
                "Excellent work, {$full_name}! You’ve proven yourself as a top performer across all levels.",
                "{$first_name}, your brilliant effort this term is commendable! A fantastic example for your classmates.",
                "Well done, {$full_name}! Your dedication to excellence shines through in these impressive results.",
                "{$first_name} {$last_name}, you’ve shown remarkable skill and focus! A truly outstanding achievement.",
                "A splendid performance, {$first_name}! Your consistency is paving the way for a bright future."
            ],
            80 => [
                "Very impressive performance, {$full_name}! Aim for even higher success in the next term.",
                "Great job, {$first_name}! Your efforts are commendable and show promise for greater heights.",
                "Consistently good results, {$full_name}. Keep striving for excellence in all you do!",
                "{$first_name}, you’ve done wonderfully this term! Push a little more to reach the top.",
                "Well done, {$full_name}! Your hard work is evident, and greater achievements are within reach.",
                "{$first_name} {$last_name}, a solid effort! Maintain this pace and aim for perfection.",
                "Bravo, {$first_name}! Your performance is strong—keep building on this excellent foundation."
            ],
            70 => [
                "Good performance, {$full_name}! With a little more effort, you’ll excel further in your studies.",
                "{$first_name}, you’ve done well this term, but there’s room to improve and shine brighter.",
                "Nice results, {$full_name}! Strive for greater achievements with focus and determination.",
                "{$first_name} {$last_name}, a commendable effort! Keep pushing to unlock your full potential.",
                "Well done, {$full_name}! You’re on a good path—add more diligence for outstanding success.",
                "{$first_name}, your performance is solid! Aim higher by working on your weaker areas.",
                "A promising result, {$full_name}! Consistency and extra effort will take you far."
            ],
            65 => [
                "Satisfactory effort, {$full_name}. Aim to push beyond the basics for better outcomes.",
                "{$first_name}, you’re doing alright, but consistency is key to reaching higher grades.",
                "Good start, {$full_name}! Work harder to climb to the next level of success.",
                "{$first_name} {$last_name}, a fair performance! More focus will help you improve greatly.",
                "{$full_name}, you’ve laid a foundation—build on it with dedication and study.",
                "{$first_name}, your effort is noted! Aim higher by strengthening your skills this term.",
                "Acceptable results, {$full_name}. Step up your game to achieve more in your academics."
            ],
            60 => [
                "Fair performance, {$full_name}. Keep pushing for better results in the coming terms.",
                "{$first_name}, you’re on the right track—focus on improving your weaker subjects.",
                "A decent effort, {$full_name}! Keep working hard to achieve more and excel.",
                "{$first_name} {$last_name}, you’ve shown some progress! More effort will yield greater rewards.",
                "{$full_name}, an okay result—strengthen your study habits for a stronger outcome.",
                "{$first_name}, you’re moving forward! Target your challenges to boost your scores.",
                "Moderate success, {$full_name}. Determination will help you rise above this level."
            ],
            50 => [
                "An average performance, {$full_name}. Aim for consistent improvement in all subjects.",
                "{$first_name}, your results are satisfactory, but you can do much better with effort.",
                "A fair effort, {$full_name}! Focus and determination will lead to greater success.",
                "{$first_name} {$last_name}, you’re at the midpoint—work harder to stand out next term.",
                "{$full_name}, this is a starting point! Build your skills to improve your grades.",
                "{$first_name}, an acceptable result—push beyond average with serious study habits.",
                "Room for growth, {$full_name}! Take your studies seriously to see better outcomes."
            ],
            40 => [
                "Below average, {$full_name}. Significant improvement is required to succeed.",
                "{$first_name}, your performance is concerning—seek guidance to improve this term.",
                "You need to focus more, {$full_name}! Extra effort will help you catch up.",
                "{$first_name} {$last_name}, this isn’t your best—work with teachers to do better.",
                "{$full_name}, your results need attention! Take action to boost your understanding.",
                "{$first_name}, a weak performance—commit to studying harder for progress.",
                "Low scores, {$full_name}. Let’s work together to turn this around next term."
            ],
            30 => [
                "Poor results, {$full_name}. A lot more effort is needed to improve your standing.",
                "{$first_name}, your performance requires immediate attention and serious work.",
                "Seek extra help, {$full_name}! Address your challenges to succeed this year.",
                "{$first_name} {$last_name}, this is below expectations—let’s find ways to improve.",
                "{$full_name}, your scores are low—dedicate more time to your books and lessons.",
                "{$first_name}, a tough term—extra support can help you overcome these struggles.",
                "Very concerning, {$full_name}! Act now to avoid falling further behind."
            ],
            0 => [
                "Unacceptable performance, {$full_name}. Take your studies seriously from now on.",
                "{$first_name}, drastic improvement is needed to progress in your academics.",
                "This is not satisfactory, {$full_name}! Immediate action is required to improve.",
                "{$first_name} {$last_name}, a worrying result—commit to change this outcome.",
                "{$full_name}, your effort is lacking—focus on your education starting today.",
                "{$first_name}, this performance is poor—let’s work together to fix this urgently.",
                "No progress, {$full_name}! Serious dedication is needed to move forward."
            ]
        ];

        foreach (array_reverse(array_keys($remarks)) as $threshold) {
            if ($average >= $threshold) {
                return $remarks[$threshold][array_rand($remarks[$threshold])];
            }
        }
        return "";
    }

    /**
     * Generate teacher's remark based on average and student.
     *
     * @param float|null $average
     * @param \App\Models\Student|null $student
     * @return string|null
     */
    protected function generateTeacherRemark($average, $student = null)
    {
        if ($average === null) {
            return "";
        }
        $first_name = $student ? $student->first_name : "Student";
        $last_name = $student ? $student->last_name : "";
        $full_name = trim("{$first_name} {$last_name}");

        $remarks = [
            95 => [
                "Absolutely outstanding, {$full_name}! You’re a model student for others to follow.",
                "Exceptional effort, {$first_name}! Your results shine brightly this term—keep it up!",
                "Top-tier performance, {$full_name}! Your hard work is truly impressive and inspiring.",
                "{$first_name} {$last_name}, you’ve excelled brilliantly! A star in every subject.",
                "{$full_name}, your dedication is remarkable! You’ve set a high standard this term.",
                "{$first_name}, an amazing result! Your focus and brilliance are commendable.",
                "Perfect scores, {$full_name}! You’re a joy to teach and a pride to this class."
            ],
            90 => [
                "Outstanding performance, {$full_name}! Keep shining in all your subjects.",
                "{$first_name}, your dedication to learning is inspiring—well done this term!",
                "An exemplary effort, {$full_name}! Your hard work has paid off wonderfully.",
                "{$first_name} {$last_name}, fantastic results! You’re a leader among your peers.",
                "{$full_name}, you’ve done exceptionally well! Maintain this excellent momentum.",
                "{$first_name}, a brilliant term! Your consistency is paving your path to success.",
                "Great work, {$full_name}! Your effort stands out as one of the best this term."
            ],
            80 => [
                "A good performance, {$full_name}! Focus on consistent excellence moving forward.",
                "{$first_name}, your hard work is paying off—keep aiming higher each term!",
                "You’ve done very well, {$full_name}! Push your limits for even better results.",
                "{$first_name} {$last_name}, solid effort! You’re close to the top—keep going!",
                "{$full_name}, a strong showing! Build on this to reach outstanding heights.",
                "{$first_name}, well done! Your progress is notable—aim for perfection next time.",
                "Impressive, {$full_name}! Stay committed to rise even higher in your studies."
            ],
            70 => [
                "Good result, {$full_name}! Practice more to perfect your skills this term.",
                "{$first_name}, you’re doing well—focus on weaker areas to improve further.",
                "A good effort, {$full_name}! Strive to achieve even better in your subjects.",
                "{$first_name} {$last_name}, nice work! Extra study will boost your grades.",
                "{$full_name}, you’re on track—target your challenges for greater success.",
                "{$first_name}, a fair result! Keep up the effort to reach higher scores.",
                "Promising, {$full_name}! More attention to detail will lift your performance."
            ],
            65 => [
                "Decent result, {$full_name}. Build on this foundation for better grades.",
                "{$first_name}, you’re improving—more effort will yield stronger outcomes.",
                "Satisfactory, {$full_name}! Aim higher with consistent practice and focus.",
                "{$first_name} {$last_name}, a good base! Work harder to climb higher.",
                "{$full_name}, you’re getting there—strengthen your study habits now.",
                "{$first_name}, fair effort! Target key areas to improve your standing.",
                "Acceptable, {$full_name}! Step up your game to excel in your class."
            ],
            60 => [
                "Fair result, {$full_name}. Put in more effort to rise above this level.",
                "{$first_name}, you’re progressing—focus on difficult topics to improve.",
                "With more effort, {$full_name}, your performance will improve significantly!",
                "{$first_name} {$last_name}, an okay term! Push harder for better results.",
                "{$full_name}, you’re moving along—work on weak spots for growth.",
                "{$first_name}, a modest outcome! Extra study will make a big difference.",
                "Moderate effort, {$full_name}! Keep pushing to unlock your potential."
            ],
            50 => [
                "An average performance, {$full_name}. Work on improving your fundamentals.",
                "{$first_name}, you’ve done okay—additional practice will help you grow.",
                "Keep working on weaker subjects, {$full_name}, to build a strong foundation!",
                "{$first_name} {$last_name}, fair results! Aim higher with more effort.",
                "{$full_name}, this is average—focus more to improve your scores.",
                "{$first_name}, a starting point! Serious study will lift you up.",
                "Room to grow, {$full_name}! Dedicate time to see better progress."
            ],
            40 => [
                "Below average, {$full_name}. Concentrate on improving your basics this term.",
                "{$first_name}, your performance is concerning—seek help to address challenges.",
                "Weak results, {$full_name}! Focus on understanding the core concepts.",
                "{$first_name} {$last_name}, this needs work—let’s improve together.",
                "{$full_name}, low scores—commit to studying harder for better outcomes.",
                "{$first_name}, a tough term—extra support can turn this around.",
                "Struggling, {$full_name}! Let’s tackle your weak areas urgently."
            ],
            30 => [
                "Very poor performance, {$full_name}. A focused study plan is necessary now.",
                "{$first_name}, you need additional support to overcome your difficulties.",
                "Your results are below expectations, {$full_name}! Put in much more effort.",
                "{$first_name} {$last_name}, this is concerning—let’s work on improvement.",
                "{$full_name}, low grades—spend more time with your books and teachers.",
                "{$first_name}, a challenging term—seek help to boost your understanding.",
                "Poor showing, {$full_name}! Act quickly to improve your academics."
            ],
            0 => [
                "Unacceptable, {$full_name}. Urgent attention to your studies is required.",
                "{$first_name}, a very poor performance—take your academics seriously now!",
                "This is concerning, {$full_name}! Take your studies more seriously.",
                "{$first_name} {$last_name}, no progress—let’s change this urgently.",
                "{$full_name}, your effort is lacking—focus on your education today.",
                "{$first_name}, a worrying result—commit to improvement immediately.",
                "Critical, {$full_name}! Serious dedication is needed to move forward."
            ]
        ];

        foreach (array_reverse(array_keys($remarks)) as $threshold) {
            if ($average >= $threshold) {
                return $remarks[$threshold][array_rand($remarks[$threshold])];
            }
        }
        return "";
    }

    /**
     * Calculate subject averages for the broadsheet.
     *
     * @param int $classId
     * @param int $sessionId
     * @param string $term
     * @param \Illuminate\Support\Collection $subjects
     * @return array
     */
    protected function calculateSubjectAverages($classId, $sessionId, $term, $subjects)
    {
        try {
            $averages = [];
            foreach ($subjects as $subject) {
                $results = Result::where([
                    'subject_id' => $subject->id,
                    'session_id' => $sessionId,
                    'term' => $term,
                    'class_id' => $classId,
                ])->whereNotNull('total')->where('total', '>', 0)->get();

                $totals = $results->pluck('total');
                $average = $totals->count() > 0 ? $totals->sum() / $totals->count() : null;
                $averages[$subject->id] = [
                    'average' => $average !== null ? round($average, 2) : null,
                    'subject_id' => $subject->id,
                    'subject_name' => $subject->name,
                ];
            }
            return $averages;
        } catch (\Exception $e) {
            Log::error("Error calculating subject averages: {$e->getMessage()}", [
                'class_id' => $classId,
                'session_id' => $sessionId,
                'term' => $term,
            ]);
            return [];
        }
    }
}
