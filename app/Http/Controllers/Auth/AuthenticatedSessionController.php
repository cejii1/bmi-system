<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\AuditLog;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Check email verification
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Please verify your email address first. Check your inbox for the verification link.',
            ]);
        }

        // Check admin approval (skip for admin users)
        if (!$user->isAdmin() && !$user->isApproved()) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account is pending admin approval. Please wait for an administrator to approve your account.',
            ]);
        }

        $request->session()->regenerate();

        AuditLog::log('logged_in', "{$user->name} logged in", $user);

        return redirect()->intended(route('dashboard', absolute: false))
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::log('logged_out', "{$user->name} logged out", $user);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out successfully.');
    }
}
