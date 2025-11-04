<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $stats = [];
        $recentAppointments = collect();
        $upcomingAppointments = collect();
        $nextAppointment = null;

        if ($user->isCounselor()) {
            // Counselor Dashboard (Full Privileges)
            $stats = [
                'total_appointments' => Appointment::where('counselor_id', $user->id)->count(),
                'pending_appointments' => Appointment::where('counselor_id', $user->id)->pending()->count(),
                'today_appointments' => Appointment::where('counselor_id', $user->id)
                    ->where('appointment_date', Carbon::today())
                    ->where('status',  'confirmed')
                    ->count(),
                'unread_notifications' => $user->getUnreadNotificationsCount(),
                'total_users' => User::count(),
            ];

            $recentAppointments = Appointment::with('user')
                ->where('counselor_id', $user->id)
                ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $upcomingAppointments = Appointment::with('user')
                ->where('counselor_id', $user->id)
                ->upcoming()
                ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->limit(5)
                ->get();

$nextAppointment = Appointment::with('user')
    ->where('counselor_id', $user->id)
    ->where(function ($query) {
        $query->where('status', 'confirmed')
              ->orWhere(function ($query) {
                  $query->where('status', 'pending')
                        ->whereRaw("STR_TO_DATE(CONCAT(appointment_date, ' ', start_time), '%Y-%m-%d %H:%i:%s') >= NOW()");
              });
    })
    ->orderBy('appointment_date')
    ->orderBy('start_time')
    ->first();

        } elseif ($user->isAssistant()) {
            // Assistant Dashboard (System-wide visibility like Counselor)
            $stats = [
                'total_appointments' => Appointment::count(),
                'pending_appointments' => Appointment::pending()->count(),
                'today_appointments' => Appointment::where('appointment_date', Carbon::today())
                    ->where('status', 'confirmed')
                    ->count(),
                'unread_notifications' => $user->getUnreadNotificationsCount(),
            ];

            // Assistants see all appointments system-wide
            $recentAppointments = Appointment::with('user')
                ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $upcomingAppointments = Appointment::with('user')
                ->upcoming()
                ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->limit(5)
                ->get();

            $nextAppointment = Appointment::with('user')
                ->upcoming()
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->first();

        } else {
            // Student/Faculty/Staff Dashboard
            $stats = [
                'total_appointments' => $user->appointments()->count(),
                'pending_appointments' => $user->appointments()->pending()->count(),
                'upcoming_appointments' => $user->appointments()->upcoming()->count(),
                'completed_appointments' => $user->appointments()->completed()->count(),
                'unread_notifications' => $user->getUnreadNotificationsCount(),
            ];

            $recentAppointments = $user->appointments()
                ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $pendingAppointments = $user->appointments()
                ->pending()
                ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->limit(5)
                ->get();

            $upcomingApprovedAppointments = $user->appointments()
                ->where('status', 'confirmed')
                ->where('appointment_date', '>=', now()->toDateString())
                ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->limit(5)
                ->get();
        }

        // Route to role-specific dashboard
        if ($user->isStudent()) {
            return view('dashboard.student', compact('stats', 'recentAppointments', 'pendingAppointments', 'upcomingApprovedAppointments'));
        } elseif ($user->isCounselor()) {
            return view('dashboard.counselor', compact('stats', 'recentAppointments', 'upcomingAppointments', 'nextAppointment'));
        } elseif ($user->isAssistant()) {
            return view('dashboard.assistant', compact('stats', 'recentAppointments', 'upcomingAppointments', 'nextAppointment'));
        } elseif ($user->isStaff()) {
            return view('dashboard.staff', compact('stats', 'recentAppointments', 'pendingAppointments', 'upcomingApprovedAppointments'));
        } else {
            return view('dashboard.faculty', compact('stats', 'recentAppointments', 'pendingAppointments', 'upcomingApprovedAppointments'));
        }
    }

    public function todayAppointments(Request $request): View
    {
        $user = $request->user();
        
        if (!$user->isCounselor() && !$user->isAssistant()) {
            abort(403, 'Only counselors and assistants can view today\'s appointments.');
        }

        // Assistants see all today's appointments system-wide
        $todayAppointments = Appointment::with('user')
            ->where('appointment_date', Carbon::today())
            ->where('status', 'confirmed')
            ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
            ->orderBy('start_time')
            ->get();

        return view('appointments.today', compact('todayAppointments'));
    }

    public function pendingAppointments(Request $request): View
    {
        $user = $request->user();
        
        if (!$user->isCounselor() && !$user->isAssistant()) {
            abort(403, 'Only counselors and assistants can view pending appointments.');
        }

        // Assistants see all pending appointments system-wide
        $pendingAppointments = Appointment::with('user')
            ->where('status', 'pending')
            ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();

        return view('appointments.pending', compact('pendingAppointments'));
    }
} 