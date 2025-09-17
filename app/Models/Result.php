<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TermEnum;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'class_id',
        'session_id',
        'term',
        'class_assessment',
        'summative_test',
        'exam',
        'total',
        'grade',
        'remark',
    ];

    protected $casts = [
        'term' => TermEnum::class,
        'class_assessment' => 'integer',
        'summative_test' => 'integer',
        'exam' => 'integer',
        'total' => 'integer',
        'grade' => 'string',
        'remark' => 'string',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
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