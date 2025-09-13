<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Feedback Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('feedback.edit', $feedbackForm) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Edit
                </a>
                <a href="{{ route('feedback.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Feedback Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Feedback Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Submitted Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $feedbackForm->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Counselor</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $feedbackForm->counselor ? $feedbackForm->counselor->full_name : 'Not specified' }}</p>
                            </div>
                            @if($feedbackForm->appointment)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Related Appointment</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $feedbackForm->appointment->getFormattedDateTime() }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ratings -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ratings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Counselor Rating</label>
                                <div class="mt-1 flex items-center">
                                    <span class="text-yellow-500 text-lg">{{ $feedbackForm->getRatingStars($feedbackForm->counselor_rating) }}</span>
                                    <span class="ml-2 text-sm text-gray-600">({{ $feedbackForm->counselor_rating }}/5)</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Service Rating</label>
                                <div class="mt-1 flex items-center">
                                    <span class="text-yellow-500 text-lg">{{ $feedbackForm->getRatingStars($feedbackForm->service_rating) }}</span>
                                    <span class="ml-2 text-sm text-gray-600">({{ $feedbackForm->service_rating }}/5)</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Facility Rating</label>
                                <div class="mt-1 flex items-center">
                                    <span class="text-yellow-500 text-lg">{{ $feedbackForm->getRatingStars($feedbackForm->facility_rating) }}</span>
                                    <span class="ml-2 text-sm text-gray-600">({{ $feedbackForm->facility_rating }}/5)</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Overall Satisfaction</label>
                                <div class="mt-1 flex items-center">
                                    <span class="text-yellow-500 text-lg">{{ $feedbackForm->getRatingStars($feedbackForm->overall_satisfaction) }}</span>
                                    <span class="ml-2 text-sm text-gray-600">({{ $feedbackForm->overall_satisfaction }}/5)</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Average Rating</label>
                            <div class="mt-1 flex items-center">
                                <span class="text-yellow-500 text-lg">{{ $feedbackForm->getRatingStars($feedbackForm->getAverageRating()) }}</span>
                                <span class="ml-2 text-sm text-gray-600">({{ $feedbackForm->getAverageRating() }}/5)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback Text -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed Feedback</h3>
                        <div class="space-y-4">
                            @if($feedbackForm->counselor_feedback)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Counselor Feedback</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $feedbackForm->counselor_feedback }}</p>
                            </div>
                            @endif
                            @if($feedbackForm->service_feedback)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Service Feedback</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $feedbackForm->service_feedback }}</p>
                            </div>
                            @endif
                            @if($feedbackForm->suggestions)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Suggestions for Improvement</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $feedbackForm->suggestions }}</p>
                            </div>
                            @endif
                            @if($feedbackForm->concerns)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Concerns or Issues</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $feedbackForm->concerns }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recommendation -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recommendation</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Would you recommend our counseling services?</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $feedbackForm->getRecommendationBadgeClass() }}">
                                    {{ $feedbackForm->getRecommendationLabel() }}
                                </span>
                            </div>
                            @if($feedbackForm->recommendation_reason)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Reason for Recommendation</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $feedbackForm->recommendation_reason }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Additional Comments -->
                    @if($feedbackForm->additional_comments)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Comments</h3>
                        <div>
                            <p class="text-sm text-gray-900">{{ $feedbackForm->additional_comments }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 