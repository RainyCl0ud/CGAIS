<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Notifications\AppointmentStatusNotification;
use App\Notifications\AssistantAppointmentNotification;

class AppointmentManager extends Component
{
    public $activeTab = 'appointments'; // 'appointments' or 'history'
    public $appointments = [];
    public $sessionHistory = [];
    public $stats = [];

    // For appointment management
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $showRescheduleModal = false;
    public $selectedAppointment = null;

    // Form fields
    public $counselor_id;
    public $appointment_date;
    public $start_time;
    public $end_time;
    public $type = 'regular';
    public $counseling_category;
    public $reason;
    public $notes;
    public $counselor_notes;
    public $rejection_reason;
    public $reschedule_reason;
    public $new_appointment_date;
    public $new_start_time;
    public $new_end_time;

    // Filters
    public $search = '';
    public $status_filter = 'all';
    public $type_filter = 'all';
    public $category_filter = 'all';
    public $date_from;
    public $date_to;
    public $sort_by = 'appointment_date';
    public $sort_order = 'desc';

    protected $listeners = [
        'refreshAppointments' => 'loadAppointments',
        'refreshHistory' => 'loadSessionHistory'
    ];

    public function mount()
    {
        $this->loadAppointments();
        $this->loadSessionHistory();
        $this->loadStats();
    }

    public function loadAppointments()
    {
        $user = Auth::user();
        $query = Appointment::with(['user', 'counselor']);

        if ($user->isCounselor() || $user->isAssistant()) {
            $query->where('counselor_id', $user->id);
        } else {
            $query->where('user_id', $user->id);
        }

        // Apply filters
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('user', function($userQuery) {
                    $userQuery->where('first_name', 'like', "%{$this->search}%")
                             ->orWhere('last_name', 'like', "%{$this->search}%")
                             ->orWhere('email', 'like', "%{$this->search}%");
                })
                ->orWhereHas('counselor', function($counselorQuery) {
                    $counselorQuery->where('first_name', 'like', "%{$this->search}%")
                                  ->orWhere('last_name', 'like', "%{$this->search}%");
                })
                ->orWhere('reason', 'like', "%{$this->search}%")
                ->orWhere('notes', 'like', "%{$this->search}%")
                ->orWhere('counselor_notes', 'like', "%{$this->search}%");
            });
        }

        if ($this->status_filter !== 'all') {
            $query->where('status', $this->status_filter);
        }

        if ($this->type_filter !== 'all') {
            $query->where('type', $this->type_filter);
        }

        if ($this->category_filter !== 'all') {
            $query->where('counseling_category', $this->category_filter);
        }

        if ($this->date_from) {
            $query->where('appointment_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('appointment_date', '<=', $this->date_to);
        }

        $allowedSortFields = ['appointment_date', 'start_time', 'created_at', 'status', 'type'];
        if (!in_array($this->sort_by, $allowedSortFields)) {
            $this->sort_by = 'appointment_date';
        }

        $query->orderBy($this->sort_by, $this->sort_order)
              ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END");

        $this->appointments = $query->get();
    }

    public function loadSessionHistory()
    {
        $user = Auth::user();
        $query = Appointment::with(['user', 'counselor']);

        if ($user->isCounselor() || $user->isAssistant()) {
            $query->where('counselor_id', $user->id);
        } else {
            $query->where('user_id', $user->id);
        }

        $query->where('status', 'completed');

        // Apply same filters
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('user', function($userQuery) {
                    $userQuery->where('first_name', 'like', "%{$this->search}%")
                             ->orWhere('last_name', 'like', "%{$this->search}%")
                             ->orWhere('email', 'like', "%{$this->search}%");
                })
                ->orWhereHas('counselor', function($counselorQuery) {
                    $counselorQuery->where('first_name', 'like', "%{$this->search}%")
                                  ->orWhere('last_name', 'like', "%{$this->search}%");
                })
                ->orWhere('reason', 'like', "%{$this->search}%")
                ->orWhere('notes', 'like', "%{$this->search}%")
                ->orWhere('counselor_notes', 'like', "%{$this->search}%");
            });
        }

        if ($this->type_filter !== 'all') {
            $query->where('type', $this->type_filter);
        }

        if ($this->category_filter !== 'all') {
            $query->where('counseling_category', $this->category_filter);
        }

        if ($this->date_from) {
            $query->where('appointment_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('appointment_date', '<=', $this->date_to);
        }

        $query->orderBy($this->sort_by, $this->sort_order);

        $this->sessionHistory = $query->get();
    }

    public function loadStats()
    {
        $user = Auth::user();
        if (!$user->isCounselor() && !$user->isAssistant()) {
            return;
        }

        $query = Appointment::where('counselor_id', $user->id);

        $this->stats = [
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
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($appointmentId)
    {
        $this->selectedAppointment = Appointment::find($appointmentId);
        $this->populateForm();
        $this->showEditModal = true;
    }

    public function openApproveModal($appointmentId)
    {
        $this->selectedAppointment = Appointment::find($appointmentId);
        $this->showApproveModal = true;
    }

    public function openRejectModal($appointmentId)
    {
        $this->selectedAppointment = Appointment::find($appointmentId);
        $this->showRejectModal = true;
    }

    public function openRescheduleModal($appointmentId)
    {
        $this->selectedAppointment = Appointment::find($appointmentId);
        $this->showRescheduleModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showApproveModal = false;
        $this->showRejectModal = false;
        $this->showRescheduleModal = false;
        $this->selectedAppointment = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->counselor_id = '';
        $this->appointment_date = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->type = 'regular';
        $this->counseling_category = '';
        $this->reason = '';
        $this->notes = '';
        $this->counselor_notes = '';
        $this->rejection_reason = '';
        $this->reschedule_reason = '';
        $this->new_appointment_date = '';
        $this->new_start_time = '';
        $this->new_end_time = '';
    }

    private function populateForm()
    {
        if ($this->selectedAppointment) {
            $this->counselor_id = $this->selectedAppointment->counselor_id;
            $this->appointment_date = $this->selectedAppointment->appointment_date->format('Y-m-d');
            $this->start_time = $this->selectedAppointment->start_time->format('H:i');
            $this->end_time = $this->selectedAppointment->end_time->format('H:i');
            $this->type = $this->selectedAppointment->type;
            $this->counseling_category = $this->selectedAppointment->counseling_category;
            $this->reason = $this->selectedAppointment->reason;
            $this->notes = $this->selectedAppointment->notes;
            $this->counselor_notes = $this->selectedAppointment->counselor_notes;
        }
    }

    public function createAppointment()
    {
        $this->validate([
            'counselor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:regular,urgent,follow_up',
            'counseling_category' => 'required_if:type,regular|in:conduct_intake_interview,information_services,internal_referral_services,counseling_services,conduct_exit_interview',
            'reason' => 'required|string|max:1000',
        ]);

        $appointmentDate = Carbon::parse($this->appointment_date);
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        $counselor = User::find($this->counselor_id);
        if ($counselor && !$counselor->isAvailableForSlot($appointmentDate, $startTime, $endTime)) {
            $this->addError('start_time', 'Counselor is not available during the selected time range.');
            return;
        }

        $appointmentData = [
            'user_id' => Auth::id(),
            'counselor_id' => $this->counselor_id,
            'appointment_date' => $this->appointment_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'type' => $this->type,
            'counseling_category' => $this->counseling_category,
            'reason' => $this->reason,
            'status' => 'pending',
        ];

        $appointment = Appointment::create($appointmentData);

        // Send email notifications to all assistants about the new appointment
        $assistants = User::where('role', 'assistant')->get();
        foreach ($assistants as $assistant) {
            $assistant->notify(new AssistantAppointmentNotification($appointment, 'booked'));
        }

        session()->flash('success', 'Appointment created successfully.');
        $this->closeModals();
        $this->loadAppointments();
    }

    public function updateAppointment()
    {
        $this->validate([
            'counselor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:regular,urgent,follow_up',
            'counseling_category' => 'required_if:type,regular|in:conduct_intake_interview,information_services,internal_referral_services,counseling_services,conduct_exit_interview',
            'reason' => 'required|string|max:1000',
        ]);

        $appointmentDate = Carbon::parse($this->appointment_date);
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        $counselor = User::find($this->counselor_id);
        if ($counselor && !$counselor->isAvailableForSlot($appointmentDate, $startTime, $endTime)) {
            $this->addError('start_time', 'Counselor is not available during the selected time range.');
            return;
        }

        $this->selectedAppointment->update([
            'counselor_id' => $this->counselor_id,
            'appointment_date' => $this->appointment_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'type' => $this->type,
            'counseling_category' => $this->counseling_category,
            'reason' => $this->reason,
            'notes' => $this->notes,
            'status' => 'pending',
        ]);

        // Send email notifications to assistants about the rescheduled appointment
        $assistants = User::where('role', 'assistant')->get();
        foreach ($assistants as $assistant) {
            $assistant->notify(new AssistantAppointmentNotification($this->selectedAppointment, 'rescheduled', 'Rescheduled by user'));
        }

        session()->flash('success', 'Appointment updated successfully.');
        $this->closeModals();
        $this->loadAppointments();
    }

    public function approveAppointment()
    {
        if (!Auth::user()->canApproveAppointments()) {
            session()->flash('error', 'You do not have permission to approve appointments.');
            return;
        }

        $this->selectedAppointment->update([
            'status' => 'confirmed',
            'counselor_notes' => $this->selectedAppointment->counselor_notes . "\n\n[Approved on " . now()->format('M d, Y g:i A') . "]"
        ]);

        // Notify the student
        $this->selectedAppointment->user->notifications()->create([
            'appointment_id' => $this->selectedAppointment->id,
            'title' => 'Appointment Approved',
            'message' => "Your appointment on {$this->selectedAppointment->getFormattedDateTime()} has been approved.",
            'type' => 'appointment_approved',
            'is_read' => false,
            'read_at' => null,
        ]);

        // Send email notification to student (counselor is approving, so student receives email)
        $this->selectedAppointment->user->notify(new AppointmentStatusNotification($this->selectedAppointment, 'approved'));

        session()->flash('success', 'Appointment approved successfully.');
        $this->closeModals();
        $this->loadAppointments();
        $this->loadStats();
    }

    public function rejectAppointment()
    {
        $this->validate(['rejection_reason' => 'required|string|max:500']);

        if (!Auth::user()->canApproveAppointments()) {
            session()->flash('error', 'You do not have permission to reject appointments.');
            return;
        }

        $this->selectedAppointment->update([
            'status' => 'cancelled',
            'counselor_notes' => $this->selectedAppointment->counselor_notes . "\n\n[Rejected on " . now()->format('M d, Y g:i A') . " - Reason: {$this->rejection_reason}]"
        ]);

        $this->selectedAppointment->user->notifications()->create([
            'appointment_id' => $this->selectedAppointment->id,
            'title' => 'Appointment Rejected',
            'message' => "Your appointment on {$this->selectedAppointment->getFormattedDateTime()} has been rejected. Reason: {$this->rejection_reason}",
            'type' => 'appointment_rejected',
            'is_read' => false,
            'read_at' => null,
        ]);

        // Send email notification to student
        $this->selectedAppointment->user->notify(new AppointmentStatusNotification($this->selectedAppointment, 'cancelled', $this->rejection_reason));
        
        // Send email notification to counselor
        $this->selectedAppointment->counselor->notify(new AppointmentStatusNotification($this->selectedAppointment, 'cancelled', $this->rejection_reason));

        session()->flash('success', 'Appointment rejected successfully.');
        $this->closeModals();
        $this->loadAppointments();
        $this->loadStats();
    }

    public function rescheduleAppointment()
    {
        $this->validate([
            'new_appointment_date' => 'required|date|after_or_equal:today',
            'new_start_time' => 'required|date_format:H:i',
            'new_end_time' => 'required|date_format:H:i',
            'reschedule_reason' => 'required|string|max:500'
        ]);

        if (!Auth::user()->canApproveAppointments()) {
            session()->flash('error', 'You do not have permission to reschedule appointments.');
            return;
        }

        $appointmentDate = Carbon::parse($this->new_appointment_date);
        $startTime = Carbon::parse($this->new_start_time);
        $endTime = Carbon::parse($this->new_end_time);

        $counselor = $this->selectedAppointment->counselor;
        if ($counselor && !$counselor->isAvailableForSlot($appointmentDate, $startTime, $endTime)) {
            $this->addError('new_start_time', 'Counselor is not available during the selected time range.');
            return;
        }

        $oldDateTime = $this->selectedAppointment->getFormattedDateTime();

        $this->selectedAppointment->update([
            'appointment_date' => $this->new_appointment_date,
            'start_time' => $this->new_start_time,
            'end_time' => $this->new_end_time,
            'counselor_notes' => $this->selectedAppointment->counselor_notes . "\n\n[Rescheduled on " . now()->format('M d, Y g:i A') . " from {$oldDateTime} to " . $this->selectedAppointment->getFormattedDateTime() . " - Reason: {$this->reschedule_reason}]"
        ]);

        $this->selectedAppointment->user->notifications()->create([
            'appointment_id' => $this->selectedAppointment->id,
            'title' => 'Appointment Rescheduled',
            'message' => "Your appointment has been rescheduled from {$oldDateTime} to {$this->selectedAppointment->getFormattedDateTime()}. Reason: {$this->reschedule_reason}",
            'type' => 'appointment_rescheduled',
            'is_read' => false,
            'read_at' => null,
        ]);

        // Refresh the appointment to get updated dates
        $this->selectedAppointment->refresh();
        
        // Send email notification to student (counselor is rescheduling, so student receives email)
        $this->selectedAppointment->user->notify(new AppointmentStatusNotification($this->selectedAppointment, 'rescheduled', $this->reschedule_reason));

        session()->flash('success', 'Appointment rescheduled successfully.');
        $this->closeModals();
        $this->loadAppointments();
    }

    public function cancelAppointment($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);
        $user = Auth::user();

        if ($appointment->user_id !== $user->id && !($user->isCounselor() || $user->isAssistant())) {
            session()->flash('error', 'You can only cancel your own appointments.');
            return;
        }

        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            session()->flash('error', 'Only pending or confirmed appointments can be cancelled.');
            return;
        }

        $appointment->update(['status' => 'cancelled']);

        // Notify accordingly
        if ($user->isCounselor() || $user->isAssistant()) {
            $appointment->user->notifications()->create([
                'appointment_id' => $appointment->id,
                'title' => 'Appointment Cancelled by Counselor',
                'message' => "Your appointment on {$appointment->getFormattedDateTime()} has been cancelled by the counselor.",
                'type' => 'appointment_cancelled',
                'is_read' => false,
                'read_at' => null,
            ]);
            
            // Send email notification to student (counselor is cancelling, so student receives email)
            $appointment->user->notify(new AppointmentStatusNotification($appointment, 'cancelled', 'Cancelled by counselor'));
        } else {
            $appointment->counselor->notifications()->create([
                'appointment_id' => $appointment->id,
                'title' => 'Appointment Cancelled by Student',
                'message' => "The appointment with {$appointment->user->full_name} on {$appointment->appointment_date->format('M d, Y')} has been cancelled by the student.",
                'type' => 'appointment_cancelled',
                'is_read' => false,
                'read_at' => null,
            ]);
            
            // Send email notification to counselor (student is cancelling, so counselor receives email)
            $appointment->counselor->notify(new AppointmentStatusNotification($appointment, 'cancelled', 'Cancelled by student'));

            // Send email notifications to assistants
            $assistants = User::where('role', 'assistant')->get();
            foreach ($assistants as $assistant) {
                // Send system notification
                $assistant->notifications()->create([
                    'appointment_id' => $appointment->id,
                    'title' => 'Appointment Cancelled by Student',
                    'message' => "The appointment with {$appointment->user->full_name} on {$appointment->appointment_date->format('M d, Y')} has been cancelled by the student.",
                    'type' => 'appointment_cancelled',
                    'is_read' => false,
                    'read_at' => null,
                ]);
                
                // Send email notification
                $assistant->notify(new AssistantAppointmentNotification($appointment, 'cancelled', 'Cancelled by student'));
            }
        }

        session()->flash('success', 'Appointment cancelled successfully.');
        $this->loadAppointments();
        $this->loadStats();
    }

    public function updateStatus($appointmentId, $status)
    {
        $appointment = Appointment::find($appointmentId);
        $user = Auth::user();

        if ($user->isCounselor() || $user->isAssistant()) {
            if ($appointment->counselor_id !== $user->id) {
                session()->flash('error', 'You can only update appointments assigned to you.');
                return;
            }
        } else {
            if ($appointment->user_id !== $user->id) {
                session()->flash('error', 'You can only update your own appointments.');
                return;
            }
            if (!in_array($appointment->status, ['pending', 'confirmed'])) {
                session()->flash('error', 'You can only update pending or confirmed appointments.');
                return;
            }
        }

        $allowedTransitions = [
            'pending' => ['confirmed', 'cancelled', 'on_hold'],
            'confirmed' => ['cancelled', 'no_show', 'on_hold'],
            'on_hold' => ['confirmed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
            'no_show' => [],
        ];

        if (!in_array($status, $allowedTransitions[$appointment->status] ?? [])) {
            session()->flash('error', "Cannot change status from '{$appointment->status}' to '{$status}'.");
            return;
        }

        if ($status === 'no_show' && $appointment->getAppointmentDateTime()->isFuture()) {
            session()->flash('error', 'Cannot mark as "No Show" for future appointments.');
            return;
        }

        $appointment->update(['status' => $status]);

        session()->flash('success', 'Appointment status updated successfully.');
        $this->loadAppointments();
        $this->loadStats();
    }

    public function updatedSearch()
    {
        $this->loadAppointments();
        $this->loadSessionHistory();
    }

    public function updatedStatusFilter()
    {
        $this->loadAppointments();
    }

    public function updatedTypeFilter()
    {
        $this->loadAppointments();
        $this->loadSessionHistory();
    }

    public function updatedCategoryFilter()
    {
        $this->loadAppointments();
        $this->loadSessionHistory();
    }

    public function updatedDateFrom()
    {
        $this->loadAppointments();
        $this->loadSessionHistory();
    }

    public function updatedDateTo()
    {
        $this->loadAppointments();
        $this->loadSessionHistory();
    }

    public function updatedSortBy()
    {
        $this->loadAppointments();
        $this->loadSessionHistory();
    }

    public function updatedSortOrder()
    {
        $this->loadAppointments();
        $this->loadSessionHistory();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status_filter = 'all';
        $this->type_filter = 'all';
        $this->category_filter = 'all';
        $this->date_from = null;
        $this->date_to = null;
        $this->sort_by = 'appointment_date';
        $this->sort_order = 'desc';
        
        $this->loadAppointments();
        $this->loadSessionHistory();
    }

    public function markAsCompleted($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);
        $user = Auth::user();
        
        if (!$user->isCounselor() && !$user->isAssistant()) {
            session()->flash('error', 'You do not have permission to mark appointments as completed.');
            return;
        }

        if ($appointment->status !== 'confirmed') {
            session()->flash('error', 'Only confirmed appointments can be marked as completed.');
            return;
        }

        if ($appointment->getAppointmentDateTime()->isFuture()) {
            session()->flash('error', 'Cannot mark future appointments as completed.');
            return;
        }

        $appointment->update([
            'status' => 'completed',
            'counselor_notes' => $appointment->counselor_notes . "\n\n[Marked as Completed on " . now()->format('M d, Y g:i A') . "]"
        ]);

        // Notify the student that their appointment is completed
        $appointment->user->notifications()->create([
            'appointment_id' => $appointment->id,
            'title' => 'Appointment Completed',
            'message' => "Your appointment on {$appointment->getFormattedDateTime()} has been marked as completed.",
            'type' => 'general',
            'is_read' => false,
            'read_at' => null,
        ]);

        session()->flash('success', 'Appointment marked as completed and moved to session history.');
        $this->loadAppointments();
        $this->loadSessionHistory();
        $this->loadStats();
    }

    public function render()
    {
        $counselors = User::where('role', 'counselor')->get();
        return view('livewire.appointment-manager', compact('counselors'));
    }
}
