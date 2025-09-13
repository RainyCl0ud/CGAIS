<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Activity') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- User Activity Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Activities</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $totalActivities }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Today</p>
                                    <p class="text-2xl font-bold text-green-900">{{ $todayActivities }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600">This Week</p>
                                    <p class="text-2xl font-bold text-yellow-900">{{ $weekActivities }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Type Distribution -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Activity Distribution</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($activityDistribution as $action => $count)
                                <div class="bg-white p-3 rounded-lg border">
                                    <div class="text-sm font-medium text-gray-600">{{ ucwords(str_replace('_', ' ', $action)) }}</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $count }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter Activities</h3>
                        <form method="GET" action="{{ route('activity-logs.user-activity') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="action" class="block text-sm font-medium text-gray-700 mb-1">Action Type</label>
                                <select name="action" id="action" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Actions</option>
                                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                                    <option value="appointment_booked" {{ request('action') == 'appointment_booked' ? 'selected' : '' }}>Appointment Booked</option>
                                    <option value="appointment_cancelled" {{ request('action') == 'appointment_cancelled' ? 'selected' : '' }}>Appointment Cancelled</option>
                                    <option value="appointment_completed" {{ request('action') == 'appointment_completed' ? 'selected' : '' }}>Appointment Completed</option>
                                    <option value="schedule_created" {{ request('action') == 'schedule_created' ? 'selected' : '' }}>Schedule Created</option>
                                    <option value="schedule_updated" {{ request('action') == 'schedule_updated' ? 'selected' : '' }}>Schedule Updated</option>
                                    <option value="feedback_submitted" {{ request('action') == 'feedback_submitted' ? 'selected' : '' }}>Feedback Submitted</option>
                                    <option value="profile_updated" {{ request('action') == 'profile_updated' ? 'selected' : '' }}>Profile Updated</option>
                                    <option value="password_changed" {{ request('action') == 'password_changed' ? 'selected' : '' }}>Password Changed</option>
                                    <option value="pds_accessed" {{ request('action') == 'pds_accessed' ? 'selected' : '' }}>PDS Accessed</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                                <select name="date_range" id="date_range" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Time</option>
                                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                    <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                                    <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                                </select>
                            </div>
                            
                            <div class="flex items-end">
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Activity Timeline -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Activities</h3>
                        
                        @forelse($activities as $activity)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    {!! $activity->getActionIcon() !!}
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activity->getActionColor() }}">
                                                    {{ $activity->getActionLabel() }}
                                                </span>
                                                <span class="text-sm text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-gray-900 mt-1">{{ $activity->description }}</p>
                                            @if($activity->model_type && $activity->model_id)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Related: {{ $activity->getModelLabel() }} #{{ $activity->model_id }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="{{ route('activity-logs.show', $activity) }}" 
                                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No activities found</h3>
                                <p class="mt-1 text-sm text-gray-500">You haven't performed any activities yet.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($activities->hasPages())
                        <div class="mt-6">
                            {{ $activities->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
