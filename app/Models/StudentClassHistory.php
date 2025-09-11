<?php
// app/Models/StudentClassHistory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TermEnum;
use Carbon\Carbon;

class StudentClassHistory extends Model
{
    use HasFactory;

    protected $table = 'student_class_history';  // Explicitly set table name to match migration

    protected $fillable = [
        'student_id',
        'session_id',
        'class_id',
        'start_term',
        'end_term',
        'join_date',
        'leave_date',
        'is_active'
    ];

    protected $casts = [
        'join_date' => 'datetime',
        'leave_date' => 'datetime',
        'is_active' => 'boolean',
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
            TermEnum::THIRD->value => 3
        ];

        $startOrder = $termOrder[$this->start_term] ?? 1;
        $endOrder = $this->end_term ? ($termOrder[$this->end_term] ?? 4) : 4;
        $targetOrder = $termOrder[$term] ?? 1;

        return $this->session_id == $sessionId &&
            $this->is_active &&
            is_null($this->leave_date) &&
            $startOrder <= $targetOrder && $targetOrder <= $endOrder;
    }

    public function markAsLeft($term)
    {
        if (!in_array($term, array_column(TermEnum::cases(), 'value'))) {
            throw new \InvalidArgumentException("Invalid term: {$term}");
        }

        $termOrder = [
            TermEnum::FIRST->value => 1,
            TermEnum::SECOND->value => 2,
            TermEnum::THIRD->value => 3
        ];

        $startOrder = $termOrder[$this->start_term] ?? 1;
        $leaveOrder = $termOrder[$term] ?? 1;

        if ($leaveOrder <= $startOrder) {
            $this->is_active = false;
        }

        $this->end_term = $term;
        $this->leave_date = Carbon::now('Africa/Lagos');
        $this->save();
    }

    public function reenroll($sessionId, $classId, $term)
    {
        if ($this->is_active && is_null($this->leave_date)) {
            throw new \InvalidArgumentException('Student is already active');
        }

        $this->session_id = $sessionId;
        $this->class_id = $classId;
        $this->start_term = $term;
        $this->end_term = null;
        $this->join_date = Carbon::now('Africa/Lagos');
        $this->leave_date = null;
        $this->is_active = true;
        $this->save();
    }
}
