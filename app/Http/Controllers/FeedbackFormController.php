<?php

namespace App\Http\Controllers;

use App\Models\FeedbackForm;
use App\Models\Appointment;
use App\Models\DocumentCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class FeedbackFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $feedbackForms = $user->feedbackForms()->with(['counselor', 'appointment'])->latest()->paginate(10);
        
        return view('feedback.index', compact('feedbackForms'));
    }



    public function downloadPdf()
    {
        try {
            // Prepare logos (from public path)
            $logos = [];
            $logoPath = public_path('storage/ustp.png');
            if (file_exists($logoPath)) {
                $contents = file_get_contents($logoPath);
                $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents);
                $logos['logo'] = 'data:' . ($mime ?: 'image/png') . ';base64,' . base64_encode($contents);
            }

            // Get document code
            $documentCode = DocumentCode::first();

            $data = [
                'logos' => $logos,
                'documentCode' => $documentCode,
            ];

            $pdf = Pdf::loadView('pdfs.feedback', $data)->setPaper([0, 0, 612, 1008], 'portrait'); // Custom size: 8.5" x 14" (legal size)

            return $pdf->stream('feedback_form.pdf');
        } catch (\Throwable $e) {
            Log::error('Failed to generate feedback PDF: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'PDF generation failed'], 500);
        }
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