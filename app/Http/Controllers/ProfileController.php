<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Notifications\NewCounselorCreated;
use App\Providers\CacheServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
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
    public function createCounselor(Request $request): RedirectResponse
    {
        $authUser = $request->user();

        // Only counselors may create new counselor accounts
        if (! $authUser || ! $authUser->isCounselor()) {
            return Redirect::route('profile.edit')->with('error', 'Unauthorized.');
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'name_extension' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'max:50'],
        ]);

        // Generate a temporary password for the new counselor
        $tempPassword = substr(bin2hex(random_bytes(6)), 0, 12);

        $newCounselor = User::create(array_merge($data, [
            'role' => 'counselor',
            'password' => Hash::make($tempPassword),
        ]));

        // Send notification/email to the newly created counselor with temp password
        try {
            $newCounselor->notify(new NewCounselorCreated($tempPassword));
        } catch (\Throwable $e) {
            // Do not fail creation if notification fails; just log later
        }

        return Redirect::route('profile.edit')->with('status', 'counselor-created');
    }

    /**
     * Toggle counselor availability status.
     */
    public function toggleCounselorActive(Request $request): RedirectResponse
    {
        $authUser = $request->user();

        // Only counselors may toggle their availability status
        if (!$authUser || !$authUser->isCounselor()) {
            return Redirect::route('profile.edit')->with('error', 'Unauthorized.');
        }

        // Both actions now require confirmation
        try {
            $request->validate([
                'confirm_intent' => 'required|boolean',
                'confirm_text' => 'sometimes|string',
                'final_confirm' => 'required|boolean',
            ]);

            if (!$request->confirm_intent || !$request->final_confirm) {
                throw ValidationException::withMessages([
                    'confirm_intent' => 'Confirmation required.',
                    'confirm_text' => 'Confirmation required.',
                    'final_confirm' => 'Confirmation required.',
                ])->errorBag('counselor_toggle');
            }

            if ($authUser->isAvailable()) {
                // Setting to unavailable: requires text confirmation
                $expectedText = 'SET UNAVAILABLE';
                if (strtoupper($request->confirm_text) !== $expectedText) {
                    throw ValidationException::withMessages([
                        'confirm_text' => 'Confirmation text does not match.',
                    ])->errorBag('counselor_toggle');
                }

                $authUser->availability_status = 'UNAVAILABLE';
                $authUser->save();
                return Redirect::route('profile.edit')->with('status', 'counselor-unavailable');
            } else {
                // Setting back to available: no text confirmation needed, just intent and final confirm
                $authUser->availability_status = 'AVAILABLE';
                $authUser->save();
                return Redirect::route('profile.edit')->with('status', 'counselor-available');
            }
        } catch (ValidationException $e) {
            return Redirect::route('profile.edit')->withErrors($e->errors(), 'counselor_toggle');
        }
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
