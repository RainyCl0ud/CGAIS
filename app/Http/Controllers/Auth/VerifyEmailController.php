<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('verification.notice')->withErrors(['token' => 'Invalid verification link.']);
        }

        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('verification.notice')->withErrors(['token' => 'Invalid or expired verification token.']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($user->verifyEmailWithToken($token)) {
            event(new Verified($user));

            // If the user is not logged in, log them in
            if (!Auth::check()) {
                Auth::login($user);
                $request->session()->regenerate();
            }

            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        return redirect()->route('verification.notice')->withErrors(['token' => 'Email verification failed.']);
    }
}
