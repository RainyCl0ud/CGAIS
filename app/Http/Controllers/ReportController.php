<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\FeedbackForm;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        
        if (!$user->isCounselor() && !$user->isAssistant()) {
            abort(403, 'Only counselors and assistants can access reports.');
        }

        // Get date range for filtering
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));

        // Appointment statistics
        $appointmentStats = $this->getAppointmentStatistics($user->id, $dateFrom, $dateTo);
        
        // Monthly trends
        $monthlyTrends = $this->getMonthlyTrends($user->id);
        
        // Category distribution
        $categoryDistribution = $this->getCategoryDistribution($user->id, $dateFrom, $dateTo);
        
        // Client statistics
        $clientStats = $this->getClientStatistics($user->id, $dateFrom, $dateTo);
        
        // Feedback analytics
        $feedbackStats = $this->getFeedbackStatistics($user->id, $dateFrom, $dateTo);

        return view('reports.index', compact(
            'appointmentStats',
            'monthlyTrends',
            'categoryDistribution',
            'clientStats',
            'feedbackStats',
            'dateFrom',
            'dateTo'
        ));
    }

    public function appointmentReport(Request $request): View
    {
        $user = $request->user();
        
        if (!$user->isCounselor() && !$user->isAssistant()) {
            abort(403);
        }

        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));

        $appointments = Appointment::with(['user', 'counselor'])
            ->where('counselor_id', $user->id)
            ->whereBetween('appointment_date', [$dateFrom, $dateTo])
            ->orderBy('appointment_date', 'desc')
            ->paginate(20);

        $summary = [
            'total' => $appointments->total(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'no_show' => $appointments->where('status', 'no_show')->count(),
            'urgent' => $appointments->where('type', 'urgent')->count(),
        ];

        return view('reports.appointments', compact('appointments', 'summary', 'dateFrom', 'dateTo'));
    }

    public function clientReport(Request $request): View
    {
        $user = $request->user();
        
        if (!$user->isCounselor() && !$user->isAssistant()) {
            abort(403);
        }

        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));

        $clients = DB::table('appointments')
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                DB::raw('COUNT(appointments.id) as total_appointments'),
                DB::raw('SUM(CASE WHEN appointments.status = "completed" THEN 1 ELSE 0 END) as completed_appointments'),
                DB::raw('SUM(CASE WHEN appointments.status = "cancelled" THEN 1 ELSE 0 END) as cancelled_appointments'),
                DB::raw('SUM(CASE WHEN appointments.status = "no_show" THEN 1 ELSE 0 END) as no_show_appointments'),
                DB::raw('MAX(appointments.appointment_date) as last_appointment')
            )
            ->where('appointments.counselor_id', $user->id)
            ->whereBetween('appointments.appointment_date', [$dateFrom, $dateTo])
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.email')
            ->orderBy('total_appointments', 'desc')
            ->paginate(20);

        return view('reports.clients', compact('clients', 'dateFrom', 'dateTo'));
    }

    public function feedbackReport(Request $request): View
    {
        $user = $request->user();
        
        if (!$user->isCounselor() && !$user->isAssistant()) {
            abort(403);
        }

        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));

        $feedback = FeedbackForm::with(['user', 'appointment'])
            ->where('counselor_id', $user->id)
            ->whereHas('appointment', function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('appointment_date', [$dateFrom, $dateTo]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $summary = [
            'total_feedback' => $feedback->total(),
            'average_rating' => $feedback->avg('overall_satisfaction'),
            'recommendation_rate' => $feedback->where('would_recommend', true)->count() / max($feedback->total(), 1) * 100,
        ];

        return view('reports.feedback', compact('feedback', 'summary', 'dateFrom', 'dateTo'));
    }

    public function exportReport(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isCounselor() && !$user->isAssistant()) {
            abort(403);
        }

        $reportType = $request->get('type', 'appointments');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));

        $filename = "{$reportType}_report_{$dateFrom}_to_{$dateTo}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportType, $user, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            switch ($reportType) {
                case 'appointments':
                    $this->exportAppointmentsData($file, $user->id, $dateFrom, $dateTo);
                    break;
                case 'clients':
                    $this->exportClientsData($file, $user->id, $dateFrom, $dateTo);
                    break;
                case 'feedback':
                    $this->exportFeedbackData($file, $user->id, $dateFrom, $dateTo);
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getAppointmentStatistics($counselorId, $dateFrom, $dateTo): array
    {
        $query = Appointment::where('counselor_id', $counselorId)
            ->whereBetween('appointment_date', [$dateFrom, $dateTo]);

        return [
            'total' => $query->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'cancelled' => $query->where('status', 'cancelled')->count(),
            'no_show' => $query->where('status', 'no_show')->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'confirmed' => $query->where('status', 'confirmed')->count(),
            'urgent' => $query->where('type', 'urgent')->count(),
            'regular' => $query->where('type', 'regular')->count(),
            'follow_up' => $query->where('type', 'follow_up')->count(),
        ];
    }

    private function getMonthlyTrends($counselorId): array
    {
        return DB::table('appointments')
            ->select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled')
            )
            ->where('counselor_id', $counselorId)
            ->where('appointment_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    private function getCategoryDistribution($counselorId, $dateFrom, $dateTo): array
    {
        return DB::table('appointments')
            ->select('counseling_category', DB::raw('COUNT(*) as count'))
            ->where('counselor_id', $counselorId)
            ->whereBetween('appointment_date', [$dateFrom, $dateTo])
            ->whereNotNull('counseling_category')
            ->groupBy('counseling_category')
            ->get()
            ->toArray();
    }

    private function getClientStatistics($counselorId, $dateFrom, $dateTo): array
    {
        $totalClients = DB::table('appointments')
            ->where('counselor_id', $counselorId)
            ->whereBetween('appointment_date', [$dateFrom, $dateTo])
            ->distinct('user_id')
            ->count('user_id');

        $newClients = DB::table('appointments')
            ->where('counselor_id', $counselorId)
            ->whereBetween('appointment_date', [$dateFrom, $dateTo])
            ->whereNotExists(function($query) use ($counselorId, $dateFrom) {
                $query->select(DB::raw(1))
                      ->from('appointments as a2')
                      ->where('a2.counselor_id', $counselorId)
                      ->where('a2.user_id', DB::raw('appointments.user_id'))
                      ->where('a2.appointment_date', '<', $dateFrom);
            })
            ->distinct('user_id')
            ->count('user_id');

        return [
            'total_clients' => $totalClients,
            'new_clients' => $newClients,
            'returning_clients' => $totalClients - $newClients,
        ];
    }

    private function getFeedbackStatistics($counselorId, $dateFrom, $dateTo): array
    {
        $feedback = FeedbackForm::where('counselor_id', $counselorId)
            ->whereHas('appointment', function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('appointment_date', [$dateFrom, $dateTo]);
            });

        return [
            'total_feedback' => $feedback->count(),
            'average_rating' => $feedback->avg('overall_satisfaction'),
            'recommendation_rate' => $feedback->where('would_recommend', true)->count() / max($feedback->count(), 1) * 100,
            'counselor_rating' => $feedback->avg('counselor_rating'),
            'service_rating' => $feedback->avg('service_rating'),
            'facility_rating' => $feedback->avg('facility_rating'),
        ];
    }

    private function exportAppointmentsData($file, $counselorId, $dateFrom, $dateTo): void
    {
        fputcsv($file, [
            'Date', 'Time', 'Client Name', 'Client Email', 'Type', 'Category', 'Status', 
            'Reason', 'Counselor Notes', 'Created At'
        ]);

        $appointments = Appointment::with('user')
            ->where('counselor_id', $counselorId)
            ->whereBetween('appointment_date', [$dateFrom, $dateTo])
            ->orderBy('appointment_date', 'desc')
            ->get();

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
    }

    private function exportClientsData($file, $counselorId, $dateFrom, $dateTo): void
    {
        fputcsv($file, [
            'Client Name', 'Email', 'Total Appointments', 'Completed', 'Cancelled', 
            'No Show', 'Last Appointment'
        ]);

        $clients = DB::table('appointments')
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->select(
                'users.first_name',
                'users.last_name',
                'users.email',
                DB::raw('COUNT(appointments.id) as total_appointments'),
                DB::raw('SUM(CASE WHEN appointments.status = "completed" THEN 1 ELSE 0 END) as completed_appointments'),
                DB::raw('SUM(CASE WHEN appointments.status = "cancelled" THEN 1 ELSE 0 END) as cancelled_appointments'),
                DB::raw('SUM(CASE WHEN appointments.status = "no_show" THEN 1 ELSE 0 END) as no_show_appointments'),
                DB::raw('MAX(appointments.appointment_date) as last_appointment')
            )
            ->where('appointments.counselor_id', $counselorId)
            ->whereBetween('appointments.appointment_date', [$dateFrom, $dateTo])
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.email')
            ->orderBy('total_appointments', 'desc')
            ->get();

        foreach ($clients as $client) {
            fputcsv($file, [
                $client->first_name . ' ' . $client->last_name,
                $client->email,
                $client->total_appointments,
                $client->completed_appointments,
                $client->cancelled_appointments,
                $client->no_show_appointments,
                $client->last_appointment
            ]);
        }
    }

    private function exportFeedbackData($file, $counselorId, $dateFrom, $dateTo): void
    {
        fputcsv($file, [
            'Client Name', 'Date', 'Counselor Rating', 'Service Rating', 'Facility Rating', 
            'Overall Satisfaction', 'Would Recommend', 'Feedback', 'Created At'
        ]);

        $feedback = FeedbackForm::with(['user', 'appointment'])
            ->where('counselor_id', $counselorId)
            ->whereHas('appointment', function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('appointment_date', [$dateFrom, $dateTo]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($feedback as $item) {
            fputcsv($file, [
                $item->user->full_name,
                $item->appointment->appointment_date->format('Y-m-d'),
                $item->counselor_rating,
                $item->service_rating,
                $item->facility_rating,
                $item->overall_satisfaction,
                $item->would_recommend ? 'Yes' : 'No',
                $item->counselor_feedback,
                $item->created_at->format('Y-m-d H:i:s')
            ]);
        }
    }
}
