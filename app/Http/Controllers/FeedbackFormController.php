<?php

namespace App\Http\Controllers;

use App\Models\FeedbackForm;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('student');
    }

    public function index()
    {
        $user = Auth::user();
        $feedbackForms = $user->feedbackForms()->with(['counselor', 'appointment'])->latest()->paginate(10);
        
        return view('feedback.index', compact('feedbackForms'));
    }

    public function create()
    {
        $user = Auth::user();
        $appointments = $user->appointments()->where('status', 'completed')->get();
        $counselors = \App\Models\User::where('role', 'counselor')->get();
        
        return view('feedback.create', compact('appointments', 'counselors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'counselor_id' => 'nullable|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'counselor_rating' => 'nullable|integer|min:1|max:5',
            'service_rating' => 'nullable|integer|min:1|max:5',
            'facility_rating' => 'nullable|integer|min:1|max:5',
            'overall_satisfaction' => 'nullable|integer|min:1|max:5',
            'counselor_feedback' => 'nullable|string|max:1000',
            'service_feedback' => 'nullable|string|max:1000',
            'suggestions' => 'nullable|string|max:1000',
            'concerns' => 'nullable|string|max:1000',
            'would_recommend' => 'nullable|boolean',
            'recommendation_reason' => 'nullable|string|max:1000',
            'additional_comments' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        
        // If appointment_id is provided, get counselor from appointment
        if ($request->appointment_id) {
            $appointment = Appointment::find($request->appointment_id);
            if ($appointment && $appointment->user_id === $user->id) {
                $validated['counselor_id'] = $appointment->counselor_id;
            }
        }

        $validated['user_id'] = $user->id;
        
        FeedbackForm::create($validated);

        return redirect()->route('feedback.index')
            ->with('success', 'Feedback submitted successfully.');
    }

    public function show(FeedbackForm $feedbackForm)
    {
        $this->authorize('view', $feedbackForm);
        
        return view('feedback.show', compact('feedbackForm'));
    }

    public function edit(FeedbackForm $feedbackForm)
    {
        $this->authorize('update', $feedbackForm);
        
        $user = Auth::user();
        $appointments = $user->appointments()->where('status', 'completed')->get();
        $counselors = \App\Models\User::where('role', 'counselor')->get();
        
        return view('feedback.edit', compact('feedbackForm', 'appointments', 'counselors'));
    }

    public function update(Request $request, FeedbackForm $feedbackForm)
    {
        $this->authorize('update', $feedbackForm);
        
        $validated = $request->validate([
            'counselor_id' => 'nullable|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'counselor_rating' => 'nullable|integer|min:1|max:5',
            'service_rating' => 'nullable|integer|min:1|max:5',
            'facility_rating' => 'nullable|integer|min:1|max:5',
            'overall_satisfaction' => 'nullable|integer|min:1|max:5',
            'counselor_feedback' => 'nullable|string|max:1000',
            'service_feedback' => 'nullable|string|max:1000',
            'suggestions' => 'nullable|string|max:1000',
            'concerns' => 'nullable|string|max:1000',
            'would_recommend' => 'nullable|boolean',
            'recommendation_reason' => 'nullable|string|max:1000',
            'additional_comments' => 'nullable|string|max:1000',
        ]);

        $feedbackForm->update($validated);

        return redirect()->route('feedback.index')
            ->with('success', 'Feedback updated successfully.');
    }

    public function destroy(FeedbackForm $feedbackForm)
    {
        $this->authorize('delete', $feedbackForm);
        
        $feedbackForm->delete();

        return redirect()->route('feedback.index')
            ->with('success', 'Feedback deleted successfully.');
    }
} 