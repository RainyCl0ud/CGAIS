<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4">
            <div class="w-full max-w-7xl mx-auto">
                <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                    <div class="mb-4 sm:mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900">Today's Appointments</h1>
                                <p class="text-gray-600 text-xs sm:text-sm mt-1">{{ Carbon\Carbon::today()->format('l, F j, Y') }}</p>
                            </div>
                            <a href="{{ route('dashboard') }}" 
                               class="px-3 sm:px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-400 transition-colors text-sm">
                                ← Back to Dashboard
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

                    @if($todayAppointments->count() > 0)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($todayAppointments as $appointment)
                                            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('appointments.show', $appointment) }}?back=today'">
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                    <div class="font-medium">{{ $appointment->start_time->format('g:i A') }} - {{ $appointment->end_time->format('g:i A') }}</div>
                                                    <div class="text-gray-500 text-xs">{{ $appointment->start_time->diffForHumans() }}</div>
                                                </td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                    <div class="font-medium">{{ $appointment->user->full_name }}</div>
                                                    <div class="text-gray-500 text-xs">{{ $appointment->user->email }}</div>
                                                </td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getTypeBadgeClass() }}">
                                                        {{ ucfirst($appointment->type) }}
                                                    </span>
                                                    @if($appointment->isUrgent())
                                                        <div class="text-xs text-red-600 font-medium mt-1">⚠ Urgent</div>
                                                    @endif
                                                </td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                    {{ $appointment->getCounselingCategoryLabel() }}
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

                        <div class="mt-4 text-center text-sm text-gray-600">
                            <p>Showing {{ $todayAppointments->count() }} appointment(s) for today</p>
                            <p class="mt-1 text-xs text-gray-500">Click on any row to view appointment details and perform actions</p>
                        </div>
                    @else
                        <div class="text-center py-8 sm:py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments today</h3>
                            <p class="mt-1 text-sm text-gray-500">You have no scheduled appointments for today.</p>
                            <div class="mt-6">
                                <a href="{{ route('appointments.index') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-900 hover:bg-blue-800">
                                    View All Appointments
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-app-layout> 