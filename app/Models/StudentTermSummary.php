<?php
// app/Models/StudentTermSummary.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'created_at'
    ];

    protected $casts = [
        'grand_total' => 'integer',
        'term_average' => 'float',
        'cumulative_average' => 'float',
        'last_term_average' => 'float',
        'subjects_offered' => 'integer',
        'created_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function session()
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
