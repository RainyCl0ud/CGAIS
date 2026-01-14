<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4 overflow-auto">
            <div class="w-full max-w-7xl mx-auto">
                <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-4 sm:mb-6">
                        <div class="mb-4 sm:mb-0">
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900">Appointments</h1>
                            <p class="text-gray-600 text-xs sm:text-sm mt-1">View and manage your counseling appointments</p>

                            @if(auth()->user()->isCounselor())
                                @php
                                    $status = auth()->user()->availability_status ?? 'AVAILABLE';

                                    $badgeClass = match($status) {
                                        'AVAILABLE' => 'bg-green-100 text-green-800 border-green-200',
                                        'ON_LEAVE' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'UNAVAILABLE' => 'bg-red-100 text-red-800 border-red-200',
                                        default => 'bg-gray-100 text-gray-800 border-gray-200',
                                    };

                                    $label = match($status) {
                                        'AVAILABLE' => 'You are currently AVAILABLE for bookings.',
                                        'ON_LEAVE' => 'You are marked as ON LEAVE. New bookings are limited based on your time range.',
                                        'UNAVAILABLE' => 'You are marked as UNAVAILABLE. New bookings are limited based on your time range.',
                                        default => 'Availability status not set.',
                                    };
                                @endphp
                                <div class="mt-2 inline-flex items-center px-3 py-1 border text-xs rounded-full {{ $badgeClass }}">
                                    <span class="font-semibold mr-1">Your Availability:</span>
                                    <span>{{ $label }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            @if(request('search'))
                                <a href="{{ url()->previous() }}"
                                   class="inline-flex items-center px-3 py-1 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Back to Student Directory
                                </a>
                            @endif
                            @if(!auth()->user()->isCounselor() && !auth()->user()->isAssistant())
                                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                                    <a href="{{ route('appointments.create') }}"
                                       class="px-4 sm:px-6 py-2 sm:py-3 bg-blue-900 text-white font-semibold rounded-lg hover:bg-blue-800 transition-colors text-sm">
                                        Book Appointment
                                    </a>
                                    <a href="{{ route('appointments.create') }}?type=urgent"
                                       class="px-4 sm:px-6 py-2 sm:py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors text-sm">
                                        Request Urgent
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-3 sm:p-4 bg-green-100 border-green-400 text-green-700 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                <!-- Filter Section for Students -->
                @if(!auth()->user()->isCounselor() && !auth()->user()->isAssistant())
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Filter Appointments</h3>
                        <form method="GET" action="{{ route('appointments.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Active Statuses</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="rescheduled" {{ request('status') === 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                </select>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>All Types</option>
                                    <option value="regular" {{ request('type') === 'regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="urgent" {{ request('type') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    <option value="follow_up" {{ request('type') === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                        </div>
                    @endif

                    @if($appointments->count() > 0)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                            @if(auth()->user()->isCounselor() || auth()->user()->isAssistant())
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                            @else
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Counselor</th>
                                            @endif
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        @if(!auth()->user()->isCounselor() && !auth()->user()->isAssistant())
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        @endif
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>

                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($appointments as $appointment)
                                            <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150" onclick="window.location.href='{{ route('appointments.show', $appointment) }}'">
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                    {{ $appointment->getFormattedDateTime() }}
                                                </td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                    @if(auth()->user()->isCounselor() || auth()->user()->isAssistant())
                                                        {{ $appointment->user->full_name }}
                                                    @else
                                                        {{ $appointment->counselor->full_name }}
                                                    @endif
                                                </td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getTypeBadgeClass() }}">
                                                        {{ ucfirst($appointment->type) }}
                                                    </span>
                                                </td>
                                            @if(!auth()->user()->isCounselor() && !auth()->user()->isAssistant())
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getCounselingCategoryBadgeClass() }}">
                                                        {{ $appointment->getCounselingCategoryLabel() }}
                                                    </span>
                                                </td>
                                            @endif
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium" onclick="event.stopPropagation()">
                                                    @if(!auth()->user()->isCounselor() && !auth()->user()->isAssistant())
                                                        @if($appointment->canBeRescheduled())
                                                            <a href="{{ route('appointments.edit', $appointment) }}"
                                                               class="text-green-600 hover:text-green-900">Reschedule</a>
                                                        @endif
                                                        @if($appointment->status === 'pending' || $appointment->status === 'confirmed')
                                                            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="inline"
                                                                  onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                                            </form>
                                                        @endif
                                                    @endif
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by booking your first appointment.</p>
                            @if(!auth()->user()->isCounselor() && !auth()->user()->isAssistant())
                                <div class="mt-6">
                                    <a href="{{ route('appointments.create') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-900 hover:bg-blue-800">
                                        Book Appointment
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
    </div>
</x-app-layout> 