<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validateWithBag('updatePassword', [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            // Update the password
            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            // Log the password change activity
            if (method_exists($request->user(), 'logActivity')) {
                $request->user()->logActivity('password_updated', 'User updated their password');
            }

            return back()->with('status', 'password-updated');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are automatically handled by Laravel
            // The errors will be available in the updatePassword error bag
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return back()
                ->withErrors(['general' => 'An unexpected error occurred. Please try again.'])
                ->withInput();
        }
    }
}
