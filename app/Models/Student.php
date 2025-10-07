<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
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
        'gender' => 'string',
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

    public function getCurrentClass($sessionId = null, $term = null)
    {
        $query = $this->classHistory()->where('is_active', true)->whereNull('leave_date');

        if ($sessionId) {
            $query->where('session_id', $sessionId);
        }

        if ($term) {
            $termValue = $term instanceof TermEnum ? $term->value : $term;
            $query->where('start_term', '<=', $termValue)
                ->where(function ($q) use ($termValue) {
                    $q->whereNull('end_term')
                        ->orWhere('end_term', '>=', $termValue);
                });
        }

        $classHistory = $query->first();
        return $classHistory ? $classHistory->class->name : null;
    }

    public function getCurrentEnrollment()
    {
        $currentSession = AcademicSession::where('is_current', true)->first();
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
        $termValue = $term instanceof TermEnum ? $term->value : $term;
        $enrollment = $this->classHistory()
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->whereNull('leave_date')
            ->where('start_term', '<=', $termValue)
            ->where(function ($q) use ($termValue) {
                $q->whereNull('end_term')
                    ->orWhere('end_term', '>=', $termValue);
            })
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
            ->whereNull('leave_date')
            ->first();

        return $classHistoryEntry ? $classHistoryEntry->class->name : null;
    }

    public static function generateRegNo($sessionYear = null)
    {
        $prefix = config('app.reg_no_prefix', 'AAIS/0559/');
        $maxRetries = 10;
        $retryCount = 0;

        $lastStudent = self::orderBy('id', 'desc')->first();
        $studentId = $lastStudent ? $lastStudent->id + 1 : 1;

        do {
            $paddedId = str_pad($studentId, 3, '0', STR_PAD_LEFT);
            $regNo = "{$prefix}{$paddedId}";

            if (!self::where('reg_no', $regNo)->exists()) {
                return $regNo;
            }

            $studentId++;
            $retryCount++;

            if ($retryCount >= $maxRetries) {
                throw new \Exception('Unable to generate unique registration number after multiple attempts.');
            }
        } while (true);
    }
}
