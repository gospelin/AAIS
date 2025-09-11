<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the student/staff login view.
     */
    public function create(): View
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * Display the admin login view.
     */
    public function adminLogin(): View
    {
        if (Auth::check()) {
            if (Auth::user()->hasRole('admin') && Auth::user()->mfa_secret && !session('mfa_verified', false)) {
                return redirect()->route('mfa.verify');
            }
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.admin_login');
    }

    /**
     * Handle student/staff login.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $key = 'login|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            Log::warning('Rate limit exceeded for login attempt from IP: ' . $request->ip());
            return back()->withErrors(['error' => 'Too many login attempts. Please try again later.']);
        }

        $studentIdRegex = '/^AAIS\/0559\/\d{3}$/';
        $isStudent = preg_match($studentIdRegex, $request->identifier);
        $field = $isStudent ? 'identifier' : 'username';
        $user = User::where($field, $request->identifier)->first();

        if (!$user) {
            RateLimiter::increment($key, 60);
            Log::warning('Login attempt with non-existent ' . $field . ': ' . $request->identifier);
            return back()->withErrors(['error' => 'Invalid credentials.']);
        }

        if (!$user->hasAnyRole(['student', 'staff'])) {
            RateLimiter::increment($key, 60);
            Log::warning('Invalid role for ' . $field . ': ' . $request->identifier);
            return back()->withErrors(['error' => 'Invalid role.']);
        }

        if (!$user->active) {
            RateLimiter::increment($key, 60);
            Log::warning('Inactive account login attempt: ' . $request->identifier);
            return back()->withErrors(['error' => 'Account is inactive. Contact admin.']);
        }

        if (Auth::attempt([$field => $request->identifier, 'password' => $request->password], $request->filled('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            AuditLog::create(['user_id' => $user->id, 'action' => 'Logged in']);
            Log::info(($isStudent ? 'Student' : 'Staff') . ' ' . $user->$field . ' logged in successfully.');
            return $this->redirectBasedOnRole($user);
        }

        RateLimiter::increment($key, 60);
        Log::warning('Failed login attempt for ' . $field . ': ' . $request->identifier);
        return back()->withErrors(['error' => 'Invalid credentials.']);
    }

    /**
     * Handle admin login.
     */
    public function adminStore(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'min:3'],
            'password' => ['required', 'string'],
        ]);

        $key = 'admin_login|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            Log::warning('Rate limit exceeded for admin login attempt from IP: ' . $request->ip());
            return back()->withErrors(['error' => 'Too many login attempts. Please try again later.']);
        }

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            RateLimiter::increment($key, 60);
            Log::warning('Admin login attempt with non-existent username: ' . $request->username);
            return back()->withErrors(['error' => 'Invalid credentials.']);
        }

        if (!$user->hasRole('admin')) {
            RateLimiter::increment($key, 60);
            Log::warning('Invalid role for username: ' . $request->username);
            return back()->withErrors(['error' => 'Invalid role.']);
        }

        if (!$user->active) {
            RateLimiter::increment($key, 60);
            Log::warning('Inactive admin account login attempt: ' . $request->username);
            return back()->withErrors(['error' => 'Account is inactive. Contact admin.']);
        }

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->filled('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            AuditLog::create(['user_id' => $user->id, 'action' => 'Logged in']);
            Log::info('Admin ' . $user->username . ' logged in successfully.');

            if ($user->mfa_secret) {
                return redirect()->route('mfa.verify');
            }

            return $this->redirectBasedOnRole($user);
        }

        RateLimiter::increment($key, 60);
        Log::warning('Failed admin login attempt for username: ' . $request->username);
        return back()->withErrors(['error' => 'Invalid credentials.']);
    }

    /**
     * Display MFA setup view.
     */
    public function showMfaSetup(): View
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            Auth::logout();
            session()->flush();
            Log::warning('Unauthorized access to MFA setup page.');
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access.']);
        }

        $google2fa = new Google2FA();
        if (!$user->mfa_secret) {
            $mfaSecret = $google2fa->generateSecretKey();
            $user->update(['mfa_secret' => $mfaSecret]);
            Log::info('Generated new MFA secret for admin: ' . $user->username);
        } else {
            $mfaSecret = $user->mfa_secret;
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->username,
            $mfaSecret
        );

        Log::debug('MFA Setup - User: ' . $user->username . ', Secret: ' . $mfaSecret . ', QR URL: ' . $qrCodeUrl);

        return view('auth.mfa_setup', [
            'qrCodeUrl' => $qrCodeUrl,
            'mfaSecret' => $mfaSecret
        ]);
    }

    /**
     * Handle MFA setup verification.
     */
    public function verifyMfaSetup(Request $request): RedirectResponse
    {
        $request->validate([
            'mfa_code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Log::warning('Unauthorized MFA setup verification attempt.');
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access.']);
        }

        $google2fa = new Google2FA();
        if ($google2fa->verifyKey($user->mfa_secret, $request->mfa_code)) {
            $request->session()->put('mfa_verified', true);
            AuditLog::create(['user_id' => $user->id, 'action' => 'MFA setup verified']);
            Log::info('MFA setup verified for admin: ' . $user->username);
            return redirect()->route('admin.dashboard')->with('status', 'MFA setup completed successfully.');
        }

        Log::warning('Invalid MFA code during setup for admin: ' . $user->username);
        return back()->withErrors(['mfa_code' => 'Invalid MFA code.']);
    }

    /**
     * Handle MFA verification.
     */
    public function verifyMfa(Request $request): RedirectResponse
    {
        $request->validate([
            'mfa_code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Log::warning('Unauthorized MFA verification attempt.');
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access.']);
        }

        $google2fa = new Google2FA();
        if ($google2fa->verifyKey($user->mfa_secret, $request->mfa_code)) {
            $request->session()->put('mfa_verified', true);
            AuditLog::create(['user_id' => $user->id, 'action' => 'MFA verified']);
            Log::info('MFA verified for admin: ' . $user->username);
            return $this->redirectBasedOnRole($user);
        }

        Log::warning('Invalid MFA code for admin: ' . $user->username);
        return back()->withErrors(['error' => 'Invalid MFA code.']);
    }

    /**
     * Display MFA verification view.
     */
    public function showMfaForm(): View
    {
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            Auth::logout();
            session()->flush();
            Log::warning('Unauthorized access to MFA verification page.');
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access.']);
        }
        return view('auth.mfa_verify');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::create(['user_id' => $user->id, 'action' => 'Logged out']);
            Log::info('User ' . ($user->username ?? $user->identifier) . ' logged out.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out.');
    }

    /**
     * Redirect based on user role.
     */
    protected function redirectBasedOnRole($user): RedirectResponse
    {
        if ($user->hasRole('admin') && !$user->mfa_secret) {
            return redirect()->route('mfa.setup');
        }
        if ($user->hasRole('admin') && $user->mfa_secret && !session('mfa_verified', false)) {
            return redirect()->route('mfa.verify');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('staff')) {
            return redirect()->route('staff.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    }
}
