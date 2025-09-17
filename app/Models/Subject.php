<?php
// app/Models/Subject.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'section',
        'deactivated'
    ];

    protected $casts = [
        'deactivated' => 'boolean',
        'name' => 'string',
        'section' => 'string',
    ];

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_subject')
            ->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject')
            ->withTimestamps();
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
