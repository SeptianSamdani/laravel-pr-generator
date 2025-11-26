<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function show()
    {
        return view('auth.login');
    }

    /**
     * Handle login attempt
     */
    public function login(Request $request)
    {
        // Rate limiting key
        $key = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        // Check if too many attempts
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        // Validate input
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $remember = $request->boolean('remember');

        // Attempt login with active user check
        if (Auth::attempt(array_merge($credentials, ['is_active' => true]), $remember)) {
            // Clear rate limiter
            RateLimiter::clear($key);
            
            // Regenerate session
            $request->session()->regenerate();

            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('User logged in');

            return redirect()->intended(route('dashboard'));
        }

        // Increment rate limiter
        RateLimiter::hit($key, 300); // 5 minutes decay

        // Authentication failed
        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->log('User logged out');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}