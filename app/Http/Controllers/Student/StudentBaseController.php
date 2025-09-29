<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Student;
use App\Enums\TermEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentBaseController extends Controller
{
    /**
     * Get the authenticated student's data.
     *
     * @return Student|null
     */
    protected function getAuthenticatedStudent()
    {
        try {
            return Student::where('user_id', Auth::id())->firstOrFail();
        } catch (\Exception $e) {
            Log::error("Error fetching authenticated student: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the current academic session and term.
     *
     * @param bool $required
     * @return array [session, term]
     */
    protected function getCurrentSessionAndTerm($required = false)
    {
        try {
            [$session, $term] = AcademicSession::getCurrentSessionAndTerm(true);

            if ($required && (!$session || !$term)) {
                throw new \Exception('No current session or term set.');
            }

            return [$session, $term];
        } catch (\Exception $e) {
            Log::error("Error fetching current session and term: " . $e->getMessage());
            return [null, null];
        }
    }
}
