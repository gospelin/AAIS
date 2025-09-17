<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TermEnum;
use Carbon\Carbon;

class StudentTermSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'session_id',
        'term',
        'grand_total',
        'term_average',
        'cumulative_average',
        'last_term_average',
        'subjects_offered',
        'position',
        'principal_remark',
        'teacher_remark',
        'next_term_begins',
        'date_issued',
    ];

    protected $casts = [
        'term' => TermEnum::class,
        'grand_total' => 'integer',
        'term_average' => 'float',
        'cumulative_average' => 'float',
        'last_term_average' => 'float',
        'subjects_offered' => 'integer',
        'position' => 'string',
        'principal_remark' => 'string',
        'teacher_remark' => 'string',
        'next_term_begins' => 'string',
        'date_issued' => 'string',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function session()
    {
        return $this->belongsTo(AcademicSession::class);
    }
}