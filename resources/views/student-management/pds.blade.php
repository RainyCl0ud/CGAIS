<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Personal Data Sheet') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $student->getFullNameAttribute() }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('students.show', $student) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Profile
                </a>
                <button onclick="window.print()" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print PDS
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($student->personalDataSheet)
                <!-- PDS Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">Personal Data Sheet</h3>
                                <p class="text-gray-600">{{ $student->getFullNameAttribute() }}</p>
                                <p class="text-sm text-gray-500">Student ID: {{ $student->student_id ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                @php
                                    $completion = $student->personalDataSheet->getCompletionPercentage();
                                @endphp
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-{{ $completion >= 80 ? 'green' : ($completion >= 50 ? 'yellow' : 'red') }}-600 h-2 rounded-full" style="width: {{ $completion }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $completion }}% Complete</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $student->personalDataSheet->isComplete() ? 'PDS is complete' : 'PDS needs completion' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Basic Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Basic Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Birth Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->birth_date ? $student->personalDataSheet->birth_date->format('F j, Y') : 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Birth Place</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->birth_place ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sex</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->getSexLabel() ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Civil Status</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->civil_status ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Citizenship</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->citizenship ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Height</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->height ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Weight</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->weight ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Blood Type</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->blood_type ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Mobile Number</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->mobile_number ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Telephone Number</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->telephone_number ?? 'Not provided' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Permanent Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->permanent_address ?? 'Not provided' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Present Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->present_address ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Family Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Family Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h5 class="text-md font-medium text-gray-800 mb-3">Father's Information</h5>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Name</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->father_name ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Occupation</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->father_occupation ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->father_contact ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-md font-medium text-gray-800 mb-3">Mother's Information</h5>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Name</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->mother_name ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Occupation</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->mother_occupation ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->mother_contact ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <h5 class="text-md font-medium text-gray-800 mb-3">Guardian Information</h5>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->guardian_name ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Relationship</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->guardian_relationship ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->guardian_contact ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Educational Background -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Educational Background</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h5 class="text-md font-medium text-gray-800 mb-3">Elementary</h5>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">School</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->elementary_school ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Year Graduated</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->elementary_year_graduated ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-md font-medium text-gray-800 mb-3">High School</h5>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">School</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->high_school ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Year Graduated</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->high_school_year_graduated ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <h5 class="text-md font-medium text-gray-800 mb-3">College</h5>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">School</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->college ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Course</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->course ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Year Level</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->year_level ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Emergency Contact</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contact Person</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->emergency_contact_name ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Relationship</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->emergency_contact_relationship ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->emergency_contact_number ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->emergency_contact_address ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Medical Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Medical Conditions</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->medical_conditions ?? 'None' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Allergies</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->allergies ?? 'None' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Medications</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->medications ?? 'None' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Personal Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hobbies</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->hobbies ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Interests</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->interests ?? 'Not provided' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Goals</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->goals ?? 'Not provided' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Concerns</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $student->personalDataSheet->concerns ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Personal Data Sheet</h3>
                        <p class="mt-1 text-sm text-gray-500">This student hasn't completed their personal data sheet yet.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        @media print {
            .header, .sidebar, .print-button {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 20px;
            }
            .bg-white {
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
            }
        }
    </style>
</x-app-layout>
