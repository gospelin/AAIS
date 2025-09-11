<?php
// app/Models/Teacher.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'user_id',
        'employee_id',
        'section'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_teacher')
            ->withPivot(['session_id', 'term', 'is_form_teacher'])
            ->withTimestamps();
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject')
            ->withTimestamps();
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public static function generateEmployeeId($section)
    {
        do {
            $randomSuffix = strtoupper(Str::random(4));
            $employeeId = "{$section}/" . date('Y') . "/{$randomSuffix}";
        } while (self::where('employee_id', $employeeId)->exists());

        return $employeeId;
    }

    public function isFormTeacherForClass($classId)
    {
        return $this->classes()
            ->wherePivot('class_id', $classId)
            ->wherePivot('is_form_teacher', true)
            ->exists();
    }
}
