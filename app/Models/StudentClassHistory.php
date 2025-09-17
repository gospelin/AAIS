<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TermEnum;
use Carbon\Carbon;

class StudentClassHistory extends Model
{
    use HasFactory;

    protected $table = 'student_class_history';

    protected $fillable = [
        'student_id',
        'session_id',
        'class_id',
        'start_term',
        'end_term',
        'join_date',
        'leave_date',
        'is_active',
    ];

    protected $casts = [
        'join_date' => 'datetime',
        'leave_date' => 'datetime',
        'is_active' => 'boolean',
        'start_term' => TermEnum::class,
        'end_term' => TermEnum::class,
    ];

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function isActiveInTerm($sessionId, $term)
    {
        $termOrder = [
            TermEnum::FIRST->value => 1,
            TermEnum::SECOND->value => 2,
            TermEnum::THIRD->value => 3,
        ];

        $startOrder = isset($this->start_term) ? ($termOrder[$this->start_term->value] ?? 1) : 1;
        $endOrder = isset($this->end_term) ? ($termOrder[$this->end_term->value] ?? 4) : 4;
        $targetOrder = $termOrder[$term] ?? 1;

        return $this->session_id == $sessionId &&
            $this->is_active &&
            is_null($this->leave_date) &&
            $startOrder <= $targetOrder &&
            $targetOrder <= $endOrder;
    }

    public function markAsLeft($term)
    {
        if (!in_array($term, array_column(TermEnum::cases(), 'value'))) {
            throw new \InvalidArgumentException("Invalid term: {$term}");
        }

        $this->update([
            'is_active' => false,
            'leave_date' => Carbon::now('Africa/Lagos'),
            'end_term' => $term,
        ]);
    }

    public function reenroll($sessionId, $classId, $term)
    {
        throw new \BadMethodCallException('Use AdminStudentController::studentReenroll to handle reenrollment.');
    }
}