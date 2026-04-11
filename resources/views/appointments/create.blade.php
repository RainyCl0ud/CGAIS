    <x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4 overflow-auto">
            <div class="w-full max-w-none mx-auto">
                <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                    <div class="mb-4 sm:mb-6">
                        <h1 id="appointment-title" class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900">Book Appointment</h1>
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
                                    @php
                                        $status = $counselor->availability_status ?? 'AVAILABLE';
                                        $statusLabel = match($status) {
                                            'AVAILABLE' => 'Available',
                                            'ON_LEAVE' => 'On Leave',
                                            'UNAVAILABLE' => 'Unavailable',
                                            default => 'Availability not set',
                                        };
                                    @endphp
                                    <option value="{{ $counselor->id }}" {{ old('counselor_id') == $counselor->id ? 'selected' : '' }}>
                                        {{ $counselor->full_name }}
                                        @if($status !== 'AVAILABLE')
                                            ({{ $statusLabel }})
                                        @endif
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
                                <option value="09:00" data-end-time="10:00">9AM-10AM</option>
                                <option value="10:00" data-end-time="11:00">10AM-11AM</option>
                                <option value="11:00" data-end-time="12:00">11AM-12PM</option>
                                <option value="13:00" data-end-time="14:00">1PM-2PM</option>
                                <option value="14:00" data-end-time="15:00">2PM-3PM</option>
                                <option value="15:00" data-end-time="16:00">3PM-4PM</option>
                                <option value="16:00" data-end-time="17:00">4PM-5PM</option>
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
                                @if(auth()->user()->isStudent())
                                    <option value="regular" {{ old('type') == 'regular' ? 'selected' : '' }}>Consultation</option>
                                    <option value="urgent" {{ old('type') == 'urgent' ? 'selected' : '' }}>Referral</option>
                                    <option value="follow_up" {{ old('type') == 'follow_up' ? 'selected' : '' }}>Consultation</option>
                                @else
                                    <!-- Faculty and Staff only see Consultation and Referral -->
                                    <option value="regular" {{ old('type') == 'regular' ? 'selected' : '' }}>Consultation</option>
                                    <option value="urgent" {{ old('type') == 'urgent' ? 'selected' : '' }}>Referral</option>
                                @endif
                            </select>
                            @error('type')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Guidance Services (Students only) -->
                        @if(auth()->user()->isStudent())
                        <div>
                            <label for="counseling_category" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Guidance Services</label>
                            <select id="counseling_category" name="counseling_category" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Select guidance services</option>
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
                                <span class="text-red-600">*</span> Reason for Referral
                            </label>
                            <textarea id="reason" name="reason" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                      placeholder="Please explain why this appointment is a referral">{{ old('reason') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">This field is required for referral appointments.</p>
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
        const counselorSelect = document.getElementById('counselor_id');
        const dateInput = document.getElementById('appointment_date');
        const timeSelect = document.getElementById('start_time');
        const typeSelect = document.getElementById('type');
        const urgencyDiv = document.getElementById('urgency_reason_div');
        const reasonField = document.getElementById('reason');
        const endTimeField = document.getElementById('end_time');
        const initialSelectedStartTime = @json(old('start_time', ''));

        function populateTimeOptions(slots, emptyMessage, selectedValue = '') {
            timeSelect.innerHTML = '';

            if (!slots || slots.length === 0) {
                timeSelect.innerHTML = `<option value="">${emptyMessage}</option>`;
                timeSelect.disabled = true;
                endTimeField.value = '';
                return;
            }

            timeSelect.disabled = false;
            timeSelect.innerHTML = '<option value="">Select a time</option>';

            slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot.time;
                option.setAttribute('data-end-time', slot.end_time);
                option.textContent = `${slot.formatted_time}${slot.conflict_message || ''}`;
                if (selectedValue && slot.time === selectedValue) {
                    option.selected = true;
                }
                timeSelect.appendChild(option);
            });

            if (timeSelect.value) {
                const selectedOption = timeSelect.options[timeSelect.selectedIndex];
                if (selectedOption && selectedOption.getAttribute('data-end-time')) {
                    endTimeField.value = selectedOption.getAttribute('data-end-time');
                }
            }
        }

        function updateAvailableSlots() {
            const counselorId = counselorSelect.value;
            const date = dateInput.value;
            const isUrgent = typeSelect.value === 'urgent';

            if (!counselorId || !date) {
                return;
            }

            const dayOfWeek = new Date(date).getDay();
            if (dayOfWeek === 0 || dayOfWeek === 6) {
                populateTimeOptions([], 'Appointments are only available on weekdays (Monday through Friday).');
                return;
            }

            fetch(`/api/counselors/${counselorId}/available-slots?date=${encodeURIComponent(date)}&urgent=${isUrgent ? 1 : 0}`)
                .then(response => response.json())
                .then(data => {
                    const selectedValue = timeSelect.value || initialSelectedStartTime;
                    populateTimeOptions(data.slots, data.message, selectedValue);
                })
                .catch(() => {
                    populateTimeOptions([], 'Unable to load available slots. Please try again later.');
                });
        }

        counselorSelect.addEventListener('change', updateAvailableSlots);
        dateInput.addEventListener('change', updateAvailableSlots);

        document.getElementById('type').addEventListener('change', function() {
            const urgencyDiv = document.getElementById('urgency_reason_div');

            updateAppointmentTitle(this.value);

            if (this.value === 'urgent') {
                urgencyDiv.classList.remove('hidden');
                reasonField.required = true;
            } else {
                urgencyDiv.classList.add('hidden');
                reasonField.required = false;
            }

            updateAvailableSlots();
        });

        function updateAppointmentTitle(type) {
            const titleElement = document.getElementById('appointment-title');
            if (type === 'urgent') {
                titleElement.textContent = 'Referral Appointment';
            } else {
                titleElement.textContent = 'Book Appointment';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const typeParam = urlParams.get('type');

            if (typeParam === 'urgent') {
                typeSelect.value = 'urgent';
                updateAppointmentTitle('urgent');
                typeSelect.dispatchEvent(new Event('change'));
            }

            if (counselorSelect.value && dateInput.value) {
                updateAvailableSlots();
            }
        });

        document.getElementById('start_time').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.getAttribute('data-end-time')) {
                endTimeField.value = selectedOption.getAttribute('data-end-time');
            } else {
                endTimeField.value = '';
            }
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            const endTime = document.getElementById('end_time').value;
            if (!endTime) {
                e.preventDefault();
                alert('Please select a time slot first.');
                return false;
            }

            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Booking Appointment...';
        });
    </script>
</x-app-layout> 