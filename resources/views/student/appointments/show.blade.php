<x-app-layout>
    <div class="flex flex-col items-center justify-start py-8 px-4 overflow-auto">
            <div class="w-full max-w-4xl mx-auto">
                <div class="bg-white/90 rounded-2xl shadow-2xl border border-blue-100 p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-blue-900">Appointment Details</h1>
                         <span class="px-5 py-1 text-xl font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                        <button onclick="history.back()" 
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            ← Back
                        </button>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Urgent Appointment Warning -->
                    @if($appointment->isUrgent())
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <strong>URGENT APPOINTMENT</strong>
                            </div>
                            <p class="mt-2 text-sm">This is an urgent appointment that may require immediate attention. Please review the details carefully.</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Appointment Information -->
                        <div class="space-y-6">
                            <div class="bg-blue-50 p-6 rounded-lg">
                                <h2 class="text-xl font-semibold text-blue-900 mb-4">Appointment Information</h2>
                                <div class="space-y-3">
                                    <div>
                                        <span class="font-medium text-gray-700">Date & Time:</span>
                                        <p class="text-gray-900">{{ $appointment->getFormattedDateTime() }}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Type:</span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getTypeBadgeClass() }}">
                                            {{ ucfirst($appointment->type) }}
                                        </span>
                                    </div>
                                    @if($appointment->counseling_category)
                                    <div>
                                        <span class="font-medium text-gray-700">Counseling Category:</span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getCounselingCategoryBadgeClass() }}">
                                            {{ $appointment->getCounselingCategoryLabel() }}
                                        </span>
                                    </div>
                                    @endif
                                    <div>
                                        <span class="font-medium text-gray-700">Status:</span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>
                                    @if($appointment->reason)
                                        <div>
                                            <span class="font-medium text-gray-700">Reason for Urgency:</span>
                                            <p class="text-gray-900">{{ $appointment->reason }}</p>
                                        </div>
                                    @endif
                                    @if($appointment->notes)
                                        <div>
                                            <span class="font-medium text-gray-700">Purpose/Concern:</span>
                                            <p class="text-gray-900">{{ $appointment->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Counselor Information -->
                            <div class="bg-green-50 p-6 rounded-lg">
                                <h2 class="text-xl font-semibold text-green-900 mb-4">Counselor Information</h2>
                                <div class="space-y-3">
                                    <div>
                                        <span class="font-medium text-gray-700">Name:</span>
                                        <p class="text-gray-900">{{ $appointment->counselor->full_name }}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Email:</span>
                                        <p class="text-gray-900">{{ $appointment->counselor->email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions and Updates -->
                        <div class="space-y-6">
                            <!-- Student Actions -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions</h2>
                                <div class="space-y-3">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @if($appointment->status === 'pending')
                                        {{-- || $appointment->status === 'confirmed' --}}
                                            <a href="{{ route('student.appointments.edit', $appointment) }}" 
                                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center">
                                                🔄 Reschedule
                                            </a>
                                            
                                            <form method="POST" action="{{ route('student.appointments.cancel', $appointment) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        onclick="return confirm('Are you sure you want to cancel this appointment?')"
                                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                    ✗ Cancel
                                                </button>
                                            </form>
                                            @else()
                                                <p>No actions available</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Counselor Notes (Read-only for students) -->
                            @if($appointment->counselor_notes)
                                <div class="bg-yellow-50 p-6 rounded-lg">
                                    <h2 class="text-xl font-semibold text-yellow-900 mb-4">Counselor Notes</h2>
                                    <div class="bg-white p-4 rounded border">
                                        <p class="text-gray-900 whitespace-pre-wrap">{{ $appointment->counselor_notes }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Status Information -->
                            <div class="bg-blue-50 p-6 rounded-lg">
                                <h2 class="text-xl font-semibold text-blue-900 mb-4">Status Information</h2>
                                <div class="space-y-3">
                                    <div>
                                        <span class="font-medium text-gray-700">Current Status:</span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Created:</span>
                                        <p class="text-gray-900">{{ $appointment->created_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                    @if($appointment->updated_at != $appointment->created_at)
                                        <div>
                                            <span class="font-medium text-gray-700">Last Updated:</span>
                                            <p class="text-gray-900">{{ $appointment->updated_at->format('M d, Y \a\t g:i A') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</x-app-layout> 