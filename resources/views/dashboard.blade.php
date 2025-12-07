@php
if (!isset($upcomingAppointments)) {
    $upcomingAppointments = collect();
}
@endphp

<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4">
            <div class="w-full max-w-6xl mx-auto">
                <div class="bg-red-100 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                    <div class="mb-6">
                        <h1 class="text-xl lg:text-2xl xl:text-3xl font-bold text-gray-900 mb-2 flex items-center gap-4">
                            <span>
                                Welcome, {{ auth()->user()->isCounselor() ? 'Dr. ' : '' }}{{ auth()->user()->isAssistant() ? 'Assistant ' : '' }}{{ Auth::user()->full_name }}!
                            </span>
                            @if(isset($nextAppointment) && $nextAppointment)
                                <span class="text-sm font-medium text-gray-700 bg-yellow-100 border border-yellow-300 rounded-md px-3 py-1">
                                    Next Appointment: {{ $nextAppointment->appointment_date->format('M d, Y') }} at {{ \Carbon\Carbon::parse($nextAppointment->start_time)->format('h:i A') }}
                                </span>
                            @endif
                        </h1>
                        <div class="text-gray-600 text-sm">{{ now()->format('l, F d, Y') }} | Manage your counseling sessions</div>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6 mb-8">
                        @if(auth()->user()->isCounselor())
                            <!-- Counselor Statistics (Full Privileges) -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 lg:p-6 rounded-xl border border-blue-200">
                                <div class="flex items-center">
                                    <div class="p-2 lg:p-3 bg-blue-500 rounded-lg">
                                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 lg:ml-4">
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">Total Appointments</p>
                                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total_appointments'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 lg:p-6 rounded-xl border border-yellow-200">
                                <div class="flex items-center">
                                    <div class="p-2 lg:p-3 bg-yellow-500 rounded-lg">
                                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 lg:ml-4">
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">Pending Approval</p>
                                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['pending_appointments'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 lg:p-6 rounded-xl border border-green-200">
                                <div class="flex items-center">
                                    <div class="p-2 lg:p-3 bg-green-500 rounded-lg">
                                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 lg:ml-4">
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">Today's Appointments</p>
                                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['today_appointments'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 lg:p-6 rounded-xl border border-purple-200">
                                <div class="flex items-center">
                                    <div class="p-2 lg:p-3 bg-purple-500 rounded-lg">
                                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 lg:ml-4">
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">Total Users</p>
                                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                        @endif
                        @if(auth()->user()->isAssistant())
                            <!-- Assistant Statistics (Same privileges as Counselor) -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 lg:p-6 rounded-xl border border-blue-200">
                                <div class="flex items-center">
                                    <div class="p-2 lg:p-3 bg-blue-500 rounded-lg">
                                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 lg:ml-4">
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">View Appointments</p>
                                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total_appointments'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 lg:p-6 rounded-xl border border-yellow-200">
                                <div class="flex items-center">
                                    <div class="p-2 lg:p-3 bg-yellow-500 rounded-lg">
                                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 lg:ml-4">
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">Pending Review</p>
                                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['pending_appointments'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 lg:p-6 rounded-xl border border-green-200">
                                <div class="flex items-center">
                                    <div class="p-2 lg:p-3 bg-green-500 rounded-lg">
                                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 lg:ml-4">
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">Today's Appointments</p>
                                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['today_appointments'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-4 lg:p-6 rounded-xl border border-gray-200">
                                <div class="flex items-center">
                                    <div class="p-2 lg:p-3 bg-gray-500 rounded-lg">
                                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2zM4 15h6v-2H4v2zM4 11h6V9H4v2zM4 7h6V5H4v2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 lg:ml-4">
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">Notifications</p>
                                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['unread_notifications'] ?? 0 }}</p>
                                    </div>
                                </div>

                        @else
                            <!-- Student/Faculty Statistics -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 lg:p-6 rounded-xl border border-blue-200">
                                <div class="flex items-center">
                                    <div class="p-2 lg:p-3 bg-blue-500 rounded-lg">
                                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 lg:ml-4">
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">My Appointments</p>
                                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total_appointments'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 lg:p-6 rounded-xl border border-yellow-200">
                                <div class="flex items-center">
                                    <div class="p-2 lg:p-3 bg-yellow-500 rounded-lg">
                                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 lg:ml-4">
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">Pending</p>
                                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['pending_appointments'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 lg:p-6 rounded-xl border border-purple-200">
                                <div class="flex items-center">
                                    <div class="p-2 lg:p-3 bg-purple-500 rounded-lg">
                                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 lg:ml-4">
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">Upcoming</p>
                                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['upcoming_appointments'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- PDS Completion Alert for Students -->
                    @if(auth()->user()->isStudent())
                        @php
                            $pds = auth()->user()->personalDataSheet;
                            $completionPercentage = $pds ? $pds->getCompletionPercentage() : 0;
                        @endphp
                        @if($completionPercentage < 100)
                            <div class="mb-6 sm:mb-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">
                                            Complete Your Personal Data Sheet
                                        </h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>Your PDS is {{ $completionPercentage }}% complete. Please complete it to access all features.</p>
                                            <div class="mt-2">
                                                <div class="w-full bg-yellow-200 rounded-full h-2">
                                                    <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $completionPercentage }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <a href="{{ route('pds.edit') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-800 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                                Complete PDS
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Quick Actions -->
                    <div class="mb-6 sm:mb-8">
                        <h2 class="text-lg sm:text-2xl font-bold text-blue-900 mb-3 sm:mb-4">Quick Actions</h2>
                        <div class="flex flex-wrap gap-2 sm:gap-4">
                            @if(auth()->user()->isStudent())
                                <!-- Student Quick Actions -->
                                <a href="{{ route('appointments.create') }}" 
                                   class="px-3 sm:px-6 py-2 sm:py-3 bg-blue-900 text-white font-semibold rounded-lg hover:bg-blue-800 transition-colors text-sm">
                                    Book Appointment
                                </a>
                                <a href="{{ route('appointments.index') }}" 
                                   class="px-3 sm:px-6 py-2 sm:py-3 bg-[#FFD700] text-[#1E3A8A] font-semibold rounded-lg hover:bg-[#FFE44D] transition-colors text-sm">
                                    My Appointments
                                </a>
                                <a href="{{ route('pds.show') }}" 
                                   class="px-3 sm:px-6 py-2 sm:py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                    Personal Data Sheet
                                </a>
                                <a href="{{ route('feedback.index') }}" 
                                   class="px-3 sm:px-6 py-2 sm:py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors text-sm">
                                    Submit Feedback
                                </a>
                            @elseif(auth()->user()->isCounselor() || auth()->user()->isAssistant())
                                <!-- Counselor/Assistant Quick Actions -->
                                <a href="{{ route('appointments.index') }}" 
                                   class="px-3 sm:px-6 py-2 sm:py-3 bg-[#FFD700] text-[#1E3A8A] font-semibold rounded-lg hover:bg-[#FFE44D] transition-colors text-sm">
                                    View Appointments
                                </a>
                                <a href="{{ route('students.index') }}" 
                                   class="px-3 sm:px-6 py-2 sm:py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                    Student Directory
                                </a>
                                @if(auth()->user()->isCounselor())
                                    <a href="{{ route('schedules.index') }}" 
                                       class="px-3 sm:px-6 py-2 sm:py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors text-sm">
                                        Manage Schedule
                                    </a>
                                @endif
                            @else
                                <!-- Faculty Quick Actions -->
                                <a href="{{ route('appointments.create') }}" 
                                   class="px-3 sm:px-6 py-2 sm:py-3 bg-blue-900 text-white font-semibold rounded-lg hover:bg-blue-800 transition-colors text-sm">
                                    Book Appointment
                                </a>
                                <a href="{{ route('appointments.index') }}" 
                                   class="px-3 sm:px-6 py-2 sm:py-3 bg-[#FFD700] text-[#1E3A8A] font-semibold rounded-lg hover:bg-[#FFE44D] transition-colors text-sm">
                                    My Appointments
                                </a>
                            @endif
                            <a href="{{ route('notifications.index') }}" 
                               class="px-3 sm:px-6 py-2 sm:py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors text-sm">
                                Notifications
                            </a>
                        </div>
                    </div>

                    <!-- Recent Appointments -->
                    @if($recentAppointments->count() > 0)
                        <div class="mb-6 sm:mb-8">
                            <h2 class="text-lg sm:text-2xl font-bold text-blue-900 mb-3 sm:mb-4">Recent Appointments</h2>
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
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($recentAppointments as $appointment)
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
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                                            {{ ucfirst($appointment->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Upcoming Appointments -->
                    @if($upcomingAppointments->count() > 0)
                        <div>
                            <h2 class="text-lg sm:text-2xl font-bold text-blue-900 mb-3 sm:mb-4">Upcoming Appointments</h2>
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
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($upcomingAppointments as $appointment)
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
                                                            {{ $appointment->getTypeLabel() }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
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
    </div>
</x-app-layout>
