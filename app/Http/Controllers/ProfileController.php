<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $targetUser = $user; // Default to self

        // Check if assistant is trying to edit another user's profile
        if ($user->isAssistant() && $request->has('user_id')) {
            $targetUser = User::findOrFail($request->get('user_id'));

            // Prevent assistant from editing counselor profiles
            if ($targetUser->isCounselor()) {
                return Redirect::route('profile.edit')
                    ->with('error', 'You cannot edit counselor profiles.');
            }
        }

        $targetUser->fill($request->validated());

        if ($targetUser->isDirty('email')) {
            $targetUser->email_verified_at = null;
        }

        $targetUser->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
