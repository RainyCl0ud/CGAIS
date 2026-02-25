<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Schedule;
use App\Models\CounselorUnavailableDate;
use App\Models\Service;
use Illuminate\View\View;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Notifications\AppointmentReminder;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentAppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Ensure only students, faculty, and Non-Teaching Staff can access this
        if (!$user->canBookAppointments()) {
            abort(403, 'Access denied. This page is for students, faculty, and Non-Teaching Staff only.');
        }

        // Get active services (counseling categories created by counselors)
        $services = Service::where('is_active', true)->orderBy('name')->get();

        // Start with base query for user's appointments
        $query = $user->appointments()
            ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc');

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply category filter (filtering by counseling_category instead of type)
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('counseling_category', $request->category);
        }

        $appointments = $query->paginate(10)->withQueryString();

        return view('student.appointments.index', compact('appointments', 'services'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();

        // Ensure only students, faculty, and Non-Teaching Staff can access this
        if (!$user->canBookAppointments()) {
            abort(403, 'Access denied. This page is for students, faculty, and Non-Teaching Staff only.');
        }

        // Get available counselors
        $counselors = User::where('role', 'counselor')->where('is_active', true)->get();

        // Active services (counseling categories)
        $services = Service::where('is_active', true)->orderBy('name')->get();
        
        // Get next 30 days of available dates
        $availableDates = $this->getAvailableDates();
        
        return view('student.appointments.create', compact('counselors', 'availableDates', 'services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Ensure only students, faculty, and Non-Teaching Staff can access this
        if (!$user->canBookAppointments()) {
            abort(403, 'Access denied. This page is for students, faculty, and Non-Teaching Staff only.');
        }

        // Validation rules for appointment creation
        $validationRules = [
            'counselor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ];

        // Type validation differs by user role:
        // Students can select regular, urgent, or follow_up
        // Faculty and Non-Teaching Staff can only select regular or urgent
        if ($user->isStudent()) {
            $validationRules['type'] = 'required|in:regular,urgent,follow_up';
        } else {
            $validationRules['type'] = 'required|in:regular,urgent';
        }

        // Counseling category: accept either a service slug or a numeric service ID.
        // We'll validate/map it manually after base validation so both forms are accepted.
        if ($user->isStudent()) {
            $validationRules['counseling_category'] = 'required';
        } else {
            $validationRules['counseling_category'] = 'sometimes|nullable';
        }

$validationRules['reason'] = 'required_if:type,urgent|nullable|string|max:500';
$validationRules['notes'] = 'nullable|string|max:1000';

        try {
            $validatedData = $request->validate($validationRules);

            // Manual validation for end_time after start_time
            if ($request->start_time >= $request->end_time) {
                return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Appointment validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return back()->withErrors($e->errors())->withInput();
        }

        // Normalize counseling_category: accept numeric service ID or service slug.
        $rawCategory = $request->input('counseling_category');
        $resolvedCategory = null;
        if ($rawCategory !== null && $rawCategory !== '') {
            // normalize common synonyms (e.g. 'counseling' -> 'counseling_services')
            $rawLower = strtolower(trim($rawCategory));
            $synonymMap = [
                'counseling' => 'counseling_services',
                'counseling service' => 'counseling_services',
                'info' => 'information_services',
                'information' => 'information_services',
                'referral' => 'internal_referral_services',
                'intake' => 'conduct_intake_interview',
                'exit' => 'conduct_exit_interview',
                'consult' => 'consultation',
            ];
            if (array_key_exists($rawLower, $synonymMap)) {
                $rawCategory = $synonymMap[$rawLower];
            }

            if (is_numeric($rawCategory)) {
                $service = Service::find(intval($rawCategory));
                if ($service) {
                    $resolvedCategory = $service->slug;
                } else {
                    return back()->withErrors(['counseling_category' => 'Selected counseling category is invalid.'])->withInput();
                }
            } else {
                $service = Service::where('slug', $rawCategory)->first();
                if ($service) {
                    $resolvedCategory = $service->slug;
                } else {
                    // Allow certain administrative default slugs (e.g. 'consultation') if present in enum
                    if (in_array($rawCategory, ['consultation','conduct_intake_interview','information_services','internal_referral_services','counseling_services','conduct_exit_interview'])) {
                        $resolvedCategory = $rawCategory;
                    } else {
                        return back()->withErrors(['counseling_category' => 'Selected counseling category is invalid.'])->withInput();
                    }
                }
            }
        } else {
            // For non-students, default to 'consultation'
            if (!$user->isStudent()) {
                $resolvedCategory = 'consultation';
            }
        }

        Log::info('Appointment creation started', [
            'user_id' => $user->id,
            'counselor_id' => $request->counselor_id,
            'appointment_date' => $request->appointment_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'counseling_category' => $request->counseling_category,
        ]);

        // Check if appointment is on a weekday (Monday through Friday)
        $appointmentDate = Carbon::parse($request->appointment_date);
        $dayOfWeek = $appointmentDate->dayOfWeek;

        if ($dayOfWeek === 0 || $dayOfWeek === 6) { // 0 = Sunday, 6 = Saturday
            return back()->withErrors(['appointment_date' => 'Appointments are only available on weekdays (Monday through Friday).']);
        }

        // Map day of week integer to string
        $daysOfWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $dayName = $daysOfWeek[$dayOfWeek];

        // Check if counselor has marked this specific date as unavailable
        $unavailableDate = CounselorUnavailableDate::where('counselor_id', $request->counselor_id)
            ->where('date', $request->appointment_date)
            ->where('is_unavailable', true)
                ->where('expires_at', '>', Carbon::now('Asia/Manila'))
                ->first();

        if ($unavailableDate) {
            return back()->withErrors(['appointment_date' => 'Counselor is not available on this date.']);
        }

        // Check if counselor is available on this date and time
        $schedule = Schedule::where('counselor_id', $request->counselor_id)
            ->where('day_of_week', $dayName)
            ->where('is_available', true)
            ->first();

        // If no custom schedule, use default working hours (9 AM - 5 PM)
        if (!$schedule) {
            $scheduleStart = Carbon::createFromTime(9, 0, 0); // 9:00 AM
            $scheduleEnd = Carbon::createFromTime(17, 0, 0);   // 5:00 PM
        } else {
            $scheduleStart = Carbon::parse($schedule->start_time);
            $scheduleEnd = Carbon::parse($schedule->end_time);
        }

        // Check if time slot is within counselor's schedule
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);

        if ($startTime < $scheduleStart || $endTime > $scheduleEnd) {
            return back()->withErrors(['start_time' => 'Appointment time must be within counselor\'s schedule.']);
        }

        // Check counselor availability status (ON_LEAVE / UNAVAILABLE with or without time range)
        $counselor = User::find($request->counselor_id);
        if ($counselor && !$counselor->isAvailableForSlot($appointmentDate, $startTime, $endTime)) {
            return back()->withErrors([
                'start_time' => 'Counselor is not available during the selected time range.',
            ])->withInput();
        }

        // Check if slot is available
        $existingAppointment = Appointment::where('counselor_id', $request->counselor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->first();

        // For urgent appointments, allow override but warn about conflicts
        if ($existingAppointment && $request->type !== 'urgent') {
            return back()->withErrors(['start_time' => 'This time slot is already booked.']);
        }

        // If urgent appointment conflicts with existing appointment, create a note
        $conflictNote = '';
        if ($existingAppointment && $request->type === 'urgent') {
            $conflictNote = "URGENT: This appointment conflicts with existing booking (ID: {$existingAppointment->id}) for {$existingAppointment->user->full_name}. Counselor review required.";
        }


        $appointmentData = [
    'user_id' => $user->id,
    'counselor_id' => $request->counselor_id,
    'appointment_date' => $request->appointment_date,
    'start_time' => $request->start_time,
    'end_time' => $request->end_time,
    'type' => $request->type,
            // For non-students (faculty/Non-Teaching Staff), use resolved category (slug) or default to 'consultation'
            'counseling_category' => $resolvedCategory ?? ($user->isStudent() ? null : 'consultation'),
    'reason' => $request->reason,
    'notes' => $request->notes,
    'status' => 'pending',
];

        // Add conflict note for urgent appointments
        if ($request->type === 'urgent' && !empty($conflictNote)) {
            $appointmentData['counselor_notes'] = $conflictNote;
        }

        $appointment = Appointment::create($appointmentData);

        Log::info('Student appointment created successfully', [
            'appointment_id' => $appointment->id,
            'user_id' => $appointment->user_id,
            'counselor_id' => $appointment->counselor_id,
        ]);

        // Schedule reminder: send immediately if within 24 hours, or schedule for 24 hours before if further away
        try {
            $appointmentDateTime = $appointment->getAppointmentDateTime();
            $hoursUntilAppointment = now()->diffInHours($appointmentDateTime, false);

            if ($hoursUntilAppointment <= 24) {
                // Send immediately
                $appointment->user->notify(new AppointmentReminder($appointment, 'tomorrow'));
            } else {
                // Schedule for 24 hours before
                $sendAt = $appointmentDateTime->subDay();
                $appointment->user->notify((new AppointmentReminder($appointment, 'tomorrow'))->delay($sendAt));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to schedule appointment reminder (student controller)', ['appointment_id' => $appointment->id, 'error' => $e->getMessage()]);
        }

        // Notify counselor and assistant of new appointment request
        $counselor = $appointment->counselor;
        $counselor->notifications()->create([
            'appointment_id' => $appointment->id,
            'title' => 'New Appointment Request',
            'message' => "You have a new appointment request from {$appointment->user->full_name} on {$appointment->appointment_date->format('M d, Y')}.",
            'type' => 'appointment_request',
            'is_read' => false,
            'read_at' => null,
        ]);

        // Notify assistant(s) as well
        $assistants = User::where('role', 'assistant')->get();
        foreach ($assistants as $assistant) {
            $assistant->notifications()->create([
                'appointment_id' => $appointment->id,
                'title' => 'New Appointment Request',
                'message' => "Counselor {$counselor->full_name} has a new appointment request from {$appointment->user->full_name} on {$appointment->appointment_date->format('M d, Y')}.",
                'type' => 'appointment_request',
                'is_read' => false,
                'read_at' => null,
            ]);
        }

        $successMessage = 'Appointment requested successfully. Please wait for confirmation.';
        
        // Special message for urgent appointments
        if ($request->type === 'urgent') {
            $successMessage = 'URGENT appointment requested successfully. The counselor will review your request and may contact you for immediate assistance.';
        }

        return redirect()->route('student.appointments.index')
            ->with('success', $successMessage);
    }

    public function show(Appointment $appointment, Request $request): View
    {
        $user = $request->user();

        // Ensure only students, faculty, and Non-Teaching Staff can access this
        if (!$user->canBookAppointments()) {
            abort(403, 'Access denied. This page is for students, faculty, and Non-Teaching Staff only.');
        }

        // Check if user can view this appointment
        if ($appointment->user_id !== $user->id) {
            abort(403, 'You can only view your own appointments.');
        }

        return view('student.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment, Request $request): View
    {
        $user = $request->user();

        // Ensure only students, faculty, and Non-Teaching Staff can access this
        if (!$user->canBookAppointments()) {
            abort(403, 'Access denied. This page is for students, faculty, and Non-Teaching Staff only.');
        }

        // Check if user can edit this appointment
        if ($appointment->user_id !== $user->id) {
            abort(403, 'You can only edit your own appointments.');
        }

        $counselors = User::where('role', 'counselor')->where('is_active', true)->where('availability_status', '!=', 'UNAVAILABLE')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('student.appointments.edit', compact('appointment', 'counselors', 'services'));
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $user = $request->user();

        // Ensure only students, faculty, and Non-Teaching Staff can access this
        if (!$user->canBookAppointments()) {
            abort(403, 'Access denied. This page is for students, faculty, and Non-Teaching Staff only.');
        }

        // Check if user can update this appointment
        if ($appointment->user_id !== $user->id) {
            abort(403, 'You can only update your own appointments.');
        }

        // Students can only reschedule confirmed appointments
        if (!$appointment->canBeRescheduled()) {
            return back()->with('error', 'Only confirmed appointments can be rescheduled.');
        }

        $validationRules = [
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reschedule_reason' => 'required|string|max:1000',
        ];

        // Type validation differs by user role:
        // Students can select regular, urgent, or follow_up
        // Faculty and Non-Teaching Staff can only select regular or urgent
        if ($user->isStudent()) {
            $validationRules['type'] = 'required|in:regular,urgent,follow_up';
        } else {
            $validationRules['type'] = 'required|in:regular,urgent';
        }
        
        // Counseling category: accept numeric service ID or slug; we'll normalize it after validation
        if ($user->isStudent()) {
            $validationRules['counseling_category'] = 'required';
        } else {
            $validationRules['counseling_category'] = 'sometimes|nullable';
        }
        
        $validationRules['reason'] = 'required_if:type,urgent|nullable|string|max:500';
        $validationRules['notes'] = 'nullable|string|max:1000';
        
        $request->validate($validationRules);

        // Normalize counseling_category: accept numeric service ID or service slug.
        $rawCategory = $request->input('counseling_category');
        $resolvedCategory = null;
        if ($rawCategory !== null && $rawCategory !== '') {
            if (is_numeric($rawCategory)) {
                $service = Service::find(intval($rawCategory));
                if ($service) {
                    $resolvedCategory = $service->slug;
                } else {
                    return back()->withErrors(['counseling_category' => 'Selected counseling category is invalid.'])->withInput();
                }
            } else {
                $service = Service::where('slug', $rawCategory)->first();
                if ($service) {
                    $resolvedCategory = $service->slug;
                } else {
                    if (in_array($rawCategory, ['consultation','conduct_intake_interview','information_services','internal_referral_services','counseling_services','conduct_exit_interview'])) {
                        $resolvedCategory = $rawCategory;
                    } else {
                        return back()->withErrors(['counseling_category' => 'Selected counseling category is invalid.'])->withInput();
                    }
                }
            }
        } else {
            if (!$user->isStudent()) {
                $resolvedCategory = 'consultation';
            }
        }

        // Check if the new date is on a weekday (Monday through Friday)
        $appointmentDate = Carbon::parse($request->appointment_date);
        $dayOfWeek = $appointmentDate->dayOfWeek;
        if ($dayOfWeek === 0 || $dayOfWeek === 6) { // 0 = Sunday, 6 = Saturday
            return back()->withErrors(['appointment_date' => 'Appointments are only available on weekdays (Monday through Friday).']);
        }

        // Map day of week integer to string
        $daysOfWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $dayName = $daysOfWeek[$dayOfWeek];

        // Check if counselor has marked this specific date as unavailable
        $unavailableDate = CounselorUnavailableDate::where('counselor_id', $request->counselor_id)
            ->where('date', $request->appointment_date)
            ->where('is_unavailable', true)
                ->where('expires_at', '>', Carbon::now('Asia/Manila'))
                ->first();

        if ($unavailableDate) {
            return back()->withErrors(['appointment_date' => 'Counselor is not available on this date.']);
        }

        // Use the existing counselor for the appointment
        $counselor = $appointment->counselor;

        // Respect counselor availability status for the new slot
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        if (!$counselor->isAvailableForSlot($appointmentDate, $startTime, $endTime)) {
            return back()->withErrors([
                'start_time' => 'Counselor is not available during the selected time range.',
            ])->withInput();
        }

        // Check for conflicts
        $conflict = Appointment::where('counselor_id', $appointment->counselor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('id', '!=', $appointment->id)
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('start_time', '<', $request->start_time)
                      ->where('end_time', '>', $request->start_time);
                })->orWhere(function($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->end_time);
                })->orWhere(function($q) use ($request) {
                    $q->where('start_time', '>=', $request->start_time)
                      ->where('end_time', '<=', $request->end_time);
                });
            })
            ->first();

        if ($conflict) {
            return back()->withErrors(['start_time' => 'The selected time slot conflicts with an existing appointment.']);
        }

        // Update the appointment
        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'counseling_category' => $resolvedCategory ?? ($user->isStudent() ? null : 'consultation'),
            'reason' => $request->reason,
            'notes' => $request->notes,
            'reschedule_reason' => $request->reschedule_reason,
            'status' => 'pending', // Reset to pending when rescheduled
        ]);

        // Notify counselor of appointment reschedule
        $counselor = $appointment->counselor;
        $counselor->notifications()->create([
            'appointment_id' => $appointment->id,
            'title' => 'Appointment Rescheduled',
            'message' => "{$appointment->user->full_name} has rescheduled their appointment to {$appointment->appointment_date->format('M d, Y')} at {$appointment->start_time->format('g:i A')}. Reason: {$request->reschedule_reason}",
            'type' => 'appointment_rescheduled',
            'is_read' => false,
            'read_at' => null,
        ]);

        // Notify assistants of appointment reschedule
        $assistants = User::where('role', 'assistant')->get();
        foreach ($assistants as $assistant) {
            $assistant->notifications()->create([
                'appointment_id' => $appointment->id,
                'title' => 'Appointment Rescheduled',
                'message' => "{$appointment->user->full_name} has rescheduled their appointment with {$counselor->full_name} to {$appointment->appointment_date->format('M d, Y')} at {$appointment->start_time->format('g:i A')}. Reason: {$request->reschedule_reason}",
                'type' => 'appointment_rescheduled',
                'is_read' => false,
                'read_at' => null,
            ]);
        }

        return redirect()->route('student.appointments.index')
            ->with('success', 'Appointment rescheduled successfully. Please wait for counselor confirmation.');
    }

    public function cancel(Appointment $appointment, Request $request): RedirectResponse
    {
        $user = $request->user();

        // Ensure only students, faculty, and Non-Teaching Staff can access this
        if (!$user->canBookAppointments()) {
            abort(403, 'Access denied. This page is for students, faculty, and Non-Teaching Staff only.');
        }

        // Only the appointment owner can cancel
        if ($appointment->user_id !== $user->id) {
            abort(403, 'You can only cancel your own appointments.');
        }

        // Only pending or confirmed appointments can be cancelled
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed appointments can be cancelled.');
        }

        $appointment->update(['status' => 'cancelled']);

        // Notify counselor of appointment cancellation
        $appointment->counselor->notifications()->create([
            'appointment_id' => $appointment->id,
            'title' => 'Appointment Cancelled',
            'message' => "The appointment requested by {$appointment->user->full_name} on {$appointment->appointment_date->format('M d, Y')} has been cancelled.",
            'type' => 'appointment_cancelled',
            'is_read' => false,
            'read_at' => null,
        ]);

        return redirect()->route('student.appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }

    public function sessionHistory(Request $request): View
    {
        $user = $request->user();

        // Ensure only students, faculty, and Non-Teaching Staff can access this
        if (!$user->canBookAppointments()) {
            abort(403, 'Access denied. This page is for students, faculty, and Non-Teaching Staff only.');
        }

        // Query for table data (respects filters)
        $tableQuery = Appointment::with(['user', 'counselor'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled', 'no_show', 'rejected', 'rescheduled']);

        // Filter by status for table data
        if ($request->filled('status') && $request->status !== 'all') {
            $tableQuery->where('status', $request->status);
        }

        $appointments = $tableQuery->orderBy('appointment_date', 'desc')->paginate(15)->withQueryString();

        // Query for statistics (always shows totals, independent of filters)
        $statsQuery = Appointment::with(['user', 'counselor'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled', 'no_show']);

        $stats = [
            'total_sessions' => $statsQuery->count(),
            'completed_sessions' => (clone $statsQuery)->where('status', 'completed')->count(),
            'cancelled_sessions' => (clone $statsQuery)->where('status', 'cancelled')->count(),
            'no_show_sessions' => (clone $statsQuery)->where('status', 'no_show')->count(),
        ];

        return view('student.appointments.session-history', compact('appointments', 'stats'));
    }

    public function exportSessionHistory(Request $request)
    {
        $user = $request->user();

        // Ensure only students, faculty, and Non-Teaching Staff can access this
        if (!$user->canBookAppointments()) {
            abort(403, 'Access denied. This page is for students, faculty, and Non-Teaching Staff only.');
        }

        $query = Appointment::with(['user', 'counselor'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled', 'no_show', 'rejected', 'rescheduled']);

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->get();

        $stats = [
            'total_sessions' => $query->count(),
            'completed_sessions' => (clone $query)->where('status', 'completed')->count(),
            'cancelled_sessions' => (clone $query)->where('status', 'cancelled')->count(),
            'no_show_sessions' => (clone $query)->where('status', 'no_show')->count(),
        ];

        $data = [
            'appointments' => $appointments,
            'stats' => $stats,
            'user' => $user,
        ];

        $pdf = Pdf::loadView('pdfs.student-session-history', $data)->setPaper('A4', 'landscape');

        $filename = 'student_session_history_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    public function getAvailableSlots(User $counselor, Request $request)
    {
        $user = $request->user();

        if (!$user->canBookAppointments()) {
            abort(403, 'Access denied. This page is for students, faculty, and Non-Teaching Staff only.');
        }

        $date = $request->get('date');
        $isUrgent = $request->boolean('urgent', false);
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        // Only allow weekdays (Mon–Fri)
        if ($dayOfWeek === 0 || $dayOfWeek === 6) {
            return response()->json([
                'slots' => [],
                'message' => 'Appointments are only available on weekdays (Monday through Friday).'
            ]);
        }

        // Map day of week integer to string
        $daysOfWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $dayName = $daysOfWeek[$dayOfWeek];

        // Check if counselor has marked this specific date as unavailable
        $unavailableDate = CounselorUnavailableDate::where('counselor_id', $counselor->id)
            ->where('date', $date)
            ->where('is_unavailable', true)
            ->where('expires_at', '>', Carbon::now('Asia/Manila'))
            ->first();

        if ($unavailableDate) {
            return response()->json([
                'slots' => [],
                'message' => 'Counselor is not available on this date.'
            ]);
        }

        // Check if counselor has a custom schedule for this day
        $schedule = Schedule::where('counselor_id', $counselor->id)
            ->where('day_of_week', $dayName)
            ->where('is_available', true)
            ->first();

        // If no custom schedule, use default working hours (9 AM - 5 PM)
        if (!$schedule) {
            $startTime = Carbon::createFromTime(9, 0, 0); // 9:00 AM
            $endTime = Carbon::createFromTime(17, 0, 0);   // 5:00 PM
        } else {
            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);
        }

        $slots = [];
        $currentTime = $startTime->copy();

        // Prepare debug info to help trace issues when slots are empty
        $debug = [
            'counselor_id' => $counselor->id,
            'counselor_availability_status' => $counselor->availability_status ?? null,
            'counselor_unavailable_from' => $counselor->unavailable_from ? $counselor->unavailable_from->format('H:i') : null,
            'counselor_unavailable_to' => $counselor->unavailable_to ? $counselor->unavailable_to->format('H:i') : null,
            'requested_date' => $date,
            'day_name' => $dayName,
            'using_custom_schedule' => $schedule ? true : false,
            'schedule_start' => $schedule ? (is_object($schedule->start_time) ? $schedule->start_time->format('H:i') : $schedule->start_time) : $startTime->format('H:i'),
            'schedule_end' => $schedule ? (is_object($schedule->end_time) ? $schedule->end_time->format('H:i') : $schedule->end_time) : $endTime->format('H:i'),
            'is_urgent' => $isUrgent,
            'found_existing_appointments' => 0,
        ];

        while ($currentTime < $endTime) {
            $slotStart = $currentTime->copy();
            $slotEnd = $currentTime->copy()->addMinutes(30);

            // Skip slots that fall into counselor's unavailable window (status-based)
            if (!$counselor->isAvailableForSlot(Carbon::parse($date), $slotStart, $slotEnd)) {
                $currentTime->addMinutes(30);
                continue;
            }

            $existingAppointment = Appointment::where('counselor_id', $counselor->id)
                ->where('appointment_date', $date)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($slotStart, $slotEnd) {
                    $query->whereBetween('start_time', [$slotStart->format('H:i'), $slotEnd->format('H:i')])
                          ->orWhereBetween('end_time', [$slotStart->format('H:i'), $slotEnd->format('H:i')])
                          ->orWhere(function ($q) use ($slotStart, $slotEnd) {
                              $q->where('start_time', '<=', $slotStart->format('H:i'))
                                ->where('end_time', '>=', $slotEnd->format('H:i'));
                          });
                })
                ->first();

            if (!$existingAppointment || $isUrgent) {
                $slots[] = [
                    'time' => $slotStart->format('H:i'),
                    'end_time' => $slotEnd->format('H:i'),
                    'formatted_time' => $slotStart->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                    'is_conflict' => $existingAppointment ? true : false,
                    'conflict_message' => $existingAppointment ? ' (Conflicts with existing booking)' : ''
                ];
            }

            if ($existingAppointment) {
                $debug['found_existing_appointments']++;
            }

            $currentTime->addMinutes(30);
        }

        $response = [
            'slots' => $slots,
            'message' => count($slots) > 0 ? 'Available time slots found.' : 'No available time slots for this date.',
            'debug' => $debug,
        ];

        if (count($slots) === 0) {
            Log::debug('No available slots response', $response);
        }

        return response()->json($response);
    }

    private function getAvailableDates(): array
    {
        $dates = [];
        $startDate = Carbon::today();
        $counselors = User::where('role', 'counselor')->where('is_active', true)->where('availability_status', '!=', 'UNAVAILABLE')->get();

        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayOfWeek = $date->dayOfWeek;

            // Only allow weekdays (Monday through Friday)
            if ($dayOfWeek === 0 || $dayOfWeek === 6) { // 0 = Sunday, 6 = Saturday
                continue;
            }

            $dateString = $date->format('Y-m-d');
            $availableCounselors = 0;

            foreach ($counselors as $counselor) {
                // Check if counselor is not marked unavailable on this date
                $isUnavailable = CounselorUnavailableDate::where('counselor_id', $counselor->id)
                    ->where('date', $dateString)
                    ->where('is_unavailable', true)
                    ->where('expires_at', '>', Carbon::now('Asia/Manila'))
                    ->exists();

                if (!$isUnavailable) {
                    // Respect full-day availability status: ON_LEAVE / UNAVAILABLE with no time range
                    if (!$counselor->isAvailable() && (!$counselor->unavailable_from || !$counselor->unavailable_to)) {
                        continue;
                    }

                    // Check if counselor has schedule or uses default hours
                    $hasSchedule = Schedule::where('counselor_id', $counselor->id)
                        ->where('day_of_week', strtolower($date->format('l')))
                        ->where('is_available', true)
                        ->exists();

                    if ($hasSchedule) {
                        $availableCounselors++;
                    } else {
                        // Counselor uses default hours (9 AM - 5 PM)
                        $availableCounselors++;
                    }
                }
            }

            if ($availableCounselors > 0) {
                $dates[] = $dateString;
            }
        }

        return $dates;
    }
}
