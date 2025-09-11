<?php
// app/Models/Student.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use App\Enums\TermEnum;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'reg_no',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'parent_name',
        'parent_phone_number',
        'address',
        'parent_occupation',
        'state_of_origin',
        'local_government_area',
        'religion',
        'date_registered',
        'approved',
        'profile_pic',
        'user_id'
    ];

    protected $casts = [
        'date_registered' => 'datetime',
        'date_of_birth' => 'date',
        'approved' => 'boolean',
    ];

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function classHistory()
    {
        return $this->hasMany(StudentClassHistory::class);
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute()
    {
        $names = [$this->first_name];
        if ($this->middle_name) {
            $names[] = $this->middle_name;
        }
        $names[] = $this->last_name;
        return implode(' ', $names);
    }

    public function getParentPhoneNumberAttribute()
    {
        return $this->attributes['parent_phone_number'] ? Crypt::decryptString($this->attributes['parent_phone_number']) : null;
    }

    public function setParentPhoneNumberAttribute($value)
    {
        $this->attributes['parent_phone_number'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getCurrentClass()
    {
        $latestClass = $this->classHistory()->latest('join_date')->first();
        return $latestClass ? $latestClass->class->name : null;
    }

    public function getCurrentEnrollment()
    {
        $currentSession = AcademicSession::getCurrentSession();
        if (!$currentSession) {
            return null;
        }

        return $this->classHistory()
            ->where('session_id', $currentSession->id)
            ->where('is_active', true)
            ->whereNull('leave_date')
            ->latest('join_date')
            ->first();
    }

    public function getClassBySessionAndTerm($sessionId, $term)
    {
        $enrollment = $this->classHistory()
            ->where('session_id', $sessionId)
            ->where('start_term', $term->value)
            ->where('is_active', true)
            ->whereNull('leave_date')
            ->first();

        return $enrollment ? $enrollment->class->name : null;
    }

    public function getClassBySession($sessionYear)
    {
        $session = AcademicSession::where('year', $sessionYear)->first();
        if (!$session) {
            return null;
        }

        $classHistoryEntry = $this->classHistory()
            ->where('session_id', $session->id)
            ->where('is_active', true)
            ->first();

        return $classHistoryEntry ? $classHistoryEntry->class->name : null;
    }

    public static function generateRegNo($sessionYear = null)
    {
        if (!$sessionYear) {
            $currentSession = AcademicSession::getCurrentSessionAndTerm(false);
            $sessionYear = $currentSession ? $currentSession->year : now()->year;
        }

        do {
            $randomSuffix = strtoupper(Str::random(4));
            $regNo = "{$sessionYear}/{$randomSuffix}";
        } while (self::where('reg_no', $regNo)->exists());

        return $regNo;
    }
}
