<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Page Title and Edit Button Row -->
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Personal Data Sheet</h1>
                        <a href="{{ route('pds.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Edit PDS
                        </a>
                    </div> 

                    <!-- Completion Progress -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Completion Progress</span>
                            <span class="text-sm font-medium text-gray-900">{{ $pds->getCompletionPercentage() }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $pds->getCompletionPercentage() }}%"></div>
                        </div>
                    </div>



                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Details -->
                        <div class="space-y-4">
                            <h4 class="text-md font-semibold text-gray-800 border-b pb-2">Basic Details</h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Birth Date</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pds->birth_date ? $pds->birth_date->format('M d, Y') : 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Birth Place</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pds->birth_place ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Sex</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pds->getSexLabel() }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Civil Status</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pds->getCivilStatusLabel() }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Citizenship</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pds->citizenship ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Blood Type</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pds->blood_type ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Height</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pds->height ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Weight</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pds->weight ?: 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="space-y-4">
                            <h4 class="text-md font-semibold text-gray-800 border-b pb-2">Contact Information</h4>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Mobile Number</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pds->mobile_number ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Telephone Number</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pds->telephone_number ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permanent Address</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pds->permanent_address ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Present Address</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pds->present_address ?: 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Information -->
                    <div class="mt-8">
                        <h4 class="text-md font-semibold text-gray-800 border-b pb-2 mb-4">Family Information</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <h5 class="text-sm font-medium text-gray-700">Father</h5>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">Name</label>
                                        <p class="text-sm text-gray-900">{{ $pds->father_name ?: 'Not specified' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">Occupation</label>
                                        <p class="text-sm text-gray-900">{{ $pds->father_occupation ?: 'Not specified' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">Contact</label>
                                        <p class="text-sm text-gray-900">{{ $pds->father_contact ?: 'Not specified' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <h5 class="text-sm font-medium text-gray-700">Mother</h5>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">Name</label>
                                        <p class="text-sm text-gray-900">{{ $pds->mother_name ?: 'Not specified' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">Occupation</label>
                                        <p class="text-sm text-gray-900">{{ $pds->mother_occupation ?: 'Not specified' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">Contact</label>
                                        <p class="text-sm text-gray-900">{{ $pds->mother_contact ?: 'Not specified' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 space-y-3">
                            <h5 class="text-sm font-medium text-gray-700">Guardian</h5>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Name</label>
                                    <p class="text-sm text-gray-900">{{ $pds->guardian_name ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Relationship</label>
                                    <p class="text-sm text-gray-900">{{ $pds->guardian_relationship ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Contact</label>
                                    <p class="text-sm text-gray-900">{{ $pds->guardian_contact ?: 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Educational Background -->
                    <div class="mt-8">
                        <h4 class="text-md font-semibold text-gray-800 border-b pb-2 mb-4">Educational Background</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <h5 class="text-sm font-medium text-gray-700">Elementary</h5>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">School</label>
                                        <p class="text-sm text-gray-900">{{ $pds->elementary_school ?: 'Not specified' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">Year Graduated</label>
                                        <p class="text-sm text-gray-900">{{ $pds->elementary_year_graduated ?: 'Not specified' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <h5 class="text-sm font-medium text-gray-700">High School</h5>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">School</label>
                                        <p class="text-sm text-gray-900">{{ $pds->high_school ?: 'Not specified' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">Year Graduated</label>
                                        <p class="text-sm text-gray-900">{{ $pds->high_school_year_graduated ?: 'Not specified' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 space-y-3">
                            <h5 class="text-sm font-medium text-gray-700">College</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">School</label>
                                    <p class="text-sm text-gray-900">{{ $pds->college ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Year Graduated</label>
                                    <p class="text-sm text-gray-900">{{ $pds->college_year_graduated ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Course</label>
                                    <p class="text-sm text-gray-900">{{ $pds->course ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Year Level</label>
                                    <p class="text-sm text-gray-900">{{ $pds->year_level ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Student ID Number</label>
                                    <p class="text-sm text-gray-900">{{ $pds->student_id_number ?: 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="mt-8">
                        <h4 class="text-md font-semibold text-gray-800 border-b pb-2 mb-4">Emergency Contact</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $pds->emergency_contact_name ?: 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Relationship</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $pds->emergency_contact_relationship ?: 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $pds->emergency_contact_number ?: 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $pds->emergency_contact_address ?: 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Health Information -->
                    <div class="mt-8">
                        <h4 class="text-md font-semibold text-gray-800 border-b pb-2 mb-4">Health Information</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Medical Conditions</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $pds->medical_conditions ?: 'None specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Allergies</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $pds->allergies ?: 'None specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Medications</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $pds->medications ?: 'None specified' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="mt-8">
                        <h4 class="text-md font-semibold text-gray-800 border-b pb-2 mb-4">Additional Information</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hobbies</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $pds->hobbies ?: 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Interests</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $pds->interests ?: 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Goals</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $pds->goals ?: 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Concerns</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $pds->concerns ?: 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 