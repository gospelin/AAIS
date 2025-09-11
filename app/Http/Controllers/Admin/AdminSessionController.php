<?php

namespace App\Http\Controllers\Admin;

use App\Models\AcademicSession;
use App\Models\UserSessionPreference;
use App\Enums\TermEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminSessionController extends AdminBaseController
{
    use AuthorizesRequests;

    public function manageSessions(Request $request)
    {
        $this->authorize('manage_sessions');

        $academicSessions = AcademicSession::orderBy('year', 'desc')->get();
        $currentSessionData = AcademicSession::getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        // Handle setting current session/term preference (separate from create/edit)
        if ($request->isMethod('post') && $request->has('session_id') && $request->has('current_term')) {
            try {
                $validated = $request->validate([
                    'session_id' => 'required|exists:academic_sessions,id',
                    'current_term' => 'required|in:First,Second,Third',
                ]);

                $termEnum = TermEnum::from($validated['current_term']);

                $preference = UserSessionPreference::where('user_id', Auth::id())->first();
                $selectedSession = AcademicSession::find($validated['session_id']);

                if (!$selectedSession) {
                    return back()->with('error', 'Selected session does not exist.');
                }

                if ($preference) {
                    $preference->update([
                        'session_id' => $validated['session_id'],
                        'current_term' => $termEnum->value,
                    ]);
                } else {
                    UserSessionPreference::create([
                        'user_id' => Auth::id(),
                        'session_id' => $validated['session_id'],
                        'current_term' => $termEnum->value,
                    ]);
                }

                $this->logActivity("Set session to {$selectedSession->year} ({$termEnum->value})", [
                    'session_id' => $selectedSession->id,
                    'term' => $termEnum->value
                ]);

                return back()->with('success', "Your current session set to {$selectedSession->year} ({$termEnum->value}).");
            } catch (ValidationException $e) {
                return back()->withErrors($e->errors());
            } catch (\Exception $e) {
                Log::error("Database error updating session preference: " . $e->getMessage());
                return back()->with('error', 'Database error occurred. Please try again.');
            }
        }

        // Pass full model instances for $sessionChoices (not limited stdClass objects)
        $sessionChoices = $academicSessions->map(function ($session) {
            return $session; // Full AcademicSession instance
        });

        $termChoices = collect(TermEnum::cases())->map(function ($term) {
            return (object) ['value' => $term->value, 'label' => $term->value];
        });

        return view('admin.manage_academic_sessions', compact(
            'sessionChoices',
            'termChoices',
            'currentSession',
            'currentTerm'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('manage_sessions');

        try {
            $validated = $request->validate([
                'year' => 'required|regex:/^\d{4}\/\d{4}$/|unique:academic_sessions,year',
                'is_current' => 'boolean',
                'current_term' => 'nullable|in:First,Second,Third',
            ]);

            if ($validated['is_current']) {
                AcademicSession::where('is_current', true)->update(['is_current' => false]);
            }

            $session = AcademicSession::create([
                'year' => $validated['year'],
                'is_current' => $validated['is_current'] ?? false,
                'current_term' => $validated['current_term'] ?? null,
            ]);

            $this->logActivity("Created academic session {$session->year}", [
                'session_id' => $session->id,
                'year' => $session->year,
                'is_current' => $session->is_current,
                'current_term' => $session->current_term,
            ]);

            return redirect()->route('admin.manage_academic_sessions')->with('success', "Academic session {$session->year} created successfully.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error creating academic session: " . $e->getMessage());
            return back()->with('error', 'Failed to create session. Please try again.')->withInput();
        }
    }

    public function edit($id)
    {
        $this->authorize('manage_sessions');

        $editSession = AcademicSession::findOrFail($id);
        $academicSessions = AcademicSession::orderBy('year', 'desc')->get();
        $currentSessionData = AcademicSession::getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        // Pass full model instances for $sessionChoices
        $sessionChoices = $academicSessions->map(function ($session) {
            return $session; // Full AcademicSession instance
        });

        $termChoices = collect(TermEnum::cases())->map(function ($term) {
            return (object) ['value' => $term->value, 'label' => $term->value];
        });

        return view('admin.manage_academic_sessions', compact(
            'sessionChoices',
            'termChoices',
            'currentSession',
            'currentTerm',
            'editSession'
        ));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('manage_sessions');

        try {
            $session = AcademicSession::findOrFail($id);

            $validated = $request->validate([
                'year' => 'required|regex:/^\d{4}\/\d{4}$/|unique:academic_sessions,year,' . $id,
                'is_current' => 'boolean',
                'current_term' => 'nullable|in:First,Second,Third',
            ]);

            if ($validated['is_current']) {
                AcademicSession::where('is_current', true)->update(['is_current' => false]);
            }

            $session->update([
                'year' => $validated['year'],
                'is_current' => $validated['is_current'] ?? false,
                'current_term' => $validated['current_term'] ?? null,
            ]);

            $this->logActivity("Updated academic session {$session->year}", [
                'session_id' => $session->id,
                'year' => $session->year,
                'is_current' => $session->is_current,
                'current_term' => $session->current_term,
            ]);

            return redirect()->route('admin.manage_academic_sessions')->with('success', "Academic session {$session->year} updated successfully.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error updating academic session: " . $e->getMessage());
            return back()->with('error', 'Failed to update session. Please try again.')->withInput();
        }
    }

    public function delete($id)
    {
        $this->authorize('manage_sessions');

        $session = AcademicSession::findOrFail($id);
        return view('admin.delete_session', compact('session'));
    }

    public function destroy($id)
    {
        $this->authorize('manage_sessions');

        try {
            $session = AcademicSession::findOrFail($id);

            if ($session->is_current) {
                return redirect()->route('admin.manage_academic_sessions')->with('error', 'Cannot delete the current academic session.');
            }

            if ($session->classHistory()->exists() || $session->results()->exists() || $session->termSummaries()->exists()) {
                return redirect()->route('admin.manage_academic_sessions')->with('error', 'Cannot delete session with associated student records, results, or summaries.');
            }

            $year = $session->year;
            $session->delete();

            $this->logActivity("Deleted academic session {$year}", ['session_id' => $id]);

            return redirect()->route('admin.manage_academic_sessions')->with('success', "Academic session {$year} deleted successfully.");
        } catch (\Exception $e) {
            Log::error("Error deleting academic session: " . $e->getMessage());
            return redirect()->route('admin.manage_academic_sessions')->with('error', 'Failed to delete session. Please try again.');
        }
    }
}
