<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserManagementController extends Controller
{
    public function __construct()
    {
        // Allow both counselors and assistants to access user management
        // Assistants will have view-only access enforced in individual methods
        $this->middleware('counselor_or_assistant');
    }

    /**
     * Check if current user can modify users (counselors only)
     */
    private function canModifyUsers(): bool
    {
        return auth()->user()->isCounselor();
    }

    /**
     * Check if current user can view users (counselors and assistants)
     */
    private function canViewUsers(): bool
    {
        return auth()->user()->isCounselor() || auth()->user()->isAssistant();
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('faculty_id', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('user-management.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        if (!$this->canModifyUsers()) {
            abort(403, 'Access denied. Only counselors can create users.');
        }
        
        return view('user-management.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!$this->canModifyUsers()) {
            abort(403, 'Access denied. Only counselors can create users.');
        }
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'name_extension' => 'nullable|string|max:10',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:student,faculty,staff',
            'student_id' => 'nullable|string|max:255|unique:users',
            'faculty_id' => 'nullable|string|max:255|unique:users',
            'staff_id' => 'nullable|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        return view('user-management.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        if (!$this->canModifyUsers()) {
            abort(403, 'Access denied. Only counselors can edit users.');
        }
        
        return view('user-management.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        if (!$this->canModifyUsers()) {
            abort(403, 'Access denied. Only counselors can update users.');
        }

        $currentUser = auth()->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'name_extension' => 'nullable|string|max:10',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:student,faculty,staff',
            'student_id' => 'nullable|string|max:255|unique:users,student_id,' . $user->id,
            'faculty_id' => 'nullable|string|max:255|unique:users,faculty_id,' . $user->id,
            'staff_id' => 'nullable|string|max:255|unique:users,staff_id,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (!$this->canModifyUsers()) {
            abort(403, 'Access denied. Only counselors can delete users.');
        }

        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
