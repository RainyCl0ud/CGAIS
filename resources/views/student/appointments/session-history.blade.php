<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4">
            <div class="w-full max-w-7xl mx-auto">
                <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
                        <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900">Past Counseling Sessions</h1>
                        <p class="text-gray-600 text-xs sm:text-sm mt-1">View your completed, cancelled, and no-show sessions</p>
                        </div>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mt-3 sm:mt-0">
                        <button onclick="history.back()" 
                                class="px-4 sm:px-6 py-2 sm:py-3 bg-blue-900 text-white font-semibold rounded-lg hover:bg-blue-800 transition-colors text-sm">
                            ← Back
                        </button>
                            <a href="{{ route('student.appointments.export-history') }}?{{ request()->getQueryString() }}" 
                           class="px-4 sm:px-6 py-2 sm:py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors text-sm">
                                📊 Export to CSV
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-3 sm:p-4 bg-green-100 border border-green-400 text-green-700 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
                        <div class="bg-blue-50 p-3 sm:p-4 rounded-lg border border-blue-200">
                            <div class="text-xs sm:text-sm font-medium text-gray-600">Total Sessions</div>
                            <div class="text-lg sm:text-xl font-semibold text-blue-900">{{ $stats['total_sessions'] }}</div>
                        </div>
                        <div class="bg-green-50 p-3 sm:p-4 rounded-lg border border-green-200">
                            <div class="text-xs sm:text-sm font-medium text-gray-600">Completed</div>
                            <div class="text-lg sm:text-xl font-semibold text-green-900">{{ $stats['completed_sessions'] }}</div>
                        </div>
                        <div class="bg-red-50 p-3 sm:p-4 rounded-lg border border-red-200">
                            <div class="text-xs sm:text-sm font-medium text-gray-600">Cancelled</div>
                            <div class="text-lg sm:text-xl font-semibold text-red-900">{{ $stats['cancelled_sessions'] }}</div>
                        </div>
                        <div class="bg-orange-50 p-3 sm:p-4 rounded-lg border border-orange-200">
                            <div class="text-xs sm:text-sm font-medium text-gray-600">No Show</div>
                            <div class="text-lg sm:text-xl font-semibold text-orange-900">{{ $stats['no_show_sessions'] }}</div>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <div class="bg-gray-50 p-4 sm:p-6 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters & Search</h3>
                        <form method="GET" action="{{ route('student.appointments.session-history') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   placeholder="Counselor name or notes..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Statuses</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="no_show" {{ request('status') === 'no_show' ? 'selected' : '' }}>No Show</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="rescheduled" {{ request('status') === 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                </select>
                            </div>

                            <!-- Type Filter -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>All Types</option>
                                    <option value="regular" {{ request('type') === 'regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="urgent" {{ request('type') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    <option value="follow_up" {{ request('type') === 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                                </select>
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select name="category" id="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="all" {{ request('category') === 'all' ? 'selected' : '' }}>All Categories</option>
                                    <option value="conduct_intake_interview" {{ request('category') === 'conduct_intake_interview' ? 'selected' : '' }}>Intake Interview</option>
                                    <option value="information_services" {{ request('category') === 'information_services' ? 'selected' : '' }}>Information Services</option>
                                    <option value="internal_referral_services" {{ request('category') === 'internal_referral_services' ? 'selected' : '' }}>Internal Referral</option>
                                    <option value="counseling_services" {{ request('category') === 'counseling_services' ? 'selected' : '' }}>Counseling Services</option>
                                    <option value="conduct_exit_interview" {{ request('category') === 'conduct_exit_interview' ? 'selected' : '' }}>Exit Interview</option>
                                </select>
                            </div>

                            <!-- Date Range -->
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Sort Options -->
                            <div>
                                <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                                <select name="sort_by" id="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="appointment_date" {{ request('sort_by') === 'appointment_date' ? 'selected' : '' }}>Date</option>
                                    <option value="start_time" {{ request('sort_by') === 'start_time' ? 'selected' : '' }}>Time</option>
                                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Created At</option>
                                    <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Status</option>
                                    <option value="type" {{ request('sort_by') === 'type' ? 'selected' : '' }}>Type</option>
                                </select>
                            </div>

                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                                <select name="sort_order" id="sort_order" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Newest First</option>
                                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Oldest First</option>
                                </select>
                            </div>

                            <!-- Filter Buttons -->
                            <div class="sm:col-span-2 lg:col-span-4 flex flex-col sm:flex-row gap-2">
                                <button type="submit" class="px-4 py-2 bg-blue-900 text-white font-semibold rounded-lg hover:bg-blue-800 transition-colors">
                                    🔍 Apply Filters
                                </button>
                                <a href="{{ route('student.appointments.session-history') }}" class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition-colors text-center">
                                    🗑️ Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>

                    @if($appointments->count() > 0)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Counselor</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session Notes</th>
    
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($appointments as $appointment)
                                            <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150" onclick="window.location.href='{{ route('student.appointments.show', $appointment) }}'">
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                    <div class="font-medium">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                                                    <div class="text-gray-500">{{ $appointment->start_time->format('g:i A') }} - {{ $appointment->end_time->format('g:i A') }}</div>
                                                </td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                <div class="font-medium">{{ $appointment->counselor->full_name }}</div>
                                                </td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getTypeBadgeClass() }}">
                                                        {{ ucfirst($appointment->type) }}
                                                    </span>
                                                </td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getCounselingCategoryBadgeClass() }}">
                                                        {{ $appointment->getCounselingCategoryLabel() }}
                                                    </span>
                                                </td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </td>
                                            <td class="px-3 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm text-gray-900">
                                                <div class="max-w-xs">
                                                    @if($appointment->type === 'urgent')
                                                        <div class="mb-1">
                                                            <span class="font-medium text-red-600">Reason for urgency:</span>
                                                            <p class="text-gray-700">{{ $appointment->reason ?: 'Not specified' }}</p>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <span class="font-medium text-gray-600">Purpose/Concern:</span>
                                                        <p class="text-gray-700">{{ $appointment->notes ?: 'Not specified' }}</p>
                                                    </div>
                                                    @if($appointment->counselor_notes)
                                                        <div class="mt-1">
                                                            <span class="font-medium text-blue-600">Counselor Notes:</span>
                                                            <p class="text-gray-700">{{ $appointment->counselor_notes }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4 sm:mt-6">
                            {{ $appointments->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 sm:py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No sessions found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or search criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
    </div>
</x-app-layout>
