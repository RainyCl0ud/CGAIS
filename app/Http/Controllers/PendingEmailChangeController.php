<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PendingEmailChangeController extends Controller
{
    /**
     * Handle pending email change verification
     */
    public function verify(Request $request, $token)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to verify your email.');
        }

        if ($user->verifyPendingEmailChange($token)) {
            return redirect()->route('profile.edit')->with('status', 'email-verified-and-updated');
        }

        return redirect()->route('profile.edit')->with('error', 'Invalid or expired verification link.');
    }

    /**
     * Cancel pending email change
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();
        $user->clearPendingEmailChange();

        return redirect()->route('profile.edit')->with('status', 'pending-email-cancelled');
    }
}