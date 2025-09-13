<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Feedback') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('feedback.update', $feedbackForm) }}">
                        @csrf
                        @method('PUT')

                        <!-- Appointment Selection -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="appointment_id" value="Select Appointment (Optional)" />
                                    <select id="appointment_id" name="appointment_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Choose an appointment</option>
                                        @foreach($appointments as $appointment)
                                            <option value="{{ $appointment->id }}" {{ old('appointment_id', $feedbackForm->appointment_id) == $appointment->id ? 'selected' : '' }}>
                                                {{ $appointment->getFormattedDateTime() }} - {{ $appointment->counselor->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">Select a completed appointment to automatically fill counselor information.</p>
                                    <x-input-error :messages="$errors->get('appointment_id')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="counselor_id" value="Counselor (Optional)" />
                                    <select id="counselor_id" name="counselor_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Choose a counselor</option>
                                        @foreach($counselors as $counselor)
                                            <option value="{{ $counselor->id }}" {{ old('counselor_id', $feedbackForm->counselor_id) == $counselor->id ? 'selected' : '' }}>
                                                {{ $counselor->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">Select a counselor if not choosing an appointment.</p>
                                    <x-input-error :messages="$errors->get('counselor_id')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Ratings -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ratings</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="counselor_rating" value="Counselor Rating" />
                                    <select id="counselor_rating" name="counselor_rating" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select rating</option>
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ old('counselor_rating', $feedbackForm->counselor_rating) == $i ? 'selected' : '' }}>
                                                {{ str_repeat('★', $i) . str_repeat('☆', 5 - $i) }} ({{ $i }}/5)
                                            </option>
                                        @endfor
                                    </select>
                                    <x-input-error :messages="$errors->get('counselor_rating')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="service_rating" value="Service Rating" />
                                    <select id="service_rating" name="service_rating" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select rating</option>
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ old('service_rating', $feedbackForm->service_rating) == $i ? 'selected' : '' }}>
                                                {{ str_repeat('★', $i) . str_repeat('☆', 5 - $i) }} ({{ $i }}/5)
                                            </option>
                                        @endfor
                                    </select>
                                    <x-input-error :messages="$errors->get('service_rating')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="facility_rating" value="Facility Rating" />
                                    <select id="facility_rating" name="facility_rating" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select rating</option>
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ old('facility_rating', $feedbackForm->facility_rating) == $i ? 'selected' : '' }}>
                                                {{ str_repeat('★', $i) . str_repeat('☆', 5 - $i) }} ({{ $i }}/5)
                                            </option>
                                        @endfor
                                    </select>
                                    <x-input-error :messages="$errors->get('facility_rating')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="overall_satisfaction" value="Overall Satisfaction" />
                                    <select id="overall_satisfaction" name="overall_satisfaction" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select rating</option>
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ old('overall_satisfaction', $feedbackForm->overall_satisfaction) == $i ? 'selected' : '' }}>
                                                {{ str_repeat('★', $i) . str_repeat('☆', 5 - $i) }} ({{ $i }}/5)
                                            </option>
                                        @endfor
                                    </select>
                                    <x-input-error :messages="$errors->get('overall_satisfaction')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Feedback Text -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed Feedback</h3>
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="counselor_feedback" value="Counselor Feedback" />
                                    <textarea id="counselor_feedback" name="counselor_feedback" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Share your experience with the counselor...">{{ old('counselor_feedback', $feedbackForm->counselor_feedback) }}</textarea>
                                    <x-input-error :messages="$errors->get('counselor_feedback')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="service_feedback" value="Service Feedback" />
                                    <textarea id="service_feedback" name="service_feedback" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Share your experience with the counseling service...">{{ old('service_feedback', $feedbackForm->service_feedback) }}</textarea>
                                    <x-input-error :messages="$errors->get('service_feedback')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="suggestions" value="Suggestions for Improvement" />
                                    <textarea id="suggestions" name="suggestions" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Any suggestions to improve our services...">{{ old('suggestions', $feedbackForm->suggestions) }}</textarea>
                                    <x-input-error :messages="$errors->get('suggestions')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="concerns" value="Concerns or Issues" />
                                    <textarea id="concerns" name="concerns" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Any concerns or issues you'd like to address...">{{ old('concerns', $feedbackForm->concerns) }}</textarea>
                                    <x-input-error :messages="$errors->get('concerns')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Recommendation -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recommendation</h3>
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="would_recommend" value="Would you recommend our counseling services?" />
                                    <div class="mt-2 space-y-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="would_recommend" value="1" class="form-radio" {{ old('would_recommend', $feedbackForm->would_recommend) === '1' ? 'checked' : '' }}>
                                            <span class="ml-2">Yes</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="would_recommend" value="0" class="form-radio" {{ old('would_recommend', $feedbackForm->would_recommend) === '0' ? 'checked' : '' }}>
                                            <span class="ml-2">No</span>
                                        </label>
                                    </div>
                                    <x-input-error :messages="$errors->get('would_recommend')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="recommendation_reason" value="Reason for Recommendation" />
                                    <textarea id="recommendation_reason" name="recommendation_reason" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Please explain your reason...">{{ old('recommendation_reason', $feedbackForm->recommendation_reason) }}</textarea>
                                    <x-input-error :messages="$errors->get('recommendation_reason')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Additional Comments -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Comments</h3>
                            <div>
                                <x-input-label for="additional_comments" value="Additional Comments" />
                                <textarea id="additional_comments" name="additional_comments" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Any additional comments or feedback...">{{ old('additional_comments', $feedbackForm->additional_comments) }}</textarea>
                                <x-input-error :messages="$errors->get('additional_comments')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('feedback.show', $feedbackForm) }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <x-primary-button class="ml-3">
                                {{ __('Update Feedback') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 