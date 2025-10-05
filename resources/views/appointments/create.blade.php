    <x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4 overflow-auto">
            <div class="w-full max-w-none mx-auto">
                <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                    <div class="mb-4 sm:mb-6">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900">Book Appointment</h1>
                        <p class="text-gray-600 text-xs sm:text-sm mt-1">Schedule a counseling session with a counselor</p>
                    </div>

                    @if(session('error'))
                        <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('student.appointments.store') }}" class="space-y-4 sm:space-y-6">
                        @csrf

                        <!-- Counselor Selection -->
                        <div>
                            <label for="counselor_id" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Counselor</label>
                            <select id="counselor_id" name="counselor_id" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Select a counselor</option>
                                @foreach($counselors as $counselor)
                                    <option value="{{ $counselor->id }}" {{ old('counselor_id') == $counselor->id ? 'selected' : '' }}>
                                        {{ $counselor->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('counselor_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date Selection -->
                        <div>
                            <label for="appointment_date" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Date</label>
                            <input type="date" id="appointment_date" name="appointment_date" required 
                                   min="{{ date('Y-m-d') }}" value="{{ old('appointment_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <p class="text-xs text-gray-500 mt-1">* Appointments available on weekdays (Monday through Friday)</p>
                            @error('appointment_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time Selection -->
                        <div>
                            <label for="start_time" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Time</label>
                            <select id="start_time" name="start_time" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Select a time</option>
                                <!-- Time slots will be populated via JavaScript -->
                            </select>
                            <input type="hidden" id="end_time" name="end_time" value="">
                            @error('start_time')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @error('end_time')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Appointment Type -->
                        <div>
                            <label for="type" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Type</label>
                            <select id="type" name="type" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Select appointment type</option>
                                <option value="regular" {{ old('type') == 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="urgent" {{ old('type') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                <option value="follow_up" {{ old('type') == 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Counseling Category (Students only) -->
                        @if(auth()->user()->isStudent())
                        <div>
                            <label for="counseling_category" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Counseling Category</label>
                            <select id="counseling_category" name="counseling_category" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Select counseling category</option>
                                <option value="conduct_intake_interview" {{ old('counseling_category') == 'conduct_intake_interview' ? 'selected' : '' }}>Conduct Intake Interview</option>
                                <option value="information_services" {{ old('counseling_category') == 'information_services' ? 'selected' : '' }}>Information Services</option>
                                <option value="internal_referral_services" {{ old('counseling_category') == 'internal_referral_services' ? 'selected' : '' }}>Internal Referral Services</option>
                                <option value="counseling_services" {{ old('counseling_category') == 'counseling_services' ? 'selected' : '' }}>Counseling Services</option>
                                <option value="conduct_exit_interview" {{ old('counseling_category') == 'conduct_exit_interview' ? 'selected' : '' }}>Conduct Exit Interview</option>
                            </select>
                            @error('counseling_category')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        <!-- Reason -->
                        <!-- Reason for Urgency (for urgent appointments) -->
                        <div id="urgency_reason_div" class="hidden">
                            <label for="reason" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                <span class="text-red-600">*</span> Reason for Urgency
                            </label>
                            <textarea id="reason" name="reason" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                      placeholder="Please explain why this appointment is urgent">{{ old('reason') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">This field is required for urgent appointments.</p>
                            @error('reason')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purpose/Concern -->
                        <div>
                            <label for="notes" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Purpose/Concern</label>
                            <textarea id="notes" name="notes" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                      placeholder="Please describe the purpose of your visit or any concerns you'd like to discuss">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 pt-4">
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 sm:py-3 bg-blue-900 text-white font-semibold rounded-lg hover:bg-blue-800 transition-colors text-sm">
                                Book Appointment
                            </button>
                            <a href="{{ route('appointments.index') }}" 
                               class="flex-1 px-4 py-2 sm:py-3 bg-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-400 transition-colors text-center text-sm">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        // JavaScript for dynamic time slot loading
        document.getElementById('counselor_id').addEventListener('change', function() {
            const counselorId = this.value;
            const dateInput = document.getElementById('appointment_date');
            const timeSelect = document.getElementById('start_time');
            
            if (counselorId && dateInput.value) {
                loadAvailableTimeSlots(counselorId, dateInput.value);
            }
        });

        document.getElementById('appointment_date').addEventListener('change', function() {
            const counselorId = document.getElementById('counselor_id').value;
            const date = this.value;
            
            // Check if selected date is a weekday (Monday through Friday)
            const selectedDate = new Date(date);
            const dayOfWeek = selectedDate.getDay(); // 0 = Sunday, 1 = Monday, 5 = Friday

            if (dayOfWeek === 0 || dayOfWeek === 6) { // Sunday or Saturday
                const timeSelect = document.getElementById('start_time');
                timeSelect.innerHTML = '<option value="">Appointments are only available on weekdays (Monday through Friday).</option>';
                timeSelect.disabled = true;
                return;
            }
            
            // Re-enable time select if it was disabled
            const timeSelect = document.getElementById('start_time');
            timeSelect.disabled = false;
            
            if (counselorId && date) {
                loadAvailableTimeSlots(counselorId, date);
            }
        });

        // Add event listener for appointment type to reload time slots and handle urgent form
        document.getElementById('type').addEventListener('change', function() {
            const counselorId = document.getElementById('counselor_id').value;
            const date = document.getElementById('appointment_date').value;
            const urgencyDiv = document.getElementById('urgency_reason_div');
            const reasonField = document.getElementById('reason');
            
            // Show/hide urgency reason field based on type
            if (this.value === 'urgent') {
                urgencyDiv.classList.remove('hidden');
                reasonField.required = true;
            } else {
                urgencyDiv.classList.add('hidden');
                reasonField.required = false;
            }
            
            if (counselorId && date) {
                loadAvailableTimeSlots(counselorId, date);
            }
        });

        // Initialize form state based on URL parameters
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const typeParam = urlParams.get('type');
            
            if (typeParam === 'urgent') {
                const typeSelect = document.getElementById('type');
                typeSelect.value = 'urgent';
                typeSelect.dispatchEvent(new Event('change'));
            }
        });

        function loadAvailableTimeSlots(counselorId, date) {
            const timeSelect = document.getElementById('start_time');
            const appointmentType = document.getElementById('type').value;
            const isUrgent = appointmentType === 'urgent';
            
            timeSelect.innerHTML = '<option value="">Loading available times...</option>';
            
            fetch(`/api/student/counselors/${counselorId}/available-slots?date=${date}&urgent=${isUrgent}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    timeSelect.innerHTML = '<option value="">Select a time</option>';
                    
                    if (data.slots && data.slots.length > 0) {
                        data.slots.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot.time;
                            option.textContent = slot.formatted_time + slot.conflict_message;
                            option.setAttribute('data-end-time', slot.end_time);
                            
                            // Add visual indication for conflicts
                            if (slot.is_conflict) {
                                option.style.color = '#dc2626'; // Red color for conflicts
                                option.style.fontWeight = 'bold';
                            }
                            
                            timeSelect.appendChild(option);
                        });
                        
                        // Show warning for urgent appointments with conflicts
                        if (isUrgent && data.slots.some(slot => slot.is_conflict)) {
                            const warningDiv = document.createElement('div');
                            warningDiv.className = 'mt-2 p-2 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded text-xs';
                            warningDiv.innerHTML = '⚠️ <strong>Urgent Booking Notice:</strong> Some time slots conflict with existing appointments. Your urgent request will be reviewed by the counselor.';
                            
                            const timeDiv = timeSelect.parentElement;
                            // Remove existing warning if any
                            const existingWarning = timeDiv.querySelector('.bg-yellow-100');
                            if (existingWarning) {
                                existingWarning.remove();
                            }
                            timeDiv.appendChild(warningDiv);
                        }
                    } else {
                        timeSelect.innerHTML = '<option value="">No available times for this date</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading time slots:', error);
                    timeSelect.innerHTML = '<option value="">Error loading times</option>';
                });
        }

        // Add event listener for time selection to populate end_time
        document.getElementById('start_time').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const endTimeField = document.getElementById('end_time');
            
            if (selectedOption && selectedOption.getAttribute('data-end-time')) {
                endTimeField.value = selectedOption.getAttribute('data-end-time');
            } else {
                endTimeField.value = '';
            }
        });

        // Add form submission debugging
        document.querySelector('form').addEventListener('submit', function(e) {
            const formData = new FormData(this);
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Check if end_time is empty
            const endTime = document.getElementById('end_time').value;
            if (!endTime) {
                e.preventDefault();
                alert('Please select a time slot first.');
                return false;
            }
            
            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Booking Appointment...';
            
            console.log('Form submitted successfully');
        });
    </script>
</x-app-layout> 