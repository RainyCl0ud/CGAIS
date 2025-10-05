    <x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4 overflow-auto min-h-screen bg-gradient-to-r from-yellow-200 via-white to-blue-300">
            <div class="w-full h-full mx-auto">
                <div class="bg-white/90 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-6 sm:p-10 backdrop-blur h-full">
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
                            <a href="{{ route('student.appointments.index') }}" 
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

    // Load slots when counselor or date changes
    counselorSelect.addEventListener('change', function() {
        if (counselorSelect.value && dateInput.value) {
            loadAvailableTimeSlots(counselorSelect.value, dateInput.value);
        }
    });

    dateInput.addEventListener('change', function() {
        const date = this.value;
        const dayOfWeek = new Date(date).getDay();

        if (dayOfWeek === 0 || dayOfWeek === 6) {
            timeSelect.innerHTML = '<option value="">Appointments are only available on weekdays (Monday through Friday).</option>';
            timeSelect.disabled = true;
            return;
        }

        timeSelect.disabled = false;

        if (counselorSelect.value && date) {
            loadAvailableTimeSlots(counselorSelect.value, date);
        }
    });

    // Urgent appointment handling
    typeSelect.addEventListener('change', function() {
        if (this.value === 'urgent') {
            urgencyDiv.classList.remove('hidden');
            reasonField.required = true;
        } else {
            urgencyDiv.classList.add('hidden');
            reasonField.required = false;
        }

        if (counselorSelect.value && dateInput.value) {
            loadAvailableTimeSlots(counselorSelect.value, dateInput.value);
        }
    });

    // Auto-load on page ready (useful for old input restore)
    document.addEventListener('DOMContentLoaded', function() {
        if (counselorSelect.value && dateInput.value) {
            loadAvailableTimeSlots(counselorSelect.value, dateInput.value);
        }
    });

    function loadAvailableTimeSlots(counselorId, date) {
        const isUrgent = typeSelect.value === 'urgent';
        timeSelect.innerHTML = '<option value="">Loading available times...</option>';

        fetch(`/api/student/counselors/${counselorId}/available-slots?date=${date}&urgent=${isUrgent}`)
            .then(response => response.json())
            .then(data => {
                timeSelect.innerHTML = '<option value="">Select a time</option>';

                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot.time;
                        option.textContent = slot.formatted_time + slot.conflict_message;
                        option.setAttribute('data-end-time', slot.end_time);

                        if (slot.is_conflict) {
                            option.style.color = '#dc2626';
                            option.style.fontWeight = 'bold';
                        }

                        timeSelect.appendChild(option);
                    });

                    if (isUrgent && data.slots.some(slot => slot.is_conflict)) {
                        const warningDiv = document.createElement('div');
                        warningDiv.className = 'mt-2 p-2 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded text-xs';
                        warningDiv.innerHTML = '⚠️ <strong>Urgent Booking Notice:</strong> Some slots conflict with existing appointments. Your urgent request will be reviewed by the counselor.';

                        const existingWarning = timeSelect.parentElement.querySelector('.bg-yellow-100');
                        if (existingWarning) existingWarning.remove();

                        timeSelect.parentElement.appendChild(warningDiv);
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

    // Set hidden end_time when time is selected
    timeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.getAttribute('data-end-time')) {
            endTimeField.value = selectedOption.getAttribute('data-end-time');
        } else {
            endTimeField.value = '';
        }
    });
</script>

</x-app-layout> 