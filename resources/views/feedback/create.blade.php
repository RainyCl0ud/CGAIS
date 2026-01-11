<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Feedback Form') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <p class="text-gray-600 mb-4">
                            View the feedback form template below. Click the "Download PDF" button to download the form for offline completion.
                        </p>

                        <div class="flex justify-end mb-6">
                            <a href="{{ route('feedback.download.pdf') }}"
                               target="_blank"
                               class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download PDF
                            </a>
                        </div>
                    </div>

                    <!-- Feedback Form Preview -->
                    <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                        <div class="text-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Client Satisfaction Measurement (CSM)</h3>
                            <p class="text-sm text-gray-600 mt-1">Preview of the downloadable feedback form</p>
                        </div>

                        <!-- Sample Form Content -->
                        <div class="space-y-4 text-sm">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <strong>Client Type:</strong> ________________________
                                </div>
                                <div>
                                    <strong>Date:</strong> ________________________
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <strong>Sex:</strong> ☐ Male ☐ Female
                                </div>
                                <div>
                                    <strong>Age:</strong> ________________________
                                </div>
                            </div>

                            <div>
                                <strong>CC1. Awareness of CC</strong>
                                <div class="ml-4 space-y-1">
                                    <div>☐ I know what a CC is and I saw this office's CC.</div>
                                    <div>☐ I know what a CC is but I did NOT see this office's CC.</div>
                                    <div>☐ I learned of the CC only when I saw this office's CC.</div>
                                    <div>☐ I do not know what a CC is.</div>
                                </div>
                            </div>

                            <div>
                                <strong>Service Quality Dimensions</strong>
                                <div class="overflow-x-auto mt-2">
                                    <table class="min-w-full border border-gray-300 text-xs">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="border border-gray-300 px-2 py-1 text-left">Question</th>
                                                <th class="border border-gray-300 px-2 py-1 text-center">😡<br>Strongly Disagree</th>
                                                <th class="border border-gray-300 px-2 py-1 text-center">☹️<br>Disagree</th>
                                                <th class="border border-gray-300 px-2 py-1 text-center">😐<br>Neither</th>
                                                <th class="border border-gray-300 px-2 py-1 text-center">🙂<br>Agree</th>
                                                <th class="border border-gray-300 px-2 py-1 text-center">😄<br>Strongly Agree</th>
                                                <th class="border border-gray-300 px-2 py-1 text-center">N/A</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="border border-gray-300 px-2 py-1">OOQ1. I am satisfied with the service that I availed.</td>
                                                <td class="border border-gray-300 px-2 py-1 text-center"></td>
                                                <td class="border border-gray-300 px-2 py-1 text-center"></td>
                                                <td class="border border-gray-300 px-2 py-1 text-center"></td>
                                                <td class="border border-gray-300 px-2 py-1 text-center"></td>
                                                <td class="border border-gray-300 px-2 py-1 text-center"></td>
                                                <td class="border border-gray-300 px-2 py-1 text-center"></td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 px-2 py-1">OOQ2. I spent a reasonable amount of time for my transaction.</td>
                                                <td class="border border-gray-300 px-2 py-1 text-center"></td>
                                                <td class="border border-gray-300 px-2 py-1 text-center"></td>
                                                <td class="border border-gray-300 px-2 py-1 text-center"></td>
                                                <td class="border border-gray-300 px-2 py-1 text-center"></td>
                                                <td class="border border-gray-300 px-2 py-1 text-center"></td>
                                                <td class="border border-gray-300 px-2 py-1 text-center"></td>
                                            </tr>
                                            <!-- More rows would be shown in actual PDF -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div>
                                <strong>Suggestions on how we can further improve our services (optional):</strong>
                                <div class="border-b border-gray-400 mt-2 h-8"></div>
                                <div class="border-b border-gray-400 h-8"></div>
                            </div>

                            <div class="text-center mt-6">
                                <strong>THANK YOU!</strong>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('feedback.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back to Feedback List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
