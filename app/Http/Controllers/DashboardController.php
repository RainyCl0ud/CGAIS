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

        if ($user->isCounselor()) {
            // Counselor Dashboard (Full Privileges)
            $stats = [
                'total_appointments' => Appointment::where('counselor_id', $user->id)->count(),
                'pending_appointments' => Appointment::where('counselor_id', $user->id)->pending()->count(),
                'today_appointments' => Appointment::where('counselor_id', $user->id)
                    ->where('appointment_date', Carbon::today())
                    ->where('status', '!=', 'cancelled')
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

        } elseif ($user->isAssistant()) {
            // Assistant Dashboard (Limited Privileges)
            $stats = [
                'total_appointments' => Appointment::where('counselor_id', $user->id)->count(),
                'pending_appointments' => Appointment::where('counselor_id', $user->id)->pending()->count(),
                'today_appointments' => Appointment::where('counselor_id', $user->id)
                    ->where('appointment_date', Carbon::today())
                    ->where('status', '!=', 'cancelled')
                    ->count(),
                'unread_notifications' => $user->getUnreadNotificationsCount(),
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

        } else {
            // Student/Faculty Dashboard
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

            $upcomingAppointments = $user->getUpcomingAppointments()
                ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
                ->limit(5)
                ->get();
        }

        // Route to role-specific dashboard
        if ($user->isStudent()) {
            return view('dashboard.student', compact('stats', 'recentAppointments', 'upcomingAppointments'));
        } elseif ($user->isCounselor()) {
            return view('dashboard.counselor', compact('stats', 'recentAppointments', 'upcomingAppointments'));
        } elseif ($user->isAssistant()) {
            return view('dashboard.assistant', compact('stats', 'recentAppointments', 'upcomingAppointments'));
        } else {
            return view('dashboard.faculty', compact('stats', 'recentAppointments', 'upcomingAppointments'));
        }
    }

    public function todayAppointments(Request $request): View
    {
        $user = $request->user();
        
        if (!$user->isCounselor() && !$user->isAssistant()) {
            abort(403, 'Only counselors and assistants can view today\'s appointments.');
        }

        $todayAppointments = Appointment::with('user')
            ->where('counselor_id', $user->id)
            ->where('appointment_date', Carbon::today())
            ->where('status', '!=', 'cancelled')
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

        $pendingAppointments = Appointment::with('user')
            ->where('counselor_id', $user->id)
            ->where('status', 'pending')
            ->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END") // Urgent first
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();

        return view('appointments.pending', compact('pendingAppointments'));
    }
} 