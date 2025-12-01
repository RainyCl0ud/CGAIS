<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4">
            <div class="w-full max-w-6xl mx-auto">
                <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                    <div class="mb-4 sm:mb-6">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900 mb-1">
                            Welcome, {{ Auth::user()->full_name }}!
                        </h1>
                        <div class="text-gray-600 text-xs sm:text-sm">{{ now()->format('l, F d, Y') }} | How can we help you today?</div>
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

                    <!-- Student Statistics Cards -->
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

                        <div class="bg-yellow-50 p-3 sm:p-6 rounded-lg border border-yellow-200">
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
                        </div>

                        <div class="bg-green-50 p-3 sm:p-6 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <div class="p-2 sm:p-3 bg-green-500 rounded-full">
                                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <p class="text-xs sm:text-sm font-medium text-gray-600">Completed</p>
                                    <p class="text-lg sm:text-2xl font-semibold text-gray-900">{{ $stats['completed_appointments'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PDS Completion Alert for Students -->
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

                    <!-- Pending Appointments -->
                    @if($pendingAppointments->count() > 0)
                        <div class="mb-6 sm:mb-8">
                            <h2 class="text-lg sm:text-2xl font-bold text-blue-900 mb-3 sm:mb-4">Pending Appointments</h2>
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
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($pendingAppointments->take(5) as $appointment)
                                                <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150" onclick="window.location.href='{{ route('student.appointments.show', $appointment) }}'">
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                        {{ $appointment->getFormattedDateTime() }}
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                        {{ $appointment->counselor->full_name }}
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getTypeBadgeClass() }}">
                                                            {{ $appointment->getTypeLabel() }}
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
                            @if($pendingAppointments->count() > 5)
                                <div class="mt-3 text-center">
                                    <a href="{{ route('student.appointments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                        View all {{ $pendingAppointments->count() }} pending appointments →
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Upcoming Approved Appointments -->
                    @if($upcomingApprovedAppointments->count() > 0)
                        <div class="mb-6 sm:mb-8">
                            <h2 class="text-lg sm:text-2xl font-bold text-blue-900 mb-3 sm:mb-4">Upcoming Approved Appointments</h2>
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
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($upcomingApprovedAppointments->take(5) as $appointment)
                                                <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150" onclick="window.location.href='{{ route('student.appointments.show', $appointment) }}'">
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                        {{ $appointment->getFormattedDateTime() }}
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                        {{ $appointment->counselor->full_name }}
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getTypeBadgeClass() }}">
                                                            {{ $appointment->getTypeLabel() }}
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
                            @if($upcomingApprovedAppointments->count() > 5)
                                <div class="mt-3 text-center">
                                    <a href="{{ route('student.appointments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                        View all {{ $upcomingApprovedAppointments->count() }} upcoming approved appointments →
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Student Quick Actions -->
                    <div class="mb-6 sm:mb-8">
                        <h2 class="text-lg sm:text-2xl font-bold text-blue-900 mb-3 sm:mb-4">Quick Actions</h2>
                        <div class="flex flex-wrap gap-2 sm:gap-4">
                            <a href="{{ route('student.appointments.create') }}" 
                               class="px-3 sm:px-6 py-2 sm:py-3 bg-blue-900 text-white font-semibold rounded-lg hover:bg-blue-800 transition-colors text-sm">
                                Book Appointment
                            </a>
                            <a href="{{ route('student.appointments.create') }}?type=urgent" 
                               class="px-3 sm:px-6 py-2 sm:py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors text-sm">
                                Request Urgent Appointment
                            </a>
                            <a href="{{ route('feedback.index') }}" 
                               class="px-3 sm:px-6 py-2 sm:py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors text-sm">
                                Submit Feedback
                            </a>
                        </div>
                    </div>

                    <!-- Student's Recent Appointments -->
                    @if($recentAppointments->count() > 0)
                        <div class="mb-6 sm:mb-8">
                            <h2 class="text-lg sm:text-2xl font-bold text-blue-900 mb-3 sm:mb-4">My Recent Appointments</h2>
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Counselor</th>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($recentAppointments as $appointment)
                                                <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150" onclick="window.location.href='{{ route('student.appointments.show', $appointment) }}'">
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                        {{ $appointment->getFormattedDateTime() }}
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                        {{ $appointment->counselor->full_name }}
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                                            {{ ucfirst($appointment->status) }}
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

                    <!-- Student's Upcoming Approved Appointments -->
                    @if($upcomingApprovedAppointments->count() > 0)
                        <div>
                            <h2 class="text-lg sm:text-2xl font-bold text-blue-900 mb-3 sm:mb-4">My Upcoming Approved Appointments</h2>
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Counselor</th>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($upcomingApprovedAppointments as $appointment)
                                                <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150" onclick="window.location.href='{{ route('student.appointments.show', $appointment) }}'">
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                        {{ $appointment->getFormattedDateTime() }}
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                        {{ $appointment->counselor->full_name }}
                                                    </td>
                                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getTypeBadgeClass() }}">
                                                            {{ ucfirst($appointment->type) }}
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
        </main>
    </div>
</x-app-layout> 