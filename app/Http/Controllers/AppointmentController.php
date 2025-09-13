<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $appointments = collect();

        if ($user->isCounselor() || $user->isAssistant()) {
            // Counselors and assistants see all appointments they're assigned to
            // Prioritize urgent appointments first
            $appointments = Appointment::with(['user', 'counselor'])
                ->where('counselor_id', $user->id)
                ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->paginate(10);
        } else {
            // Students and faculty see their own appointments
            $appointments = $user->appointments()
                ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->paginate(10);
        }

        return view('appointments.index', compact('appointments'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        
        if ($user->isCounselor() || $user->isAssistant()) {
            return redirect()->route('appointments.index')
                ->with('error', 'Counselors and assistants cannot book appointments.');
        }

        // Get available counselors
        $counselors = User::where('role', 'counselor')->get();
        
        // Get next 30 days of available dates
        $availableDates = $this->getAvailableDates();
        
        return view('appointments.create', compact('counselors', 'availableDates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->isCounselor() || $user->isAssistant()) {
            return redirect()->route('appointments.index')
                ->with('error', 'Counselors and assistants cannot book appointments.');
        }
        
        $validationRules = [
            'counselor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'type' => 'required|in:regular,urgent,follow_up',
            'reason' => 'required|string|max:1000',
        ];

        // Add counseling category validation based on user role
        if ($user->isStudent()) {
            $validationRules['counseling_category'] = 'required|in:conduct_intake_interview,information_services,internal_referral_services,counseling_services,conduct_exit_interview';
        }

        try {
            $validatedData = $request->validate($validationRules);
            
            // Manual validation for end_time after start_time
            if ($request->start_time >= $request->end_time) {
                return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Appointment validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return back()->withErrors($e->errors())->withInput();
        }

        \Log::info('Appointment creation started', [
            'user_id' => $user->id,
            'counselor_id' => $request->counselor_id,
            'appointment_date' => $request->appointment_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'reason' => $request->reason,
        ]);

        // Check if appointment is on Monday or Friday
        $appointmentDate = Carbon::parse($request->appointment_date);
        $dayOfWeek = strtolower($appointmentDate->format('l'));
        
        if ($dayOfWeek !== 'monday' && $dayOfWeek !== 'friday') {
            return back()->withErrors(['appointment_date' => 'Appointments are only available on Monday and Friday.']);
        }

        // Check if counselor is available on this date and time
        $schedule = Schedule::where('counselor_id', $request->counselor_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$schedule) {
            return back()->withErrors(['appointment_date' => 'Counselor is not available on this date.']);
        }

        // Check if time slot is within counselor's schedule
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $scheduleStart = Carbon::parse($schedule->start_time);
        $scheduleEnd = Carbon::parse($schedule->end_time);

        if ($startTime < $scheduleStart || $endTime > $scheduleEnd) {
            return back()->withErrors(['start_time' => 'Appointment time must be within counselor\'s schedule.']);
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
            'reason' => $request->reason,
            'status' => 'pending',
        ];

        // Set counseling category based on user role
        if ($user->isStudent()) {
            $appointmentData['counseling_category'] = $request->counseling_category;
        } else {
            $appointmentData['counseling_category'] = 'consultation';
        }

        // Add conflict note for urgent appointments
        if ($request->type === 'urgent' && !empty($conflictNote)) {
            $appointmentData['counselor_notes'] = $conflictNote;
        }

        $appointment = Appointment::create($appointmentData);

        \Log::info('Appointment created successfully', [
            'appointment_id' => $appointment->id,
            'user_id' => $appointment->user_id,
            'counselor_id' => $appointment->counselor_id,
        ]);

        $successMessage = 'Appointment requested successfully. Please wait for confirmation.';
        
        // Special message for urgent appointments
        if ($request->type === 'urgent') {
            $successMessage = 'URGENT appointment requested successfully. The counselor will review your request and may contact you for immediate assistance.';
        }

        return redirect()->route('appointments.index')
            ->with('success', $successMessage);
    }

    public function show(Appointment $appointment, Request $request): View
    {
        $user = $request->user();
        
        // Check if user can view this appointment
        if (!$user->isCounselor() && !$user->isAssistant() && $appointment->user_id !== $user->id) {
            abort(403);
        }

        if ($user->isCounselor() || $user->isAssistant()) {
            if ($appointment->counselor_id !== $user->id) {
                abort(403);
            }
        }

        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment, Request $request): View
    {
        $user = $request->user();
        
        if (!$user->isCounselor() && !$user->isAssistant() && $appointment->user_id !== $user->id) {
            abort(403);
        }

        if ($user->isCounselor() || $user->isAssistant()) {
            if ($appointment->counselor_id !== $user->id) {
                abort(403);
            }
        }

        $counselors = User::where('role', 'counselor')->get();
        
        return view('appointments.edit', compact('appointment', 'counselors'));
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $user = $request->user();
        
        // Check if user can update this appointment
        if ($user->isCounselor() || $user->isAssistant()) {
            if ($appointment->counselor_id !== $user->id) {
                abort(403, 'You can only update appointments assigned to you.');
            }
        } else {
            if ($appointment->user_id !== $user->id) {
                abort(403, 'You can only update your own appointments.');
            }
            
            // Students can only reschedule pending or confirmed appointments
            if (!in_array($appointment->status, ['pending', 'confirmed'])) {
                return back()->with('error', 'You can only reschedule pending or confirmed appointments.');
            }
        }

        // If status is being updated
        if ($request->has('status')) {
            $request->validate([
                'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
            ]);

            // Validate status transitions
            $currentStatus = $appointment->status;
            $newStatus = $request->status;
            
            // Check if status transition is allowed
            $allowedTransitions = $this->getAllowedStatusTransitions($currentStatus);
            if (!in_array($newStatus, $allowedTransitions)) {
                return back()->withErrors(['status' => "Cannot change status from '{$currentStatus}' to '{$newStatus}'. Allowed transitions: " . implode(', ', $allowedTransitions)]);
            }

            // Special validation for no_show status
            if ($newStatus === 'no_show') {
                $appointmentDateTime = $appointment->getAppointmentDateTime();
                if ($appointmentDateTime->isFuture()) {
                    return back()->withErrors(['status' => 'Cannot mark as "No Show" for future appointments.']);
                }
            }

            $appointment->update([
                'status' => $request->status,
            ]);

            return redirect()->route('appointments.show', $appointment)
                ->with('success', 'Appointment status updated successfully.');
        }

        // If only counselor notes are being updated (for counselors/assistants)
        if ($user->isCounselor() || $user->isAssistant()) {
            $request->validate([
                'counselor_notes' => 'nullable|string|max:1000',
            ]);

            $appointment->update([
                'counselor_notes' => $request->counselor_notes,
            ]);

            return redirect()->route('appointments.show', $appointment)
                ->with('success', 'Counselor notes updated successfully.');
        }

        // For students - reschedule appointment
        $request->validate([
            'counselor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:regular,urgent,follow_up',
            'counseling_category' => 'required_if:user_role,student|in:conduct_intake_interview,information_services,internal_referral_services,counseling_services,conduct_exit_interview',
            'reason' => 'required_if:type,urgent|nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if the new date is Monday or Friday
        $appointmentDate = Carbon::parse($request->appointment_date);
        $dayOfWeek = $appointmentDate->dayOfWeek;
        if ($dayOfWeek !== 1 && $dayOfWeek !== 5) { // 1 = Monday, 5 = Friday
            return back()->withErrors(['appointment_date' => 'Appointments are only available on Monday and Friday.']);
        }

        // Check if the selected counselor is available
        $counselor = User::find($request->counselor_id);
        if (!$counselor || $counselor->role !== 'counselor') {
            return back()->withErrors(['counselor_id' => 'Selected counselor is not available.']);
        }

        // Check for conflicts
        $conflict = Appointment::where('counselor_id', $request->counselor_id)
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
            'counselor_id' => $request->counselor_id,
            'appointment_date' => $request->appointment_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'counseling_category' => $request->counseling_category,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'status' => 'pending', // Reset to pending when rescheduled
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment rescheduled successfully. Please wait for counselor confirmation.');
    }

    /**
     * Get allowed status transitions based on current status
     */
    private function getAllowedStatusTransitions(string $currentStatus): array
    {
        return match($currentStatus) {
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['cancelled', 'no_show'],
            'completed' => [], // No transitions allowed
            'cancelled' => [], // No transitions allowed
            'no_show' => [], // No transitions allowed
            default => [],
        };
    }

    public function destroy(Appointment $appointment, Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user->isCounselor() && !$user->isAssistant() && $appointment->user_id !== $user->id) {
            abort(403);
        }

        if ($user->isCounselor() || $user->isAssistant()) {
            if ($appointment->counselor_id !== $user->id) {
                abort(403);
            }
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Cancel appointment (for students)
     */
    public function cancel(Appointment $appointment, Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Only the appointment owner can cancel
        if ($appointment->user_id !== $user->id) {
            abort(403, 'You can only cancel your own appointments.');
        }

        // Only pending or confirmed appointments can be cancelled
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed appointments can be cancelled.');
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Show session history for counselors and students
     */
    public function sessionHistory(Request $request): View
    {
        $user = $request->user();
        
        $query = Appointment::with(['user', 'counselor']);
        
        if ($user->isCounselor() || $user->isAssistant()) {
            // Counselors and assistants see their assigned appointments
            $query->where('counselor_id', $user->id);
        } else {
            // Students see their own appointments
            $query->where('user_id', $user->id);
        }
        
        // Show past appointments (completed, cancelled, no_show, rejected, rescheduled)
        $query->whereIn('status', ['completed', 'cancelled', 'no_show', 'rejected', 'rescheduled']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $user) {
                if ($user->isCounselor() || $user->isAssistant()) {
                    // Counselors search by client name
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                    });
                } else {
                    // Students search by counselor name
                    $q->whereHas('counselor', function($counselorQuery) use ($search) {
                        $counselorQuery->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%");
                    });
                }
                $q->orWhere('reason', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('counselor_notes', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by counseling category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('counseling_category', $request->category);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('appointment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('appointment_date', '<=', $request->date_to);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'appointment_date');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['appointment_date', 'start_time', 'created_at', 'status', 'type'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'appointment_date';
        }

        $query->orderBy($sortBy, $sortOrder);

        $appointments = $query->paginate(15)->withQueryString();

        // Get statistics for the filtered results
        $stats = [
            'total_sessions' => $query->count(),
            'completed_sessions' => $query->where('status', 'completed')->count(),
            'cancelled_sessions' => $query->where('status', 'cancelled')->count(),
            'no_show_sessions' => $query->where('status', 'no_show')->count(),
        ];

        return view('appointments.session-history', compact('appointments', 'stats'));
    }

    /**
     * Export session history to CSV
     */
    public function exportSessionHistory(Request $request)
    {
        $user = $request->user();
        
        $query = Appointment::with(['user', 'counselor']);
        
        if ($user->isCounselor() || $user->isAssistant()) {
            // Counselors and assistants export their assigned appointments
            $query->where('counselor_id', $user->id);
        } else {
            // Students export their own appointments
            $query->where('user_id', $user->id);
        }
        
        // Export past appointments (completed, cancelled, no_show, rejected, rescheduled)
        $query->whereIn('status', ['completed', 'cancelled', 'no_show', 'rejected', 'rescheduled']);

        // Apply same filters as session history
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $user) {
                if ($user->isCounselor() || $user->isAssistant()) {
                    // Counselors search by client name
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                    });
                } else {
                    // Students search by counselor name
                    $q->whereHas('counselor', function($counselorQuery) use ($search) {
                        $counselorQuery->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%");
                    });
                }
                $q->orWhere('reason', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('counselor_notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('counseling_category', $request->category);
        }

        if ($request->filled('date_from')) {
            $query->where('appointment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('appointment_date', '<=', $request->date_to);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->get();

        $filename = 'session_history_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($appointments) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Date',
                'Time',
                'Client Name',
                'Client Email',
                'Type',
                'Category',
                'Status',
                'Reason',
                'Counselor Notes',
                'Created At'
            ]);

            // CSV data
            foreach ($appointments as $appointment) {
                fputcsv($file, [
                    $appointment->appointment_date->format('Y-m-d'),
                    $appointment->start_time->format('H:i') . ' - ' . $appointment->end_time->format('H:i'),
                    $appointment->user->full_name,
                    $appointment->user->email,
                    ucfirst($appointment->type),
                    $appointment->getCounselingCategoryLabel(),
                    ucfirst($appointment->status),
                    $appointment->reason,
                    $appointment->counselor_notes,
                    $appointment->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get appointment statistics for dashboard
     */
    public function getStatistics(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isCounselor() && !$user->isAssistant()) {
            abort(403);
        }

        $query = Appointment::where('counselor_id', $user->id);

        $stats = [
            'total' => $query->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'confirmed' => $query->where('status', 'confirmed')->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'cancelled' => $query->where('status', 'cancelled')->count(),
            'no_show' => $query->where('status', 'no_show')->count(),
            'urgent' => $query->where('type', 'urgent')->count(),
            'today' => $query->where('appointment_date', now()->toDateString())
                            ->where('status', '!=', 'cancelled')
                            ->count(),
            'this_week' => $query->whereBetween('appointment_date', [
                                now()->startOfWeek()->toDateString(),
                                now()->endOfWeek()->toDateString()
                            ])
                            ->where('status', '!=', 'cancelled')
                            ->count(),
            'this_month' => $query->whereBetween('appointment_date', [
                                now()->startOfMonth()->toDateString(),
                                now()->endOfMonth()->toDateString()
                            ])
                            ->where('status', '!=', 'cancelled')
                            ->count(),
        ];

        return response()->json($stats);
    }

    private function getAvailableDates(): array
    {
        $dates = [];
        $startDate = Carbon::today();
        
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayOfWeek = strtolower($date->format('l'));
            
            // Only allow Monday and Friday
            if ($dayOfWeek !== 'monday' && $dayOfWeek !== 'friday') {
                continue;
            }
            
            // Check if any counselor is available on this day
            $availableCounselors = Schedule::where('day_of_week', $dayOfWeek)
                ->where('is_available', true)
                ->count();
                
            if ($availableCounselors > 0) {
                $dates[] = $date->format('Y-m-d');
            }
        }
        
        return $dates;
    }

    /**
     * Approve an appointment (Counselor only).
     */
    public function approve(Appointment $appointment): RedirectResponse
    {
        if (!auth()->user()->canApproveAppointments()) {
            return redirect()->route('appointments.index')
                ->with('error', 'You do not have permission to approve appointments.');
        }

        $appointment->update([
            'status' => 'confirmed',
            'counselor_notes' => $appointment->counselor_notes . "\n\n[Approved on " . now()->format('M d, Y g:i A') . "]"
        ]);

        // Create notification for the user
        $appointment->user->notifications()->create([
            'title' => 'Appointment Approved',
            'message' => "Your appointment on {$appointment->getFormattedDateTime()} has been approved.",
            'type' => 'appointment_approved',
            'read_at' => null,
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment approved successfully.');
    }

    /**
     * Reject an appointment (Counselor only).
     */
    public function reject(Request $request, Appointment $appointment): RedirectResponse
    {
        if (!auth()->user()->canApproveAppointments()) {
            return redirect()->route('appointments.index')
                ->with('error', 'You do not have permission to reject appointments.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $appointment->update([
            'status' => 'cancelled',
            'counselor_notes' => $appointment->counselor_notes . "\n\n[Rejected on " . now()->format('M d, Y g:i A') . " - Reason: {$request->rejection_reason}]"
        ]);

        // Create notification for the user
        $appointment->user->notifications()->create([
            'title' => 'Appointment Rejected',
            'message' => "Your appointment on {$appointment->getFormattedDateTime()} has been rejected. Reason: {$request->rejection_reason}",
            'type' => 'appointment_rejected',
            'read_at' => null,
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment rejected successfully.');
    }

    /**
     * Reschedule an appointment (Counselor only).
     */
    public function reschedule(Request $request, Appointment $appointment): RedirectResponse
    {
        if (!auth()->user()->canApproveAppointments()) {
            return redirect()->route('appointments.index')
                ->with('error', 'You do not have permission to reschedule appointments.');
        }

        $request->validate([
            'new_appointment_date' => 'required|date|after_or_equal:today',
            'new_start_time' => 'required|date_format:H:i',
            'new_end_time' => 'required|date_format:H:i',
            'reschedule_reason' => 'required|string|max:500'
        ]);

        // Check if new time slot is available
        $conflictingAppointment = Appointment::where('counselor_id', $appointment->counselor_id)
            ->where('appointment_date', $request->new_appointment_date)
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $appointment->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->new_start_time, $request->new_end_time])
                    ->orWhereBetween('end_time', [$request->new_start_time, $request->new_end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->new_start_time)
                            ->where('end_time', '>=', $request->new_end_time);
                    });
            })
            ->first();

        if ($conflictingAppointment) {
            return back()->withErrors(['new_start_time' => 'The selected time slot conflicts with an existing appointment.'])->withInput();
        }

        $oldDateTime = $appointment->getFormattedDateTime();

        $appointment->update([
            'appointment_date' => $request->new_appointment_date,
            'start_time' => $request->new_start_time,
            'end_time' => $request->new_end_time,
            'counselor_notes' => $appointment->counselor_notes . "\n\n[Rescheduled on " . now()->format('M d, Y g:i A') . " from {$oldDateTime} to " . $appointment->getFormattedDateTime() . " - Reason: {$request->reschedule_reason}]"
        ]);

        // Create notification for the user
        $appointment->user->notifications()->create([
            'title' => 'Appointment Rescheduled',
            'message' => "Your appointment has been rescheduled from {$oldDateTime} to {$appointment->getFormattedDateTime()}. Reason: {$request->reschedule_reason}",
            'type' => 'appointment_rescheduled',
            'read_at' => null,
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment rescheduled successfully.');
    }

    public function getAvailableSlots(User $counselor, Request $request)
    {
        $date = $request->get('date');
        $isUrgent = $request->get('urgent', false);
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));
        
        // Only allow Monday and Friday
        if ($dayOfWeek !== 'monday' && $dayOfWeek !== 'friday') {
            return response()->json([
                'slots' => [],
                'message' => 'Appointments are only available on Monday and Friday.'
            ]);
        }
        
        // Get counselor's schedule for this day
        $schedule = Schedule::where('counselor_id', $counselor->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();
            
        if (!$schedule) {
            return response()->json([
                'slots' => [],
                'message' => 'Counselor is not available on this day.'
            ]);
        }
        
        // Generate time slots (30-minute intervals)
        $slots = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $currentTime = $startTime->copy();
        
        while ($currentTime < $endTime) {
            $slotTime = $currentTime->format('H:i');
            $slotEndTime = $currentTime->copy()->addMinutes(30)->format('H:i');
            
            // Check if this slot is already booked
            $existingAppointment = Appointment::where('counselor_id', $counselor->id)
                ->where('appointment_date', $date)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($slotTime, $slotEndTime) {
                    $query->whereBetween('start_time', [$slotTime, $slotEndTime])
                        ->orWhereBetween('end_time', [$slotTime, $slotEndTime])
                        ->orWhere(function ($q) use ($slotTime, $slotEndTime) {
                            $q->where('start_time', '<=', $slotTime)
                                ->where('end_time', '>=', $slotEndTime);
                        });
                })
                ->first();
                
            // For urgent appointments, show all slots but mark conflicts
            if (!$existingAppointment || $isUrgent) {
                $isConflict = $existingAppointment ? true : false;
                $slots[] = [
                    'time' => $slotTime,
                    'end_time' => $slotEndTime,
                    'formatted_time' => $currentTime->format('g:i A') . ' - ' . $currentTime->addMinutes(30)->format('g:i A'),
                    'is_conflict' => $isConflict,
                    'conflict_message' => $isConflict ? ' (Conflicts with existing booking)' : ''
                ];
            }
            
            $currentTime->addMinutes(30);
        }
        
        return response()->json([
            'slots' => $slots,
            'message' => count($slots) > 0 ? 'Available time slots found.' : 'No available time slots for this date.'
        ]);
    }
} 