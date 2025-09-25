public function manage(Request $request): View
    {
        $user = $request->user();
        $activeTab = $request->get('tab', 'appointments');

        // Build query based on user role and active tab
        $query = Appointment::with(['user', 'counselor']);

        if ($user->isCounselor() || $user->isAssistant()) {
            $query->where('counselor_id', $user->id);
        } else {
            $query->where('user_id', $user->id);
        }

        // Filter by tab
        if ($activeTab === 'history') {
            $query->where('status', 'completed');
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $user) {
                if ($user->isCounselor() || $user->isAssistant()) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                    });
                } else {
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

        // Sorting
        $sortBy = $request->get('sort_by', 'appointment_date');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortFields = ['appointment_date', 'start_time', 'created_at', 'status', 'type'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'appointment_date';
        }

        $query->orderByRaw("CASE WHEN type = 'urgent' THEN 0 ELSE 1 END")
              ->orderBy($sortBy, $sortOrder);

        $appointments = $query->paginate(15)->withQueryString();

        // Statistics for counselors/assistants
        $stats = [];
        if ($user->isCounselor() || $user->isAssistant()) {
            $statsQuery = Appointment::where('counselor_id', $user->id);
            $stats = [
                'total' => $statsQuery->count(),
                'pending' => $statsQuery->where('status', 'pending')->count(),
                'confirmed' => $statsQuery->where('status', 'confirmed')->count(),
                'completed' => $statsQuery->where('status', 'completed')->count(),
                'cancelled' => $statsQuery->where('status', 'cancelled')->count(),
                'no_show' => $statsQuery->where('status', 'no_show')->count(),
                'urgent' => $statsQuery->where('type', 'urgent')->count(),
                'today' => $statsQuery->where('appointment_date', now()->toDateString())
                                ->where('status', '!=', 'cancelled')
                                ->count(),
                'this_week' => $statsQuery->whereBetween('appointment_date', [
                                    now()->startOfWeek()->toDateString(),
                                    now()->endOfWeek()->toDateString()
                                ])
                                ->where('status', '!=', 'cancelled')
                                ->count(),
                'this_month' => $statsQuery->whereBetween('appointment_date', [
                                    now()->startOfMonth()->toDateString(),
                                    now()->endOfMonth()->toDateString()
                                ])
                                ->where('status', '!=', 'cancelled')
                                ->count(),
            ];
        }

        $counselors = User::where('role', 'counselor')->get();

        return view('appointments.manage', compact(
            'appointments',
            'activeTab',
            'stats',
            'counselors'
        ));
    }
