<?php

namespace App\Http\Controllers\Admin;

use App\Models\AcademicSession;
use App\Models\UserSessionPreference;
use App\Enums\TermEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $sessionChoices = $academicSessions->map(function ($session) {
            return $session;
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

    public function setCurrentSessionForm()
    {
        $this->authorize('manage_sessions');

        $academicSessions = AcademicSession::orderBy('year', 'desc')->get();
        $termChoices = collect(TermEnum::cases())->map(function ($term) {
            return (object) ['value' => $term->value, 'label' => $term->value];
        });

        $currentSessionData = AcademicSession::getCurrentSessionAndTerm(true);
        $currentSession = $currentSessionData[0];
        $currentTerm = $currentSessionData[1];

        return view('admin.set_current_session', compact(
            'academicSessions',
            'termChoices',
            'currentSession',
            'currentTerm'
        ));
    }

    public function setCurrentSession(Request $request)
    {
        $this->authorize('manage_sessions');

        try {
            $validated = $request->validate([
                'session_id' => 'required|exists:academic_sessions,id',
                'current_term' => 'required|in:First,Second,Third',
            ]);

            DB::beginTransaction();

            // Update is_current flag
            AcademicSession::where('is_current', true)->update(['is_current' => false]);
            $session = AcademicSession::findOrFail($validated['session_id']);
            $session->update([
                'is_current' => true,
                'current_term' => $validated['current_term'],
            ]);

            // Update all user preferences to match the system-wide setting
            UserSessionPreference::query()->delete();
            UserSessionPreference::create([
                'user_id' => Auth::id(),
                'session_id' => $session->id,
                'current_term' => $validated['current_term'],
            ]);

            $this->logActivity("Set system-wide current session to {$session->year} ({$validated['current_term']})", [
                'session_id' => $session->id,
                'term' => $validated['current_term'],
            ]);

            DB::commit();

            return redirect()->route('admin.set_current_session')->with('success', "Current session set to {$session->year} ({$validated['current_term']}).");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error setting current session: " . $e->getMessage());
            return back()->with('error', 'Failed to set current session. Please try again.')->withInput();
        }
    }

    public function store(Request $request)
    {
        $this->authorize('manage_sessions');

        try {
            $validated = $request->validate([
                'year' => [
                    'required',
                    'regex:/^\d{4}\/\d{4}$/',
                    'unique:academic_sessions,year',
                    function ($attribute, $value, $fail) {
                        [$startYear, $endYear] = explode('/', $value);
                        if ((int)$endYear !== (int)$startYear + 1) {
                            $fail('The academic year must consist of two consecutive years (e.g., 2025/2026).');
                        }
                    },
                ],
            ]);

            $session = AcademicSession::create([
                'year' => $validated['year'],
                'is_current' => false,
                'current_term' => null,
            ]);

            $this->logActivity("Created academic session {$session->year}", [
                'session_id' => $session->id,
                'year' => $session->year,
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

        $sessionChoices = $academicSessions->map(function ($session) {
            return $session;
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
                'year' => [
                    'required',
                    'regex:/^\d{4}\/\d{4}$/',
                    'unique:academic_sessions,year,' . $id,
                    function ($attribute, $value, $fail) {
                        [$startYear, $endYear] = explode('/', $value);
                        if ((int)$endYear !== (int)$startYear + 1) {
                            $fail('The academic year must consist of two consecutive years (e.g., 2025/2026).');
                        }
                    },
                ],
            ]);

            $session->update([
                'year' => $validated['year'],
            ]);

            $this->logActivity("Updated academic session {$session->year}", [
                'session_id' => $session->id,
                'year' => $session->year,
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

            // Check for related records using the correct column name 'session_id'
            if ($session->classHistory()->exists() || $session->results()->exists() || $session->termSummaries()->exists()) {
                return redirect()->route('admin.manage_academic_sessions')->with('error', 'Cannot delete session with associated student records, results, or summaries.');
            }

            DB::beginTransaction();

            // Delete related user preferences
            UserSessionPreference::where('session_id', $session->id)->delete();

            $year = $session->year;
            $session->delete();

            $this->logActivity("Deleted academic session {$year}", ['session_id' => $id]);

            DB::commit();

            return redirect()->route('admin.manage_academic_sessions')->with('success', "Academic session {$year} deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting academic session: " . $e->getMessage());
            return redirect()->route('admin.manage_academic_sessions')->with('error', 'Failed to delete session. Please try again.');
        }
    }
}
