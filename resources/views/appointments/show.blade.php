<x-app-layout>
    <div class="flex flex-col items-center justify-start py-8 px-4 overflow-auto">
            <div class="w-full max-w-4xl mx-auto">
                <div class="bg-white/90 rounded-2xl shadow-2xl border border-blue-100 p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-blue-900">Appointment Details</h1>
                        @php
                            $backRoute = match(request()->get('back')) {
                                'pending' => route('pending.appointments'),
                                'today' => route('today.appointments'),
                                'notifications' => route('notifications.index'),
                                default => route('appointments.index'),
                            };
                        @endphp
                        <a href="{{ $backRoute }}"
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                             ← Back
                        </a>
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

                    <!-- Conflict Warning for Counselors -->
                    @if((auth()->user()->isCounselor() || auth()->user()->isAssistant()) && $appointment->isUrgent() && str_contains($appointment->counselor_notes ?? '', 'URGENT:'))
                        <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <strong>SCHEDULE CONFLICT DETECTED</strong>
                            </div>
                            <p class="mt-2 text-sm">This urgent appointment conflicts with an existing booking. Please review and decide whether to approve or reschedule.</p>
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
                                    <div>
                                        <span class="font-medium text-gray-700">Reason:</span>
                                        <p class="text-gray-900">{{ $appointment->reason }}</p>
                                    </div>
                                    @if($appointment->notes)
                                        <div>
                                            <span class="font-medium text-gray-700">Notes:</span>
                                            <p class="text-gray-900">{{ $appointment->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Client/Counselor Information -->
                            <div class="bg-green-50 p-6 rounded-lg">
                                <h2 class="text-xl font-semibold text-green-900 mb-4">
                                    @if(auth()->user()->isCounselor() || auth()->user()->isAssistant())
                                        Client Information
                                    @else
                                        Counselor Information
                                    @endif
                                </h2>
                                <div class="space-y-3">
                                    @if(auth()->user()->isCounselor() || auth()->user()->isAssistant())
                                        <div>
                                            <span class="font-medium text-gray-700">Name:</span>
                                            <p class="text-gray-900">{{ $appointment->user->full_name }}</p>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Email:</span>
                                            <p class="text-gray-900">{{ $appointment->user->email }}</p>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Role:</span>
                                            <p class="text-gray-900">{{ ucfirst($appointment->user->role) }}</p>
                                        </div>
                                        @if($appointment->user->student_id)
                                            <div>
                                                <span class="font-medium text-gray-700">Student ID:</span>
                                                <p class="text-gray-900">{{ $appointment->user->student_id }}</p>
                                            </div>
                                        @endif
                                        @if($appointment->user->faculty_id)
                                            <div>
                                                <span class="font-medium text-gray-700">Faculty ID:</span>
                                                <p class="text-gray-900">{{ $appointment->user->faculty_id }}</p>
                                            </div>
                                        @endif
                                    @else
                                        <div>
                                            <span class="font-medium text-gray-700">Name:</span>
                                            <p class="text-gray-900">{{ $appointment->counselor->full_name }}</p>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Email:</span>
                                            <p class="text-gray-900">{{ $appointment->counselor->email }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions and Updates -->
                        <div class="space-y-6">
                            @if(auth()->user()->isCounselor() || auth()->user()->isAssistant())
                                <!-- Counselor Actions -->
                                <div class="bg-yellow-50 p-6 rounded-lg">
                                    <h2 class="text-xl font-semibold text-yellow-900 mb-4">Update Appointment</h2>
                                    <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="space-y-4" id="counselorNotesForm">
                                        @csrf
                                        @method('PATCH')
                                        
                                        <div>
                                            <label for="counselor_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                                Counselor Notes
                                            </label>
                                            <textarea id="counselor_notes" name="counselor_notes" rows="4"
                                                      placeholder="Add notes about the session..."
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $appointment->counselor_notes }}</textarea>
                                        </div>

                                        <button type="submit" 
                                                class="w-full px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition-colors">
                                            Update Notes
                                        </button>
                                    </form>
                                </div>
                            @endif

                            <!-- Appointment Actions -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions</h2>
                                <div class="space-y-3">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
                                    @if(auth()->user()->isCounselor())
                                        <!-- Counselor Status Actions (Full Privileges) -->
                                        @if($appointment->status === 'pending')
                                            <button onclick="approveAppointment('{{ $appointment->id }}')"
                                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                                ✓ Approve Appointment
                                            </button>
                                            
                                            <button onclick="showRejectModal('{{ $appointment->id }}')" 
                                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                ✗ Reject Appointment
                                            </button>
                                            
                                            <button onclick="showRescheduleModal('{{ $appointment->id }}')" 
                                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                🔄 Reschedule Appointment
                                            </button>
                                            
                                        @elseif($appointment->status === 'confirmed')
                                            <button onclick="confirmStatusChange('{{ $appointment->id }}', 'cancelled', 'Confirmed', 'Cancelled')" 
                                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                ✗ Cancel Appointment
                                            </button>
                                            <button onclick="showRescheduleModal('{{ $appointment->id }}')" 
                                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                🔄 Reschedule Appointment
                                            </button>
                                            @if($appointment->getAppointmentDateTime()->isPast())
                                                <button onclick="confirmStatusChange('{{ $appointment->id }}', 'no_show', 'Confirmed', 'No Show')" 
                                                        class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                                                    ⚠ Mark as No Show
                                                </button>
                                            @endif
                                        @elseif($appointment->status === 'completed')
                                            <div class="p-3 bg-green-100 border border-green-400 text-green-700 rounded text-center">
                                                ✓ Appointment Completed
                                            </div>
                                        @elseif($appointment->status === 'cancelled')
                                            <div class="p-3 bg-red-100 border border-red-400 text-red-700 rounded text-center">
                                                ✗ Appointment Cancelled
                                            </div>
                                        @elseif($appointment->status === 'no_show')
                                            <div class="p-3 bg-orange-100 border border-orange-400 text-orange-700 rounded text-center">
                                                ⚠ No Show
                                            </div>
                                        @endif
                                        
                                    @elseif(auth()->user()->isAssistant())
                                        <!-- Assistant Status Actions (Limited Privileges) -->
                                        @if($appointment->status === 'pending')
                                            <div class="p-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded text-center">
                                                ⚠ Pending Counselor Approval
                                            </div>
                                        @elseif($appointment->status === 'confirmed')
                                            <button onclick="confirmStatusChange('{{ $appointment->id }}', 'cancelled', 'Confirmed', 'Cancelled')" 
                                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                ✗ Cancel Appointment
                                            </button>
                                            @if($appointment->getAppointmentDateTime()->isPast())
                                                <button onclick="confirmStatusChange('{{ $appointment->id }}', 'no_show', 'Confirmed', 'No Show')" 
                                                        class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                                                    ⚠ Mark as No Show
                                                </button>
                                            @endif
                                        @elseif($appointment->status === 'completed')
                                            <div class="p-3 bg-green-100 border border-green-400 text-green-700 rounded text-center">
                                                ✓ Appointment Completed
                                            </div>
                                        @elseif($appointment->status === 'cancelled')
                                            <div class="p-3 bg-red-100 border border-red-400 text-red-700 rounded text-center">
                                                ✗ Appointment Cancelled
                                            </div>
                                        @elseif($appointment->status === 'no_show')
                                            <div class="p-3 bg-orange-100 border border-orange-400 text-orange-700 rounded text-center">
                                                ⚠ No Show
                                            </div>
                                        @endif
                                        
                                    @else
                                        <!-- User Actions -->
                                        @if($appointment->isPending())
                                            <a href="{{ route('appointments.edit', $appointment) }}" 
                                               class="block w-full px-4 py-2 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition-colors">
                                                Edit Appointment
                                            </a>
                                            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" 
                                                  onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                    Cancel Appointment
                                                </button>
                                            </form>
                                        @elseif($appointment->isConfirmed())
                                            <div class="p-3 bg-blue-100 border border-blue-400 text-blue-700 rounded text-center">
                                                ✓ Appointment Confirmed
                                            </div>
                                        @elseif($appointment->isCompleted())
                                            <div class="p-3 bg-green-100 border border-green-400 text-green-700 rounded text-center">
                                                ✓ Appointment Completed
                                            </div>
                                        @elseif($appointment->isCancelled())
                                            <div class="p-3 bg-red-100 border border-red-400 text-red-700 rounded text-center">
                                                ✗ Appointment Cancelled
                                            </div>
                                        @elseif($appointment->isNoShow())
                                            <div class="p-3 bg-orange-100 border border-orange-400 text-orange-700 rounded text-center">
                                                ⚠ No Show
                                            </div>
                                        @endif
                                    @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Counselor Notes Display -->
                            @if($appointment->counselor_notes && (auth()->user()->isCounselor() || auth()->user()->isAssistant()))
                                <div class="bg-purple-50 p-6 rounded-lg">
                                    <h2 class="text-xl font-semibold text-purple-900 mb-4">Counselor Notes</h2>
                                    <p class="text-gray-900">{{ $appointment->counselor_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Status Change Confirmation Modal -->
    <div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Confirm Status Change</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to change the appointment status from 
                        <span id="currentStatus" class="font-semibold text-gray-700"></span> 
                        to 
                        <span id="newStatus" class="font-semibold text-gray-700"></span>?
                    </p>
                    <div class="mt-4 text-xs text-gray-600">
                        <p id="statusWarning" class="text-red-600 font-medium"></p>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmStatusChange" 
                            class="px-4 py-2 bg-blue-900 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-blue-800 transition-colors">
                        Confirm
                    </button>
                    <button id="cancelStatusChange" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-24 hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Appointment Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4 text-center">Reject Appointment</h3>
                <form id="rejectForm" method="POST" class="mt-4">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                                  placeholder="Please provide a reason for rejecting this appointment..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideRejectModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 transition-colors">
                            Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reschedule Appointment Modal -->
    <div id="rescheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4 text-center">Reschedule Appointment</h3>
                <form id="rescheduleForm" method="POST" class="mt-4">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="new_appointment_date" class="block text-sm font-medium text-gray-700 mb-2">New Date</label>
                        <input type="date" id="new_appointment_date" name="new_appointment_date" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="new_start_time" class="block text-sm font-medium text-gray-700 mb-2">New Start Time</label>
                        <input type="time" id="new_start_time" name="new_start_time" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="new_end_time" class="block text-sm font-medium text-gray-700 mb-2">New End Time</label>
                        <input type="time" id="new_end_time" name="new_end_time" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="reschedule_reason" class="block text-sm font-medium text-gray-700 mb-2">Reschedule Reason</label>
                        <textarea id="reschedule_reason" name="reschedule_reason" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Please provide a reason for rescheduling..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideRescheduleModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md hover:bg-blue-700 transition-colors">
                            Reschedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Status change confirmation function
        function confirmStatusChange(appointmentId, newStatus, currentStatusText, newStatusText) {
            const modal = document.getElementById('statusModal');
            const currentStatusSpan = document.getElementById('currentStatus');
            const newStatusSpan = document.getElementById('newStatus');
            const statusWarning = document.getElementById('statusWarning');

            // Update modal content
            currentStatusSpan.textContent = currentStatusText;
            newStatusSpan.textContent = newStatusText;

            // Show warnings for certain status changes
            let warning = '';
            if (newStatus === 'cancelled') {
                warning = 'This will cancel the appointment and notify the client.';
            } else if (newStatus === 'no_show') {
                warning = 'This will mark the client as a no-show.';
            } else if (newStatus === 'confirmed') {
                warning = 'This will confirm the appointment and notify the client.';
            }

            statusWarning.textContent = warning;

            // Show modal
            modal.classList.remove('hidden');

            // Handle confirm button
            const confirmButton = document.getElementById('confirmStatusChange');
            confirmButton.onclick = function() {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/appointments/${appointmentId}`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';

                const statusField = document.createElement('input');
                statusField.type = 'hidden';
                statusField.name = 'status';
                statusField.value = newStatus;

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                form.appendChild(statusField);

                document.body.appendChild(form);
                form.submit();
            };
        }

        // Approve appointment function
        function approveAppointment(appointmentId) {
            // Create and submit form to approve route
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/appointments/${appointmentId}/approve`;

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PATCH';

            form.appendChild(csrfToken);
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        }

        // Modal functions
        function showRejectModal(appointmentId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/appointments/${appointmentId}/reject`;
            modal.classList.remove('hidden');
        }

        function hideRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
        }

        function showRescheduleModal(appointmentId) {
            const modal = document.getElementById('rescheduleModal');
            const form = document.getElementById('rescheduleForm');
            form.action = `/appointments/${appointmentId}/reschedule`;
            modal.classList.remove('hidden');
        }

        function hideRescheduleModal() {
            const modal = document.getElementById('rescheduleModal');
            modal.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const statusModal = document.getElementById('statusModal');
            const rejectModal = document.getElementById('rejectModal');
            const rescheduleModal = document.getElementById('rescheduleModal');
            const cancelButton = document.getElementById('cancelStatusChange');

            // Handle cancel button
            cancelButton.addEventListener('click', function() {
                statusModal.classList.add('hidden');
            });

            // Close modals when clicking outside
            statusModal.addEventListener('click', function(e) {
                if (e.target === statusModal) {
                    statusModal.classList.add('hidden');
                }
            });

            rejectModal.addEventListener('click', function(e) {
                if (e.target === rejectModal) {
                    rejectModal.classList.add('hidden');
                }
            });

            rescheduleModal.addEventListener('click', function(e) {
                if (e.target === rescheduleModal) {
                    rescheduleModal.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout> 