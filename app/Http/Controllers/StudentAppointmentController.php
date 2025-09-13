<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class StudentAppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        
        // Ensure only students can access this
        if (!$user->isStudent()) {
            abort(403, 'Access denied. This page is for students only.');
        }

        $appointments = $user->appointments()
            ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->paginate(10);

        return view('student.appointments.index', compact('appointments'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        
        // Ensure only students can access this
        if (!$user->isStudent()) {
            abort(403, 'Access denied. This page is for students only.');
        }

        // Get available counselors
        $counselors = User::where('role', 'counselor')->get();
        
        // Get next 30 days of available dates
        $availableDates = $this->getAvailableDates();
        
        return view('student.appointments.create', compact('counselors', 'availableDates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Ensure only students can access this
        if (!$user->isStudent()) {
            abort(403, 'Access denied. This page is for students only.');
        }
        
        $validationRules = [
            'counselor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'type' => 'required|in:regular,urgent,follow_up',
            'counseling_category' => 'required|in:conduct_intake_interview,information_services,internal_referral_services,counseling_services,conduct_exit_interview',
            'reason' => 'required_if:type,urgent|nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];

        try {
            $validatedData = $request->validate($validationRules);
            
            // Manual validation for end_time after start_time
            if ($request->start_time >= $request->end_time) {
                return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Student appointment validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return back()->withErrors($e->errors())->withInput();
        }

        \Log::info('Student appointment creation started', [
            'user_id' => $user->id,
            'counselor_id' => $request->counselor_id,
            'appointment_date' => $request->appointment_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'counseling_category' => $request->counseling_category,
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
            'counseling_category' => $request->counseling_category,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'status' => 'pending',
        ];

        // Add conflict note for urgent appointments
        if ($request->type === 'urgent' && !empty($conflictNote)) {
            $appointmentData['counselor_notes'] = $conflictNote;
        }

        $appointment = Appointment::create($appointmentData);

        \Log::info('Student appointment created successfully', [
            'appointment_id' => $appointment->id,
            'user_id' => $appointment->user_id,
            'counselor_id' => $appointment->counselor_id,
        ]);

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
        
        // Ensure only students can access this
        if (!$user->isStudent()) {
            abort(403, 'Access denied. This page is for students only.');
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
        
        // Ensure only students can access this
        if (!$user->isStudent()) {
            abort(403, 'Access denied. This page is for students only.');
        }
        
        // Check if user can edit this appointment
        if ($appointment->user_id !== $user->id) {
            abort(403, 'You can only edit your own appointments.');
        }

        $counselors = User::where('role', 'counselor')->get();
        
        return view('student.appointments.edit', compact('appointment', 'counselors'));
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $user = $request->user();
        
        // Ensure only students can access this
        if (!$user->isStudent()) {
            abort(403, 'Access denied. This page is for students only.');
        }
        
        // Check if user can update this appointment
        if ($appointment->user_id !== $user->id) {
            abort(403, 'You can only update your own appointments.');
        }
        
        // Students can only reschedule pending or confirmed appointments
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'You can only reschedule pending or confirmed appointments.');
        }

        $request->validate([
            'counselor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:regular,urgent,follow_up',
            'counseling_category' => 'required|in:conduct_intake_interview,information_services,internal_referral_services,counseling_services,conduct_exit_interview',
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

        return redirect()->route('student.appointments.index')
            ->with('success', 'Appointment rescheduled successfully. Please wait for counselor confirmation.');
    }

    public function cancel(Appointment $appointment, Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Ensure only students can access this
        if (!$user->isStudent()) {
            abort(403, 'Access denied. This page is for students only.');
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

        return redirect()->route('student.appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }

    public function sessionHistory(Request $request): View
    {
        $user = $request->user();
        
        // Ensure only students can access this
        if (!$user->isStudent()) {
            abort(403, 'Access denied. This page is for students only.');
        }
        
        $query = Appointment::with(['user', 'counselor'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled', 'no_show', 'rejected', 'rescheduled']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Students search by counselor name
                $q->whereHas('counselor', function($counselorQuery) use ($search) {
                    $counselorQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                });
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

        return view('student.appointments.session-history', compact('appointments', 'stats'));
    }

    public function exportSessionHistory(Request $request)
    {
        $user = $request->user();
        
        // Ensure only students can access this
        if (!$user->isStudent()) {
            abort(403, 'Access denied. This page is for students only.');
        }
        
        $query = Appointment::with(['user', 'counselor'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled', 'no_show', 'rejected', 'rescheduled']);

        // Apply same filters as session history
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Students search by counselor name
                $q->whereHas('counselor', function($counselorQuery) use ($search) {
                    $counselorQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                });
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

        $filename = 'student_session_history_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
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
                'Counselor Name',
                'Type',
                'Category',
                'Status',
                'Reason for Urgency',
                'Purpose/Concern',
                'Counselor Notes',
                'Created At'
            ]);

            // CSV data
            foreach ($appointments as $appointment) {
                fputcsv($file, [
                    $appointment->appointment_date->format('Y-m-d'),
                    $appointment->start_time->format('H:i') . ' - ' . $appointment->end_time->format('H:i'),
                    $appointment->counselor->full_name,
                    ucfirst($appointment->type),
                    $appointment->getCounselingCategoryLabel(),
                    ucfirst($appointment->status),
                    $appointment->reason,
                    $appointment->notes,
                    $appointment->counselor_notes,
                    $appointment->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getAvailableSlots(User $counselor, Request $request)
    {
        $user = $request->user();
        
        // Ensure only students can access this
        if (!$user->isStudent()) {
            abort(403, 'Access denied. This page is for students only.');
        }
        
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
}
