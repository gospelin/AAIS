<?php
// app/Models/FeePayment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'session_id',
        'term',
        'has_paid_fee'
    ];

    protected $casts = [
        'has_paid_fee' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
