<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Activity Logs') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('activity-logs.export') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Logs</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $totalLogs }}</p>
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
                                    <p class="text-2xl font-bold text-green-900">{{ $todayLogs }}</p>
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
                                    <p class="text-2xl font-bold text-yellow-900">{{ $weekLogs }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-600">Active Users</p>
                                    <p class="text-2xl font-bold text-purple-900">{{ $activeUsers }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Advanced Filters</h3>
                        <form method="GET" action="{{ route('activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                       placeholder="Search by user, action, or description..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="action" class="block text-sm font-medium text-gray-700 mb-1">Action</label>
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
                                <label for="model_type" class="block text-sm font-medium text-gray-700 mb-1">Model Type</label>
                                <select name="model_type" id="model_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Types</option>
                                    <option value="App\Models\User" {{ request('model_type') == 'App\Models\User' ? 'selected' : '' }}>User</option>
                                    <option value="App\Models\Appointment" {{ request('model_type') == 'App\Models\Appointment' ? 'selected' : '' }}>Appointment</option>
                                    <option value="App\Models\Schedule" {{ request('model_type') == 'App\Models\Schedule' ? 'selected' : '' }}>Schedule</option>
                                    <option value="App\Models\FeedbackForm" {{ request('model_type') == 'App\Models\FeedbackForm' ? 'selected' : '' }}>Feedback</option>
                                    <option value="App\Models\PersonalDataSheet" {{ request('model_type') == 'App\Models\PersonalDataSheet' ? 'selected' : '' }}>Personal Data Sheet</option>
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
                                    <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                </select>
                            </div>
                            
                            <div class="md:col-span-2 lg:col-span-4 flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Apply Filters
                                </button>
                                <a href="{{ route('activity-logs.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Activity Logs Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>

                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($activities as $log)
                                    <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150" onclick="window.location.href='{{ route('activity-logs.show', $log) }}'">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ strtoupper(substr($log->user->name ?? 'Unknown', 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $log->user->name ?? 'Unknown User' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $log->user->email ?? 'No email' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $log->getActionColor() }}">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    {!! $log->getActionIcon() !!}
                                                </svg>
                                                {{ $log->getActionLabel() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ Str::limit($log->description, 50) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($log->model_type && $log->model_id)
                                                <span class="text-sm text-gray-900">{{ $log->getModelLabel() }}</span>
                                                <div class="text-sm text-gray-500">ID: {{ $log->model_id }}</div>
                                            @else
                                                <span class="text-sm text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->ip_address ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->formatted_time }}
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            No activity logs found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
