<?php
// app/Models/Classes.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'section', 'hierarchy'];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'class_teacher')
            ->withPivot(['session_id', 'term', 'is_form_teacher'])
            ->withTimestamps();
    }

    public function classHistory()
    {
        return $this->hasMany(StudentClassHistory::class);
    }

    public static function getNextClass($currentHierarchy)
    {
        return self::where('hierarchy', '>', $currentHierarchy)
            ->orderBy('hierarchy')
            ->first();
    }

    public static function getPreviousClass($currentHierarchy)
    {
        return self::where('hierarchy', '<', $currentHierarchy)
            ->orderBy('hierarchy', 'desc')
            ->first();
    }
}
