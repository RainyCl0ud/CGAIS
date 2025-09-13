<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
 public function index(Request $request): View
{
    $user = $request->user();
    
    $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

    // Filter by user if not admin
    if (!$user->isCounselor() && !$user->isAssistant()) {
        $query->where('user_id', $user->id);
    }

    // Filter by action
    if ($request->filled('action')) {
        $query->where('action', $request->action);
    }

    // Filter by model type
    if ($request->filled('model_type')) {
        $query->where('model_type', $request->model_type);
    }

    // Filter by date range
    if ($request->filled('date_from')) {
        $query->where('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
    }

    // Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
              ->orWhereHas('user', function($userQuery) use ($search) {
                  $userQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    $activities = $query->paginate(20)->withQueryString();

    // Get available actions for filter
    $availableActions = ActivityLog::distinct('action')->pluck('action')->sort();
    
    // Get available model types for filter
    $availableModelTypes = ActivityLog::distinct('model_type')->pluck('model_type')->sort();

    // --- Stats (respect current filters) ---
    $totalLogs   = (clone $query)->count();
    $todayLogs   = (clone $query)->whereDate('created_at', now())->count();
    $weekLogs    = (clone $query)->where('created_at', '>=', now()->startOfWeek())->count();
    $activeUsers = (clone $query)->distinct('user_id')->count('user_id');

    return view('activity-logs.index', compact(
        'activities',
        'availableActions',
        'availableModelTypes',
        'totalLogs',
        'todayLogs',
        'weekLogs',
        'activeUsers'
    ));
}
    public function show(ActivityLog $activityLog, Request $request): View
    {
        $user = $request->user();
        
        // Check if user can view this activity log
        if (!$user->isCounselor() && !$user->isAssistant() && $activityLog->user_id !== $user->id) {
            abort(403);
        }

        return view('activity-logs.show', compact('activityLog'));
    }

    public function userActivity(Request $request): View
    {
        $user = $request->user();
        
        // Build query with filters
        $query = ActivityLog::where('user_id', $user->id);
        
        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        // Filter by date range
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->startOfMonth());
                    break;
            }
        }
        
        $activities = $query->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate statistics
        $totalActivities = ActivityLog::where('user_id', $user->id)->count();
        $todayActivities = ActivityLog::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();
        $weekActivities = ActivityLog::where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();
        
        // Activity distribution
        $activityDistribution = ActivityLog::where('user_id', $user->id)
            ->selectRaw('action, count(*) as count')
            ->groupBy('action')
            ->pluck('count', 'action')
            ->toArray();

        return view('activity-logs.user-activity', compact(
            'activities', 
            'totalActivities', 
            'todayActivities', 
            'weekActivities', 
            'activityDistribution'
        ));
    }

    public function export(Request $request)
    {
        $user = $request->user();
        
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filter by user if not admin
        if (!$user->isCounselor() && !$user->isAssistant()) {
            $query->where('user_id', $user->id);
        }

        // Apply filters
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $activities = $query->get();

        $filename = 'activity_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Date & Time',
                'User',
                'Action',
                'Description',
                'Model Type',
                'Model ID',
                'IP Address',
                'User Agent'
            ]);

            // CSV data
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $activity->user->full_name,
                    $activity->getActionLabel(),
                    $activity->description,
                    $activity->getModelLabel(),
                    $activity->model_id,
                    $activity->ip_address,
                    $activity->user_agent
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
