<x-app-layout>
    @php
        $pageTitle = 'Edit Personal Data Sheet';
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('pds.update') }}" id="pds-form">
                        @csrf
                        @method('PATCH')

                        <!-- Completion Progress -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Completion Progress</span>
                                <span class="text-sm font-medium text-gray-900" id="completion-percentage">{{ $pds->getCompletionPercentage() }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" id="completion-bar" style="width: {{ $pds->getCompletionPercentage() }}%"></div>
                            </div>
                        </div>

                        <!-- Basic Details -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="birth_date" value="Birth Date" />
                                    <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date', $pds->birth_date?->format('Y-m-d'))" />
                                    <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="birth_place" value="Birth Place" />
                                    <x-text-input id="birth_place" name="birth_place" type="text" class="mt-1 block w-full" :value="old('birth_place', $pds->birth_place)" />
                                    <x-input-error :messages="$errors->get('birth_place')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="sex" value="Sex" />
                                    <select id="sex" name="sex" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Sex</option>
                                        <option value="male" {{ old('sex', $pds->sex) === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('sex', $pds->sex) === 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('sex')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="civil_status" value="Civil Status" />
                                    <select id="civil_status" name="civil_status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Civil Status</option>
                                        <option value="single" {{ old('civil_status', $pds->civil_status) === 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('civil_status', $pds->civil_status) === 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="widowed" {{ old('civil_status', $pds->civil_status) === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="separated" {{ old('civil_status', $pds->civil_status) === 'separated' ? 'selected' : '' }}>Separated</option>
                                        <option value="divorced" {{ old('civil_status', $pds->civil_status) === 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('civil_status')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="citizenship" value="Citizenship" />
                                    <x-text-input id="citizenship" name="citizenship" type="text" class="mt-1 block w-full" :value="old('citizenship', $pds->citizenship)" />
                                    <x-input-error :messages="$errors->get('citizenship')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="blood_type" value="Blood Type" />
                                    <x-text-input id="blood_type" name="blood_type" type="text" class="mt-1 block w-full" :value="old('blood_type', $pds->blood_type)" />
                                    <x-input-error :messages="$errors->get('blood_type')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="height" value="Height" />
                                    <x-text-input id="height" name="height" type="text" class="mt-1 block w-full" :value="old('height', $pds->height)" />
                                    <x-input-error :messages="$errors->get('height')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="weight" value="Weight" />
                                    <x-text-input id="weight" name="weight" type="text" class="mt-1 block w-full" :value="old('weight', $pds->weight)" />
                                    <x-input-error :messages="$errors->get('weight')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="mobile_number" value="Mobile Number" />
                                    <x-text-input id="mobile_number" name="mobile_number" type="text" class="mt-1 block w-full" :value="old('mobile_number', $pds->mobile_number)" />
                                    <x-input-error :messages="$errors->get('mobile_number')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="telephone_number" value="Telephone Number" />
                                    <x-text-input id="telephone_number" name="telephone_number" type="text" class="mt-1 block w-full" :value="old('telephone_number', $pds->telephone_number)" />
                                    <x-input-error :messages="$errors->get('telephone_number')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="permanent_address" value="Permanent Address" />
                                    <textarea id="permanent_address" name="permanent_address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('permanent_address', $pds->permanent_address) }}</textarea>
                                    <x-input-error :messages="$errors->get('permanent_address')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="present_address" value="Present Address" />
                                    <textarea id="present_address" name="present_address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('present_address', $pds->present_address) }}</textarea>
                                    <x-input-error :messages="$errors->get('present_address')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Family Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Family Information</h3>
                            
                            <!-- Father -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-800 mb-3">Father</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="father_name" value="Name" />
                                        <x-text-input id="father_name" name="father_name" type="text" class="mt-1 block w-full" :value="old('father_name', $pds->father_name)" />
                                        <x-input-error :messages="$errors->get('father_name')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="father_occupation" value="Occupation" />
                                        <x-text-input id="father_occupation" name="father_occupation" type="text" class="mt-1 block w-full" :value="old('father_occupation', $pds->father_occupation)" />
                                        <x-input-error :messages="$errors->get('father_occupation')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="father_contact" value="Contact" />
                                        <x-text-input id="father_contact" name="father_contact" type="text" class="mt-1 block w-full" :value="old('father_contact', $pds->father_contact)" />
                                        <x-input-error :messages="$errors->get('father_contact')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Mother -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-800 mb-3">Mother</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="mother_name" value="Name" />
                                        <x-text-input id="mother_name" name="mother_name" type="text" class="mt-1 block w-full" :value="old('mother_name', $pds->mother_name)" />
                                        <x-input-error :messages="$errors->get('mother_name')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="mother_occupation" value="Occupation" />
                                        <x-text-input id="mother_occupation" name="mother_occupation" type="text" class="mt-1 block w-full" :value="old('mother_occupation', $pds->mother_occupation)" />
                                        <x-input-error :messages="$errors->get('mother_occupation')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="mother_contact" value="Contact" />
                                        <x-text-input id="mother_contact" name="mother_contact" type="text" class="mt-1 block w-full" :value="old('mother_contact', $pds->mother_contact)" />
                                        <x-input-error :messages="$errors->get('mother_contact')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Guardian -->
                            <div>
                                <h4 class="text-md font-medium text-gray-800 mb-3">Guardian</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="guardian_name" value="Name" />
                                        <x-text-input id="guardian_name" name="guardian_name" type="text" class="mt-1 block w-full" :value="old('guardian_name', $pds->guardian_name)" />
                                        <x-input-error :messages="$errors->get('guardian_name')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="guardian_relationship" value="Relationship" />
                                        <x-text-input id="guardian_relationship" name="guardian_relationship" type="text" class="mt-1 block w-full" :value="old('guardian_relationship', $pds->guardian_relationship)" />
                                        <x-input-error :messages="$errors->get('guardian_relationship')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="guardian_contact" value="Contact" />
                                        <x-text-input id="guardian_contact" name="guardian_contact" type="text" class="mt-1 block w-full" :value="old('guardian_contact', $pds->guardian_contact)" />
                                        <x-input-error :messages="$errors->get('guardian_contact')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Educational Background -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Educational Background</h3>
                            
                            <!-- Elementary -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-800 mb-3">Elementary</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="elementary_school" value="School" />
                                        <x-text-input id="elementary_school" name="elementary_school" type="text" class="mt-1 block w-full" :value="old('elementary_school', $pds->elementary_school)" />
                                        <x-input-error :messages="$errors->get('elementary_school')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="elementary_year_graduated" value="Year Graduated" />
                                        <x-text-input id="elementary_year_graduated" name="elementary_year_graduated" type="text" class="mt-1 block w-full" :value="old('elementary_year_graduated', $pds->elementary_year_graduated)" />
                                        <x-input-error :messages="$errors->get('elementary_year_graduated')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- High School -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-800 mb-3">High School</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="high_school" value="School" />
                                        <x-text-input id="high_school" name="high_school" type="text" class="mt-1 block w-full" :value="old('high_school', $pds->high_school)" />
                                        <x-input-error :messages="$errors->get('high_school')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="high_school_year_graduated" value="Year Graduated" />
                                        <x-text-input id="high_school_year_graduated" name="high_school_year_graduated" type="text" class="mt-1 block w-full" :value="old('high_school_year_graduated', $pds->high_school_year_graduated)" />
                                        <x-input-error :messages="$errors->get('high_school_year_graduated')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- College -->
                            <div>
                                <h4 class="text-md font-medium text-gray-800 mb-3">College</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="college" value="School" />
                                        <x-text-input id="college" name="college" type="text" class="mt-1 block w-full" :value="old('college', $pds->college)" />
                                        <x-input-error :messages="$errors->get('college')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="college_year_graduated" value="Year Graduated" />
                                        <x-text-input id="college_year_graduated" name="college_year_graduated" type="text" class="mt-1 block w-full" :value="old('college_year_graduated', $pds->college_year_graduated)" />
                                        <x-input-error :messages="$errors->get('college_year_graduated')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="course" value="Course" />
                                        <x-text-input id="course" name="course" type="text" class="mt-1 block w-full" :value="old('course', $pds->course)" />
                                        <x-input-error :messages="$errors->get('course')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="year_level" value="Year Level" />
                                        <x-text-input id="year_level" name="year_level" type="text" class="mt-1 block w-full" :value="old('year_level', $pds->year_level)" />
                                        <x-input-error :messages="$errors->get('year_level')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="student_id_number" value="Student ID Number" />
                                        <x-text-input id="student_id_number" name="student_id_number" type="text" class="mt-1 block w-full" :value="old('student_id_number', $pds->student_id_number)" />
                                        <x-input-error :messages="$errors->get('student_id_number')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Emergency Contact</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="emergency_contact_name" value="Name" />
                                    <x-text-input id="emergency_contact_name" name="emergency_contact_name" type="text" class="mt-1 block w-full" :value="old('emergency_contact_name', $pds->emergency_contact_name)" />
                                    <x-input-error :messages="$errors->get('emergency_contact_name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="emergency_contact_relationship" value="Relationship" />
                                    <x-text-input id="emergency_contact_relationship" name="emergency_contact_relationship" type="text" class="mt-1 block w-full" :value="old('emergency_contact_relationship', $pds->emergency_contact_relationship)" />
                                    <x-input-error :messages="$errors->get('emergency_contact_relationship')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="emergency_contact_number" value="Contact Number" />
                                    <x-text-input id="emergency_contact_number" name="emergency_contact_number" type="text" class="mt-1 block w-full" :value="old('emergency_contact_number', $pds->emergency_contact_number)" />
                                    <x-input-error :messages="$errors->get('emergency_contact_number')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="emergency_contact_address" value="Address" />
                                    <textarea id="emergency_contact_address" name="emergency_contact_address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('emergency_contact_address', $pds->emergency_contact_address) }}</textarea>
                                    <x-input-error :messages="$errors->get('emergency_contact_address')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Health Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <x-input-label for="medical_conditions" value="Medical Conditions" />
                                    <textarea id="medical_conditions" name="medical_conditions" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('medical_conditions', $pds->medical_conditions) }}</textarea>
                                    <x-input-error :messages="$errors->get('medical_conditions')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="allergies" value="Allergies" />
                                    <textarea id="allergies" name="allergies" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('allergies', $pds->allergies) }}</textarea>
                                    <x-input-error :messages="$errors->get('allergies')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="medications" value="Medications" />
                                    <textarea id="medications" name="medications" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('medications', $pds->medications) }}</textarea>
                                    <x-input-error :messages="$errors->get('medications')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="hobbies" value="Hobbies" />
                                    <textarea id="hobbies" name="hobbies" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('hobbies', $pds->hobbies) }}</textarea>
                                    <x-input-error :messages="$errors->get('hobbies')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="interests" value="Interests" />
                                    <textarea id="interests" name="interests" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('interests', $pds->interests) }}</textarea>
                                    <x-input-error :messages="$errors->get('interests')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="goals" value="Goals" />
                                    <textarea id="goals" name="goals" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('goals', $pds->goals) }}</textarea>
                                    <x-input-error :messages="$errors->get('goals')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="concerns" value="Concerns" />
                                    <textarea id="concerns" name="concerns" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('concerns', $pds->concerns) }}</textarea>
                                    <x-input-error :messages="$errors->get('concerns')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ml-3">
                                {{ __('Save Changes') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-save functionality
        let autoSaveTimeout;
        const form = document.getElementById('pds-form');
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            input.addEventListener('input', () => {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    autoSave();
                }, 2000); // Auto-save after 2 seconds of inactivity
            });
        });

        function autoSave() {
            const formData = new FormData(form);
            
            fetch('{{ route("pds.auto-save") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update completion percentage
                    document.getElementById('completion-percentage').textContent = data.completion_percentage + '%';
                    document.getElementById('completion-bar').style.width = data.completion_percentage + '%';
                    
                    // Show success message
                    showNotification('Data auto-saved successfully', 'success');
                }
            })
            .catch(error => {
                console.error('Auto-save error:', error);
                showNotification('Auto-save failed', 'error');
            });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</x-app-layout> 