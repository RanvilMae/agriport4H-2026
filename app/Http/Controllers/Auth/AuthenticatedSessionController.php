<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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

        // Check if the user is accepted
        if (!Auth::user()->is_accepted) {
            // Explicitly logout unapproved users
            Auth::guard('web')->logout();

            // Clean up session state
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // FIX: Redirect back with input and errors instead of redirecting to named route
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Your account is pending admin approval. Please contact your Region Coordinator or President.',
                ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}