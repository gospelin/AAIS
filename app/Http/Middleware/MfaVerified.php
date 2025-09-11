<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MfaVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            Log::warning('Unauthorized access attempt to MFA-protected route.');
            return redirect()->route('login')->withErrors(['error' => 'Please login first.']);
        }

        $user = Auth::user();
        if ($user->hasRole('admin') && $user->mfa_secret && !session('mfa_verified', false)) {
            Log::info('MFA verification required for admin: ' . $user->username);
            return redirect()->route('mfa.verify');
        }

        return $next($request);
    }
}
