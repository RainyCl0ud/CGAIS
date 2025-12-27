<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Providers\CacheServiceProvider;
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

        // Handle email changes separately
        $emailChanged = false;
        $newEmail = $request->get('email');
        
        if ($targetUser->email !== $newEmail) {
            $emailChanged = true;
            
            // Generate pending email change
            $token = $targetUser->generatePendingEmailChange($newEmail);
            
            // Send verification email to the new email address
            $verificationUrl = route('pending-email.verify', $token);
            $targetUser->notify(new \App\Notifications\PendingEmailChangeNotification($newEmail, $verificationUrl));
        }

        // Update other profile information (excluding email if it was changed)
        $profileData = $request->validated();
        if ($emailChanged) {
            unset($profileData['email']); // Don't update email yet, wait for verification
        }
        $targetUser->fill($profileData);

        $targetUser->save();

        // Clear cache to ensure changes are visible immediately
        CacheServiceProvider::clearRelatedCaches('User', $targetUser->id);

        if ($emailChanged) {
            return Redirect::route('profile.edit')->with('status', 'email-change-pending');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Create a new counselor account.
     */
    // Counselor creation feature removed.

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
