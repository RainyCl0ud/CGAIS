<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Notification;
use Illuminate\View\View;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $appointments = collect();

        if ($user->isCounselor() || $user->isAssistant()) {
            $appointments = Appointment::with(['user', 'counselor'])
                ->where('counselor_id', $user->id)
                ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END")
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->paginate(10);
        } else {
            $appointments = $user->appointments()
                ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END")
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->paginate(10);
        }

        return view('appointments.index', compact('appointments'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        
        if ($user->isCounselor() || $user->isAssistant()) {
            return redirect()->route('appointments.index')
                ->with('error', 'Counselors and assistants cannot book appointments.');
        }

        $counselors = User::where('role', 'counselor')->get();
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

        if ($user->isStudent()) {
            $validationRules['counseling_category'] = 'required|in:conduct_intake_interview,information_services,internal_referral_services,counseling_services,conduct_exit_interview';
        }

        try {
            $validatedData = $request->validate($validationRules);
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

        $appointmentDate = Carbon::parse($request->appointment_date);
        $dayOfWeek = $appointmentDate->dayOfWeek;

        if ($dayOfWeek === 0 || $dayOfWeek === 6) {
            return back()->withErrors(['appointment_date' => 'Appointments are only available from Monday to Friday.']);
        }

        $schedule = Schedule::where('counselor_id', $request->counselor_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$schedule) {
            return back()->withErrors(['appointment_date' => 'Counselor is not available on this date.']);
        }

        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $scheduleStart = Carbon::parse($schedule->start_time);
        $scheduleEnd = Carbon::parse($schedule->end_time);

        if ($startTime < $scheduleStart || $endTime > $scheduleEnd) {
            return back()->withErrors(['start_time' => 'Appointment time must be within counselor\'s schedule.']);
        }

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

        if ($existingAppointment && $request->type !== 'urgent') {
            return back()->withErrors(['start_time' => 'This time slot is already booked.']);
        }

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

        if ($user->isStudent()) {
            $appointmentData['counseling_category'] = $request->counseling_category;
        } else {
            $appointmentData['counseling_category'] = 'consultation';
        }

        if ($request->type === 'urgent' && !empty($conflictNote)) {
            $appointmentData['counselor_notes'] = $conflictNote;
        }

        $appointment = Appointment::create($appointmentData);

        $successMessage = $request->type === 'urgent'
            ? 'URGENT appointment requested successfully. The counselor will review your request and may contact you for immediate assistance.'
            : 'Appointment requested successfully. Please wait for confirmation.';

        return redirect()->route('appointments.index')->with('success', $successMessage);
    }

    public function show(Appointment $appointment, Request $request): View
    {
        $user = $request->user();
        if (!$user->isCounselor() && !$user->isAssistant() && $appointment->user_id !== $user->id) {
            abort(403);
        }
        if (($user->isCounselor() || $user->isAssistant()) && $appointment->counselor_id !== $user->id) {
            abort(403);
        }
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment, Request $request): View
    {
        $user = $request->user();
        if (!$user->isCounselor() && !$user->isAssistant() && $appointment->user_id !== $user->id) {
            abort(403);
        }
        if (($user->isCounselor() || $user->isAssistant()) && $appointment->counselor_id !== $user->id) {
            abort(403);
        }
        $counselors = User::where('role', 'counselor')->get();
        return view('appointments.edit', compact('appointment', 'counselors'));
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->isCounselor() || $user->isAssistant()) {
            if ($appointment->counselor_id !== $user->id) {
                abort(403, 'You can only update appointments assigned to you.');
            }
        } else {
            if ($appointment->user_id !== $user->id) {
                abort(403, 'You can only update your own appointments.');
            }
            if (!in_array($appointment->status, ['pending', 'confirmed'])) {
                return back()->with('error', 'You can only reschedule pending or confirmed appointments.');
            }
        }

        if ($request->has('status')) {
            $request->validate([
                'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
            ]);
            $currentStatus = $appointment->status;
            $newStatus = $request->status;
            $allowedTransitions = $this->getAllowedStatusTransitions($currentStatus);
            if (!in_array($newStatus, $allowedTransitions)) {
                return back()->withErrors(['status' => "Cannot change status from '{$currentStatus}' to '{$newStatus}'. Allowed transitions: " . implode(', ', $allowedTransitions)]);
            }
            if ($newStatus === 'no_show' && $appointment->getAppointmentDateTime()->isFuture()) {
                return back()->withErrors(['status' => 'Cannot mark as "No Show" for future appointments.']);
            }
            $appointment->update(['status' => $request->status]);
            return redirect()->route('appointments.show', $appointment)->with('success', 'Appointment status updated successfully.');
        }

        if ($user->isCounselor() || $user->isAssistant()) {
            $request->validate(['counselor_notes' => 'nullable|string|max:1000']);
            $appointment->update(['counselor_notes' => $request->counselor_notes]);
            return redirect()->route('appointments.show', $appointment)->with('success', 'Counselor notes updated successfully.');
        }

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

        $appointmentDate = Carbon::parse($request->appointment_date);
        $dayOfWeek = $appointmentDate->dayOfWeek;
        if ($dayOfWeek === 0 || $dayOfWeek === 6) {
            return back()->withErrors(['appointment_date' => 'Appointments are only available from Monday to Friday.']);
        }

        $counselor = User::find($request->counselor_id);
        if (!$counselor || $counselor->role !== 'counselor') {
            return back()->withErrors(['counselor_id' => 'Selected counselor is not available.']);
        }

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

        $appointment->update([
            'counselor_id' => $request->counselor_id,
            'appointment_date' => $request->appointment_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'counseling_category' => $request->counseling_category,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment rescheduled successfully. Please wait for counselor confirmation.');
    }

    private function getAllowedStatusTransitions(string $currentStatus): array
    {
        return match($currentStatus) {
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['cancelled', 'no_show'],
            'completed' => [],
            'cancelled' => [],
            'no_show' => [],
            default => [],
        };
    }

    public function destroy(Appointment $appointment, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->isCounselor() && !$user->isAssistant() && $appointment->user_id !== $user->id) {
            abort(403);
        }
        if (($user->isCounselor() || $user->isAssistant()) && $appointment->counselor_id !== $user->id) {
            abort(403);
        }
        $appointment->update(['status' => 'cancelled']);

        // Notify student of appointment cancellation by counselor
        $appointment->user->notifications()->create([
            'appointment_id' => $appointment->id,
            'title' => 'Appointment Cancelled by Counselor',
            'message' => "Your appointment on {$appointment->getFormattedDateTime()} has been cancelled by the counselor.",
            'type' => 'appointment_cancelled',
            'is_read' => false,
            'read_at' => null,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment cancelled successfully.');
    }

    public function cancel(Appointment $appointment, Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($appointment->user_id !== $user->id) {
            abort(403, 'You can only cancel your own appointments.');
        }
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed appointments can be cancelled.');
        }
        $appointment->update(['status' => 'cancelled']);

        // Notify counselor of appointment cancellation by student
        $appointment->counselor->notifications()->create([
            'appointment_id' => $appointment->id,
            'title' => 'Appointment Cancelled by Student',
            'message' => "The appointment with {$appointment->user->full_name} on {$appointment->appointment_date->format('M d, Y')} has been cancelled by the student.",
            'type' => 'appointment_cancelled',
            'is_read' => false,
            'read_at' => null,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment cancelled successfully.');
    }

    public function sessionHistory(Request $request): View
    {
        $user = $request->user();

        $query = Appointment::with(['user', 'counselor']);

        if ($user->isCounselor() || $user->isAssistant()) {
            $query->where('counselor_id', $user->id);
        } else {
            $query->where('user_id', $user->id);
        }

        $query->where('status', 'completed');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $user) {
                if ($user->isCounselor() || $user->isAssistant()) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%")
                                 ->orWhere('id', '=', $search);
                    });
                } else {
                    $q->whereHas('counselor', function($counselorQuery) use ($search) {
                        $counselorQuery->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%");
                    });
                }
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

        $sortBy = $request->get('sort_by', 'appointment_date');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['appointment_date', 'start_time', 'created_at', 'status', 'type'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'appointment_date';
        }

        $query->orderBy($sortBy, $sortOrder);

        $appointments = $query->paginate(15)->withQueryString();

        $stats = [
            'total_sessions' => $query->count(),
            'completed_sessions' => $query->count(),
        ];

        return view('appointments.session-history', compact('appointments', 'stats'));
    }

    public function exportSessionHistory(Request $request)
    {
        $user = $request->user();
        
        $query = Appointment::with(['user', 'counselor']);
        
        if ($user->isCounselor() || $user->isAssistant()) {
            $query->where('counselor_id', $user->id);
        } else {
            $query->where('user_id', $user->id);
        }

        $query->where('status', 'completed');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $user) {
                if ($user->isCounselor() || $user->isAssistant()) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%")
                                 ->orWhere('id', '=', $search);
                    });
                } else {
                    $q->whereHas('counselor', function($counselorQuery) use ($search) {
                        $counselorQuery->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%");
                    });
                }
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
            $dayOfWeek = $date->dayOfWeek;
            if ($dayOfWeek === 0 || $dayOfWeek === 6) {
                continue;
            }
            $availableCounselors = Schedule::where('day_of_week', $dayOfWeek)
                ->where('is_available', true)
                ->count();
            if ($availableCounselors > 0) {
                $dates[] = $date->format('Y-m-d');
            }
        }
        return $dates;
    }

    public function approve(Appointment $appointment): RedirectResponse
    {
        if (!Auth::user()->canApproveAppointments()) {
            return redirect()->route('appointments.index')
                ->with('error', 'You do not have permission to approve appointments.');
        }

        $appointment->update([
            'status' => 'confirmed',
            'counselor_notes' => $appointment->counselor_notes . "\n\n[Approved on " . now()->format('M d, Y g:i A') . "]"
        ]);

        // Notify the approved student
        $appointment->user->notifications()->create([
            'appointment_id' => $appointment->id,
            'title' => 'Appointment Approved',
            'message' => "Your appointment on {$appointment->getFormattedDateTime()} has been approved.",
            'type' => 'appointment_approved',
            'is_read' => false,
            'read_at' => null,
        ]);

        // Cancel all other pending appointments for the same counselor and date
        $otherPendingAppointments = Appointment::where('counselor_id', $appointment->counselor_id)
            ->where('appointment_date', $appointment->appointment_date)
            ->where('status', 'pending')
            ->where('id', '!=', $appointment->id)
            ->get();

        foreach ($otherPendingAppointments as $pendingAppointment) {
            $pendingAppointment->update(['status' => 'cancelled']);

            // Notify each student whose appointment was cancelled
            $pendingAppointment->user->notifications()->create([
                'appointment_id' => $pendingAppointment->id,
                'title' => 'Appointment Automatically Cancelled',
                'message' => "Your appointment request for {$pendingAppointment->appointment_date->format('M d, Y')} has been automatically cancelled because another appointment was confirmed for that day.",
                'type' => 'appointment_cancelled',
                'is_read' => false,
                'read_at' => null,
            ]);
        }

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment approved successfully. Other pending requests for this day have been cancelled.');
    }

    public function reject(Request $request, Appointment $appointment): RedirectResponse
    {
      if (!Auth::check() || !Auth::user()->canApproveAppointments()) {
    return redirect()->route('appointments.index')
        ->with('error', 'You do not have permission to reject appointments.');
}


        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $appointment->update([
            'status' => 'cancelled',
            'counselor_notes' => $appointment->counselor_notes . "\n\n[Rejected on " . now()->format('M d, Y g:i A') . " - Reason: {$request->rejection_reason}]"
        ]);

        $appointment->user->notifications()->create([
            'appointment_id' => $appointment->id,
            'title' => 'Appointment Rejected',
            'message' => "Your appointment on {$appointment->getFormattedDateTime()} has been rejected. Reason: {$request->rejection_reason}",
            'type' => 'appointment_rejected',
            'is_read' => false,
            'read_at' => null,
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment rejected successfully.');
    }

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
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        if ($dayOfWeek === 0 || $dayOfWeek === 6) {
            return response()->json([
                'slots' => [],
                'message' => 'Appointments are only available from Monday to Friday.'
            ]);
        }
        
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
        
        $slots = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $currentTime = $startTime->copy();
        
        while ($currentTime < $endTime) {
            $slotTime = $currentTime->format('H:i');
            $slotEndTime = $currentTime->copy()->addMinutes(30)->format('H:i');
            
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
                
            if (!$existingAppointment || $isUrgent) {
                $isConflict = $existingAppointment ? true : false;
                $slotEndTimeFormatted = $currentTime->copy()->addMinutes(30)->format('g:i A');
                $slots[] = [
                    'time' => $slotTime,
                    'end_time' => $slotEndTime,
                    'formatted_time' => $currentTime->format('g:i A') . ' - ' . $slotEndTimeFormatted,
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
