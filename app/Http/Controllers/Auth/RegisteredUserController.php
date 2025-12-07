<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\AuthorizedId;
use App\Notifications\CustomVerifyEmail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'name_extension' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:student,faculty,staff'],
            'student_id' => ['required_if:role,student', 'nullable', 'string', 'unique:users,student_id', 'exclude_unless:role,student'],
            'faculty_id' => ['required_if:role,faculty', 'nullable', 'string', 'unique:users,faculty_id', 'exclude_unless:role,faculty'],
            'staff_id' => ['required_if:role,staff', 'nullable', 'string', 'unique:users,staff_id', 'exclude_unless:role,staff'],
            'course_category' => ['required_if:role,student', 'nullable', 'in:BSIT,BTLED,BAT,BSA', 'exclude_unless:role,student'],
            'year_level' => ['required_if:role,student', 'nullable', 'in:1st Year,2nd Year,3rd Year,4th Year,5th Year', 'exclude_unless:role,student'],
        ]);

        // Pre-approved ID check using AuthorizedId system
        if ($request->role === 'student') {
            $authorizedId = AuthorizedId::where('id_number', $request->student_id)
                ->where('type', 'student')
                ->where('is_used', false)
                ->first();
            if (!$authorizedId) {
                return back()->withErrors(['student_id' => 'This Student ID is not authorized or has already been used. Please contact the counselor.'])->withInput();
            }
        } elseif ($request->role === 'faculty') {
            $authorizedId = AuthorizedId::where('id_number', $request->faculty_id)
                ->where('type', 'faculty')
                ->where('is_used', false)
                ->first();
            if (!$authorizedId) {
                return back()->withErrors(['faculty_id' => 'This Faculty ID is not authorized or has already been used. Please contact the counselor.'])->withInput();
            }
        } elseif ($request->role === 'staff') {
            $authorizedId = AuthorizedId::where('id_number', $request->staff_id)
                ->where('type', 'staff')
                ->where('is_used', false)
                ->first();
            if (!$authorizedId) {
                return back()->withErrors(['staff_id' => 'This Staff ID is not authorized or has already been used. Please contact the counselor.'])->withInput();
            }
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'name_extension' => $request->name_extension,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'student_id' => $request->role === 'student' ? $request->student_id : null,
            'faculty_id' => $request->role === 'faculty' ? $request->faculty_id : null,
            'staff_id' => $request->role === 'staff' ? $request->staff_id : null,
            'course_category' => $request->role === 'student' ? $request->course_category : null,
            'year_level' => $request->role === 'student' ? $request->year_level : null,
        ]);

        // Mark authorized ID as used
        if (isset($authorizedId)) {
            $authorizedId->markAsUsed($user->id);
        }

        event(new Registered($user));

        // Send custom email verification notification
        $user->notify(new CustomVerifyEmail());

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
