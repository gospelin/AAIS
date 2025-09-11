<?php
// app/Models/AcademicSession.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TermEnum;
use App\Models\UserSessionPreference;
use Carbon\Carbon;

class AcademicSession extends Model
{
    use HasFactory;

    // protected $table = 'academic_sessions';

    protected $fillable = [
        'year',
        'is_current',
        'current_term'
    ];

    protected $casts = [
        'is_current' => 'boolean',
    ];

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

        if ($preference) {
            $session = self::find($preference->session_id);
            $term = $preference->current_term ? TermEnum::from($preference->current_term) : null;
        } else {
            $legacySession = self::where('is_current', true)->first();

            if ($legacySession) {
                $session = $legacySession;
                $term = $legacySession->current_term ? TermEnum::from($legacySession->current_term) : TermEnum::FIRST;

                $preference = UserSessionPreference::create([
                    'user_id' => auth()->id(),
                    'session_id' => $session->id,
                    'current_term' => $term->value
                ]);
            } else {
                $session = self::orderBy('year', 'desc')->first();
                if (!$session) {
                    return $includeTerm ? [null, null] : null;
                }
                $term = TermEnum::FIRST;

                $preference = UserSessionPreference::create([
                    'user_id' => auth()->id(),
                    'session_id' => $session->id,
                    'current_term' => $term->value
                ]);
            }
        }

        return $includeTerm ? [$session, $term] : $session;
    }

    public function classHistory()
    {
        return $this->hasMany(StudentClassHistory::class);
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
        return $this->hasMany(UserSessionPreference::class);
    }
}
