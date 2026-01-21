<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CourseController extends Controller
{
    public function __construct()
    {
        // Allow both counselors and assistants to access course management
        // Assistants will have view-only access enforced in individual methods
        $this->middleware('counselor_or_assistant');
    }

    /**
     * Check if current user can modify courses (counselors only)
     */
    private function canModifyCourses(): bool
    {
        return auth()->user()->isCounselor();
    }

    /**
     * Check if current user can view courses (counselors and assistants)
     */
    private function canViewCourses(): bool
    {
        return auth()->user()->isCounselor() || auth()->user()->isAssistant();
    }

    /**
     * Display a listing of courses.
     */
    public function index(Request $request): View
    {
        $query = Course::query();

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('name')->get();

        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): View
    {
        if (!$this->canModifyCourses()) {
            abort(403, 'Access denied. Only counselors can create courses.');
        }

        return view('courses.create');
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!$this->canModifyCourses()) {
            abort(403, 'Access denied. Only counselors can create courses.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Course::create($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course): View
    {
        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): View
    {
        if (!$this->canModifyCourses()) {
            abort(403, 'Access denied. Only counselors can edit courses.');
        }

        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        if (!$this->canModifyCourses()) {
            abort(403, 'Access denied. Only counselors can update courses.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code,' . $course->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Toggle the active status of the specified course.
     */
    public function toggle(Course $course): RedirectResponse
    {
        if (!$this->canModifyCourses()) {
            abort(403, 'Access denied. Only counselors can modify courses.');
        }

        $course->update(['is_active' => !$course->is_active]);

        $status = $course->is_active ? 'activated' : 'deactivated';

        return redirect()->route('courses.index')
            ->with('success', "Course {$status} successfully.");
    }
}
