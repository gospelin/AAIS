<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TermEnum;
use App\Models\UserSessionPreference;
use Carbon\Carbon;

class AcademicSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'is_current',
        'current_term'
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'current_term' => 'string',
    ];

    /**
     * Get the next session's year in YYYY/YYYY format.
     *
     * @return string|null
     */
    public function getNextSessionYear()
    {
        if (!preg_match('/^\d{4}\/\d{4}$/', $this->year)) {
            return null; // Invalid format
        }
        $yearParts = explode('/', $this->year);
        $endYear = (int) $yearParts[1];
        return ($endYear) . '/' . ($endYear + 1);
    }

    /**
     * Get the next academic session.
     *
     * @return AcademicSession|null
     */
    public function getNextSession()
    {
        $nextYear = $this->getNextSessionYear();
        if (!$nextYear) {
            return null;
        }
        return self::where('year', $nextYear)->first();
    }

    // Rest of the model (getCurrentSession, getCurrentSessionAndTerm, relationships) remains unchanged
    public static function getCurrentSession()
    {
        return self::where('is_current', true)->first();
    }

    public static function getCurrentSessionAndTerm($includeTerm = false)
    {
        if (!auth()->check()) {
            return $includeTerm ? [null, null] : null;
        }

        $preference = UserSessionPreference::where('user_id', auth()->id())->first();

        if ($preference && $session = self::find($preference->session_id)) {
            $term = $preference->current_term ? TermEnum::from($preference->current_term) : null;
        } else {
            $session = self::where('is_current', true)->first();

            if (!$session) {
                $session = self::orderBy('year', 'desc')->first();
                $term = $session ? TermEnum::FIRST : null;

                if ($session && auth()->check()) {
                    UserSessionPreference::create([
                        'user_id' => auth()->id(),
                        'session_id' => $session->id,
                        'current_term' => $term ? $term->value : null,
                    ]);
                }
            } else {
                $term = $session->current_term ? TermEnum::from($session->current_term) : TermEnum::FIRST;

                if (auth()->check()) {
                    UserSessionPreference::create([
                        'user_id' => auth()->id(),
                        'session_id' => $session->id,
                        'current_term' => $term->value,
                    ]);
                }
            }
        }

        return $includeTerm ? [$session, $term] : $session;
    }

    public function classHistory()
    {
        return $this->hasMany(StudentClassHistory::class, 'session_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function termSummaries()
    {
        return $this->hasMany(StudentTermSummary::class);
    }

    public function userPreferences()
    {
        return $this->hasMany(UserSessionPreference::class, 'session_id');
    }
}
