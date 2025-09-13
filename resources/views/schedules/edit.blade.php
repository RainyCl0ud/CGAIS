<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4">
            <div class="w-full max-w-2xl mx-auto">
                <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                    <div class="mb-4 sm:mb-6">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900">Edit Schedule</h1>
                        <p class="text-gray-600 text-xs sm:text-sm mt-1">Update your availability for {{ ucfirst($schedule->day_of_week) }} (Monday/Friday only)</p>
                    </div>

                    @if(session('error'))
                        <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('schedules.update', $schedule) }}" class="space-y-4 sm:space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Day Display (Read-only) -->
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Day of Week</label>
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm text-gray-700">
                                @php
                                    $today = now();
                                    $dayName = ucfirst($schedule->day_of_week);
                                    $nextOccurrence = $today->copy()->next(strtolower($schedule->day_of_week));
                                    $formattedDate = $nextOccurrence->format('M d, Y');
                                @endphp
                                {{ $dayName }} - Next: {{ $formattedDate }}
                            </div>
                            <p class="text-xs text-gray-500 mt-1">This schedule applies to every {{ strtolower($dayName) }}</p>
                        </div>

                        <!-- Start Time -->
                        <div>
                            <label for="start_time" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Start Time</label>
                            <input type="time" id="start_time" name="start_time" required 
                                   value="{{ old('start_time', $schedule->start_time->format('H:i')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                            @error('start_time')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div>
                            <label for="end_time" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">End Time</label>
                            <input type="time" id="end_time" name="end_time" required 
                                   value="{{ old('end_time', $schedule->end_time->format('H:i')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                            @error('end_time')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Max Appointments -->
                        <div>
                            <label for="max_appointments" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Maximum Appointments</label>
                            <input type="number" id="max_appointments" name="max_appointments" required 
                                   min="1" max="10" value="{{ old('max_appointments', $schedule->max_appointments) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                            @error('max_appointments')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Availability Toggle -->
                        <div class="flex items-center">
                            <input type="checkbox" id="is_available" name="is_available" value="1" 
                                   {{ old('is_available', $schedule->is_available) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_available" class="ml-2 block text-xs sm:text-sm text-gray-900">
                                Available for appointments
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 pt-4">
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 sm:py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                Update Schedule
                            </button>
                            <a href="{{ route('schedules.index') }}" 
                               class="flex-1 px-4 py-2 sm:py-3 bg-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-400 transition-colors text-center text-sm">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</x-app-layout> 