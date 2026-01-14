<div>
    <!-- Header with Stats (for counselors and assistants) -->
    @if(Auth::user()->isCounselor() || Auth::user()->isAssistant())
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2 md:mb-0">Appointment Statistics</h3>

                @if(Auth::user()->isCounselor())
                    @php
                        $availabilityStatus = Auth::user()->availability_status ?? 'AVAILABLE';
                        $availabilityLabel = match($availabilityStatus) {
                            'AVAILABLE' => 'Available for bookings',
                            'ON_LEAVE' => 'On leave (limited availability)',
                            'UNAVAILABLE' => 'Unavailable for bookings (see time range)',
                            default => 'Availability status not set',
                        };
                        $availabilityBadgeClass = match($availabilityStatus) {
                            'AVAILABLE' => 'bg-green-100 text-green-800 border-green-200',
                            'ON_LEAVE' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'UNAVAILABLE' => 'bg-red-100 text-red-800 border-red-200',
                            default => 'bg-gray-100 text-gray-800 border-gray-200',
                        };
                    @endphp
                    <div class="inline-flex items-center px-3 py-1 border text-xs rounded-full {{ $availabilityBadgeClass }}">
                        <span class="font-semibold mr-1">Your Availability:</span>
                        <span>{{ $availabilityLabel }}</span>
                    </div>
                @endif
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="text-sm font-medium text-gray-600">Total</div>
                    <div class="text-2xl font-bold text-blue-900">{{ $stats['total'] ?? 0 }}</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="text-sm font-medium text-gray-600">Pending</div>
                    <div class="text-2xl font-bold text-yellow-900">{{ $stats['pending'] ?? 0 }}</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="text-sm font-medium text-gray-600">Confirmed</div>
                    <div class="text-2xl font-bold text-green-900">{{ $stats['confirmed'] ?? 0 }}</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <div class="text-sm font-medium text-gray-600">Completed</div>
                    <div class="text-2xl font-bold text-purple-900">{{ $stats['completed'] ?? 0 }}</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="text-sm font-medium text-gray-600">Cancelled</div>
                    <div class="text-2xl font-bold text-red-900">{{ $stats['cancelled'] ?? 0 }}</div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-medium text-gray-600">No Show</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['no_show'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab Navigation -->
    <div class="mb-6">
        <nav class="flex space-x-8">
            <button wire:click="switchTab('appointments')" 
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'appointments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Manage Appointments
                @if(Auth::user()->isCounselor() || Auth::user()->isAssistant())
                    <span class="ml-2 bg-blue-100 text-blue-800 py-0.5 px-2 rounded-full text-xs">{{ count($appointments) }}</span>
                @endif
            </button>
            <button wire:click="switchTab('history')" 
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'history' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Session History
                <span class="ml-2 bg-purple-100 text-purple-800 py-0.5 px-2 rounded-full text-xs">{{ count($sessionHistory) }}</span>
            </button>
        </nav>
    </div>

    @if($activeTab === 'appointments')
        <!-- Appointments Management Tab -->
        <div>
            <!-- Filters and Search -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" wire:model.live="search" id="search" 
                               placeholder="Search appointments..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="status_filter" id="status_filter" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="no_show">No Show</option>
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label for="type_filter" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select wire:model.live="type_filter" id="type_filter" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all">All Types</option>
                            <option value="regular">Regular</option>
                            <option value="urgent">Urgent</option>
                            <option value="follow_up">Follow Up</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" wire:model.live="date_from" id="date_from" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <!-- Date To -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" wire:model.live="date_to" id="date_to" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                        <select wire:model.live="sort_by" id="sort_by" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="appointment_date">Date</option>
                            <option value="start_time">Time</option>
                            <option value="created_at">Created</option>
                            <option value="status">Status</option>
                            <option value="type">Type</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                        <select wire:model.live="sort_order" id="sort_order" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="desc">Descending</option>
                            <option value="asc">Ascending</option>
                        </select>
                    </div>
                </div>

                <!-- Clear Filters -->
                <div class="mt-4">
                    <button wire:click="clearFilters" 
                            class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition-colors">
                        Clear All Filters
                    </button>
                </div>
            </div>

            <!-- Appointments Table -->
            @if(count($appointments) > 0)
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        @foreach($appointments as $appointment)
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium text-blue-600 truncate">
                                                    {{ $appointment->user->full_name }}
                                                </p>
                                                <p class="ml-2 flex-shrink-0 flex">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $appointment->getTypeBadgeClass() }}">
                                                        {{ ucfirst($appointment->type) }}
                                                    </span>
                                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $appointment->appointment_date->format('M d, Y') }} at 
                                                {{ $appointment->start_time->format('g:i A') }}
                                            </div>
                                        </div>
                                        <div class="mt-2 sm:flex sm:justify-between">
                                            <div class="sm:flex">
                                                <p class="flex items-center text-sm text-gray-500">
                                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    @if(Auth::user()->isCounselor() || Auth::user()->isAssistant())
                                                        Student: {{ $appointment->user->full_name }}
                                                    @else
                                                        Counselor: {{ $appointment->counselor->full_name }}
                                                    @endif
                                                </p>
                                                @if($appointment->reason)
                                                    <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                        </svg>
                                                        {{ Str::limit($appointment->reason, 100) }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Special note for completed appointments -->
                                        @if($appointment->status === 'completed')
                                            <div class="mt-2 p-3 bg-purple-50 border border-purple-200 rounded-md">
                                                <div class="flex">
                                                    <svg class="h-5 w-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div class="ml-3">
                                                        <p class="text-sm text-purple-800">
                                                            <strong>Completed Session:</strong> This appointment has been completed. 
                                                            <a href="{{ route('appointments.session-history') }}" class="font-medium underline hover:text-purple-600">
                                                                View in Session History
                                                            </a> for detailed session notes and reports.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="ml-5 flex-shrink-0 flex space-x-2">
                                        @if($appointment->status === 'pending' && (Auth::user()->isCounselor() || Auth::user()->isAssistant()))
                                            <button wire:click="openApproveModal({{ $appointment->id }})" 
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                                Approve
                                            </button>
                                            <button wire:click="openRejectModal({{ $appointment->id }})" 
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                                Reject
                                            </button>
                                        @endif
                                        
                                        @if($appointment->canBeRescheduled() && (Auth::user()->isCounselor() || Auth::user()->isAssistant()))
                                            <button wire:click="openRescheduleModal({{ $appointment->id }})"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                                Reschedule
                                            </button>
                                        @endif
                                        
                                        @if($appointment->status === 'confirmed' && (Auth::user()->isCounselor() || Auth::user()->isAssistant()))
                                            <button wire:click="markAsCompleted({{ $appointment->id }})" 
                                                    class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm">
                                                Mark Completed
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('appointments.show', $appointment) }}" 
                                           class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm">
                                            View
                                        </a>
                                        
                                        @if(in_array($appointment->status, ['pending', 'confirmed']) && (Auth::id() === $appointment->user_id || Auth::user()->isCounselor() || Auth::user()->isAssistant()))
                                            <button wire:click="cancelAppointment({{ $appointment->id }})" 
                                                    onclick="return confirm('Are you sure you want to cancel this appointment?')"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                                Cancel
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10h8V11h-2m-6 0H6a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2v-9a2 2 0 00-2-2h-2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria or filters.</p>
                </div>
            @endif
        </div>
    @elseif($activeTab === 'history')
        <!-- Session History Tab -->
        <div>
            <div class="mb-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                <div class="flex">
                    <svg class="h-5 w-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-purple-800">Session History</h3>
                        <div class="mt-2 text-sm text-purple-700">
                            <p>This section displays all completed counseling sessions. These records are maintained for documentation and future reference.</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($sessionHistory) > 0)
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        @foreach($sessionHistory as $appointment)
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium text-blue-600 truncate">
                                                    {{ $appointment->user->full_name }}
                                                </p>
                                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    Completed
                                                </span>
                                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $appointment->getTypeBadgeClass() }}">
                                                    {{ ucfirst($appointment->type) }}
                                                </span>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $appointment->appointment_date->format('M d, Y') }} at 
                                                {{ $appointment->start_time->format('g:i A') }}
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            @if($appointment->counselor_notes)
                                                <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                                    <h4 class="text-sm font-medium text-blue-900 mb-1">Session Notes:</h4>
                                                    <p class="text-sm text-blue-800">{{ $appointment->counselor_notes }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-5">
                                        <a href="{{ route('appointments.show', $appointment) }}" 
                                           class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No completed sessions found</h3>
                    <p class="mt-1 text-sm text-gray-500">Completed counseling sessions will appear here.</p>
                </div>
            @endif
        </div>
    @endif
</div>