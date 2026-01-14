<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Document Codes') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-white">
        <div class="w-full">
            <div class="bg-white min-h-screen">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Document Code Management</h3>
                        <p class="text-sm text-gray-600">Select which document code you want to edit. You can manage separate codes for Personal Data Sheets (PDS) and Feedback Forms.</p>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- PDS Document Code Card -->
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-medium text-gray-900">Personal Data Sheet (PDS)</h4>
                                        <p class="text-sm text-gray-600">Manage document code for student PDS forms</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500 mb-4">
                                    <p>Current Code: <span class="font-mono text-gray-700">{{ \App\Models\DocumentCode::where('type', 'pds')->first()?->document_code_no ?? 'FM-USTP-GCS-02' }}</span></p>
                                </div>
                                <a href="{{ route('document-codes.edit', 'pds') }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 w-full justify-center">
                                    Edit PDS Document Code
                                </a>
                            </div>
                        </div>

                        <!-- Feedback Form Document Code Card -->
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-medium text-gray-900">Feedback Form</h4>
                                        <p class="text-sm text-gray-600">Manage document code for feedback forms</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500 mb-4">
                                    <p>Current Code: <span class="font-mono text-gray-700">{{ \App\Models\DocumentCode::where('type', 'feedback_form')->first()?->document_code_no ?? 'FM-USTP-GCS-01' }}</span></p>
                                </div>
                                <a href="{{ route('document-codes.edit', 'feedback_form') }}"
                                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 w-full justify-center">
                                    Edit Feedback Form Document Code
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
