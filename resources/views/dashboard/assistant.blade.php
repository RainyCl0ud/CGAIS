<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4">
            <div class="w-full max-w-6xl mx-auto">
                <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                    <div class="mb-4 sm:mb-6">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900 mb-1">
                            Welcome, {{ Auth::user()->full_name }}!
                        </h1>
                        <div class="text-gray-600 text-xs sm:text-sm">{{ now()->format('l, F d, Y') }} | Assist with counseling sessions</div>
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

                    <!-- Assistant Statistics Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
                        <div class="bg-blue-50 p-3 sm:p-6 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="p-2 sm:p-3 bg-blue-500 rounded-full">
                                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <p class="text-xs sm:text-sm font-medium text-gray-600">Total Appointments</p>
                                    <p class="text-lg sm:text-2xl font-semibold text-gray-900">{{ $stats['total_appointments'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('pending.appointments') }}" class="block bg-yellow-50 p-3 sm:p-6 rounded-lg border border-yellow-200 hover:bg-yellow-100 transition-colors cursor-pointer">
                            <div class="flex items-center">
                                <div class="p-2 sm:p-3 bg-yellow-500 rounded-full">
                                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <p class="text-xs sm:text-sm font-medium text-gray-600">Pending</p>
                                    <p class="text-lg sm:text-2xl font-semibold text-gray-900">{{ $stats['pending_appointments'] ?? 0 }}</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('today.appointments') }}" class="block bg-green-50 p-3 sm:p-6 rounded-lg border border-green-200 hover:bg-green-100 transition-colors cursor-pointer">
                            <div class="flex items-center">
                                <div class="p-2 sm:p-3 bg-green-500 rounded-full">
                                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <p class="text-xs sm:text-sm font-medium text-gray-600">Today's Appointments</p>
                                    <p class="text-lg sm:text-2xl font-semibold text-gray-900">{{ $stats['today_appointments'] ?? 0 }}</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Assistant Quick Actions -->
                    <div class="mb-6 sm:mb-8">
                        <h2 class="text-lg sm:text-2xl font-bold text-blue-900 mb-3 sm:mb-4">Quick Actions</h2>
                        <div class="flex flex-wrap gap-2 sm:gap-4">
                            <a href="{{ route('appointments.index') }}" 
                               class="px-3 sm:px-6 py-2 sm:py-3 bg-[#FFD700] text-[#1E3A8A] font-semibold rounded-lg hover:bg-[#FFE44D] transition-colors text-sm">
                                View Appointments
                            </a>
                        </div>
                    </div>

                    <!-- Assistant's Recent Appointments -->
                    @if($recentAppointments->count() > 0)
                        <div class="mb-6 sm:mb-8">
                            <h2 class="text-lg sm:text-2xl font-bold text-blue-900 mb-3 sm:mb-4">Recent Appointments</h2>
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($recentAppointments as $appointment)
                                                <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150" onclick="window.location.href='{{ route('appointments.show', $appointment) }}'">
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                        {{ $appointment->getFormattedDateTime() }}
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                        {{ $appointment->user->full_name }}
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                                            {{ ucfirst($appointment->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium" onclick="event.stopPropagation()">
                                                        @if($appointment->status === 'pending')
                                                            <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="confirmed">
                                                                <button type="submit" class="text-green-600 hover:text-green-900">Confirm</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Assistant's Upcoming Appointments -->
                    @if($upcomingAppointments->count() > 0)
                        <div>
                            <h2 class="text-lg sm:text-2xl font-bold text-blue-900 mb-3 sm:mb-4">Upcoming Appointments</h2>
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($upcomingAppointments as $appointment)
                                                <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150" onclick="window.location.href='{{ route('appointments.show', $appointment) }}'">
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                        {{ $appointment->getFormattedDateTime() }}
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                        {{ $appointment->user->full_name }}
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getTypeBadgeClass() }}">
                                                            {{ ucfirst($appointment->type) }}
                                                        </span>
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-app-layout> 