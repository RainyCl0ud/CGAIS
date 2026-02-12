<x-guest-layout>
    <!-- Role Selection Modal -->
    <div id="roleSelectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4 text-center">Select Your Role</h2>
            <p class="text-gray-600 text-center mb-6">Please select the role you are registering as:</p>
            
            <div class="grid grid-cols-1 gap-4">
                <button type="button" onclick="selectRole('student')" class="role-btn flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="ml-4 text-left">
                        <h3 class="text-lg font-semibold text-gray-900">Student</h3>
                        <p class="text-sm text-gray-500">Register as a student</p>
                    </div>
                </button>

                <button type="button" onclick="selectRole('faculty')" class="role-btn flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all">
                    <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4 text-left">
                        <h3 class="text-lg font-semibold text-gray-900">Faculty</h3>
                        <p class="text-sm text-gray-500">Register as a faculty member</p>
                    </div>
                </button>

                <button type="button" onclick="selectRole('staff')" class="role-btn flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-all">
                    <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-4 text-left">
                        <h3 class="text-lg font-semibold text-gray-900">Non-Teaching Staff</h3>
                        <p class="text-sm text-gray-500">Register as a Non-Teaching Staff member</p>
                    </div>
                </button>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Already have an account? Login</a>
            </div>
        </div>
    </div>

    <!-- Registration Form (hidden until role is selected) -->
    <div id="registrationForm" class="hidden">
        <!-- Information Box -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Registration Requirements</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>To register an account, you must provide a valid Student ID or Faculty/Staff ID that has been pre-authorized by a counselor. If you don't have an authorized ID, please contact the counseling office.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Badge -->
        <div class="mb-4 flex items-center justify-between">
            <span id="selectedRoleBadge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                Registering as: <span id="selectedRoleText" class="ml-1 font-bold">Student</span>
            </span>
            <button type="button" onclick="changeRole()" class="text-sm text-blue-600 hover:text-blue-800 underline">Change Role</button>
        </div>

        <form id="registerForm" method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="role" id="roleInput" value="student">

            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-100 border-red-400 text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Student Registration Fields -->
            <div id="studentFields" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div class="relative">
                        <x-input-label for="student_first_name" :value="__('First Name')" />
                        <x-text-input id="student_first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus placeholder="Juan" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                    <!-- Middle Name -->
                    <div class="relative">
                        <x-input-label for="student_middle_name" :value="__('Middle Name (optional)')" />
                        <x-text-input id="student_middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name')" placeholder="Santos" />
                        <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Last Name -->
                    <div class="relative">
                        <x-input-label for="student_last_name" :value="__('Last Name')" />
                        <x-text-input id="student_last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required placeholder="Dela Cruz" />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                    <!-- Name Extension -->
                    <div class="relative">
                        <x-input-label for="student_name_extension" :value="__('Name Extension (optional)')" />
                        <x-text-input id="student_name_extension" class="block mt-1 w-full" type="text" name="name_extension" :value="old('name_extension')" placeholder="Jr., Sr., III" />
                        <x-input-error :messages="$errors->get('name_extension')" class="mt-2" />
                    </div>
                </div>

                <!-- Email Address -->
                <div class="relative">
                    <x-input-label for="student_email" :value="__('Email Address')" />
                    <x-text-input id="student_email" class="block mt-1 w-full" type="email" name="email" autocomplete="off" required placeholder="juan.delacruz@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Mobile Phone Number -->
                <div class="relative">
                    <x-input-label for="student_phone" :value="__('Mobile Phone Number')" />
                    <x-text-input id="student_phone" class="block mt-1 w-full" type="tel" name="phone_number" :value="old('phone_number')" required placeholder="09123456789" />
                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                </div>

                <!-- Student ID -->
                <div class="relative">
                    <x-input-label for="student_id" :value="__('Student ID')" />
                    <x-text-input id="student_id" class="block mt-1 w-full" type="text" name="student_id" :value="old('student_id')" required placeholder="2021000001" />
                    <div class="mt-1 text-xs text-gray-500">You must provide a valid Student ID that has been pre-authorized by a counselor.</div>
                    <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                </div>

                <!-- Course -->
                <div class="relative">
                    <x-input-label for="course_id" :value="__('Course')" />
                    <select id="course_id" name="course_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">Select Program</option>
                        @foreach(\App\Models\Course::active()->get() as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->description }} ({{ $course->name }} {{$course->code}})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                </div>

                <!-- Year Level -->
                <div class="relative">
                    <x-input-label for="year_level" :value="__('Year Level')" />
                    <select id="year_level" name="year_level" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">Select Year Level</option>
                        <option value="1st Year" {{ old('year_level') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                        <option value="2nd Year" {{ old('year_level') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3rd Year" {{ old('year_level') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4th Year" {{ old('year_level') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                    </select>
                    <x-input-error :messages="$errors->get('year_level')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div class="relative">
                        <x-input-label for="student_password" :value="__('Password')" />
                        <div class="relative">
                            <x-text-input id="student_password" class="block mt-1 w-full pr-10" type="password" name="password" autocomplete="new-password" required placeholder="Enter your password" />
                            <button type="button" class="absolute inset-y-0 end-0 px-3 flex items-center text-gray-600" onclick="togglePasswordVisibility('student_password', this)" tabindex="-1">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <!-- Confirm Password -->
                    <div class="relative">
                        <x-input-label for="student_password_confirmation" :value="__('Confirm Password')" />
                        <div class="relative">
                            <x-text-input id="student_password_confirmation" class="block mt-1 w-full pr-10" type="password" name="password_confirmation" autocomplete="new-password" required placeholder="Confirm your password" />
                            <button type="button" class="absolute inset-y-0 end-0 px-3 flex items-center text-gray-600" onclick="togglePasswordVisibility('student_password_confirmation', this)" tabindex="-1">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Faculty/Staff Registration Fields -->
            <div id="facultyStaffFields" class="hidden space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div class="relative">
                        <x-input-label for="fs_first_name" :value="__('First Name')" />
                        <x-text-input id="fs_first_name" class="block mt-1 w-full fs-input" type="text" name="first_name" :value="old('first_name')" placeholder="Juan" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                    <!-- Middle Name -->
                    <div class="relative">
                        <x-input-label for="fs_middle_name" :value="__('Middle Name (optional)')" />
                        <x-text-input id="fs_middle_name" class="block mt-1 w-full fs-input" type="text" name="middle_name" :value="old('middle_name')" placeholder="Santos" />
                        <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Last Name -->
                    <div class="relative">
                        <x-input-label for="fs_last_name" :value="__('Last Name')" />
                        <x-text-input id="fs_last_name" class="block mt-1 w-full fs-input" type="text" name="last_name" :value="old('last_name')" placeholder="Dela Cruz" />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                    <!-- Name Extension -->
                    <div class="relative">
                        <x-input-label for="fs_name_extension" :value="__('Name Extension (optional)')" />
                        <x-text-input id="fs_name_extension" class="block mt-1 w-full fs-input" type="text" name="name_extension" :value="old('name_extension')" placeholder="Jr., Sr., III" />
                        <x-input-error :messages="$errors->get('name_extension')" class="mt-2" />
                    </div>
                </div>

                <!-- Email Address -->
                <div class="relative">
                    <x-input-label for="fs_email" :value="__('Email Address')" />
                    <x-text-input id="fs_email" class="block mt-1 w-full fs-input" type="email" name="email" autocomplete="off" placeholder="juan.delacruz@ustp.edu.ph" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Phone Number -->
                <div class="relative">
                    <x-input-label for="fs_phone" :value="__('Phone Number')" />
                    <x-text-input id="fs_phone" class="block mt-1 w-full fs-input" type="tel" name="phone_number" :value="old('phone_number')" placeholder="09123456789" />
                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                </div>

                <!-- Faculty/Staff ID -->
                <div id="facultyIdField" class="relative">
                    <x-input-label for="faculty_id" :value="__('Faculty ID Number')" />
                    <x-text-input id="faculty_id" class="block mt-1 w-full fs-input" type="text" name="faculty_id" :value="old('faculty_id')" placeholder="FAC001" />
                    <div class="mt-1 text-xs text-gray-500">You must provide a valid Faculty ID that has been pre-authorized by a counselor.</div>
                    <x-input-error :messages="$errors->get('faculty_id')" class="mt-2" />
                </div>

                <div id="staffIdField" class="relative hidden">
                    <x-input-label for="staff_id" :value="__('Staff ID Number')" />
                    <x-text-input id="staff_id" class="block mt-1 w-full fs-input" type="text" name="staff_id" :value="old('staff_id')" placeholder="STAFF001" />
                    <div class="mt-1 text-xs text-gray-500">You must provide a valid Staff ID that has been pre-authorized by a counselor.</div>
                    <x-input-error :messages="$errors->get('staff_id')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div class="relative">
                        <x-input-label for="fs_password" :value="__('Password')" />
                        <div class="relative">
                            <x-text-input id="fs_password" class="block mt-1 w-full pr-10 fs-input" type="password" name="password" autocomplete="new-password" placeholder="Enter your password" />
                            <button type="button" class="absolute inset-y-0 end-0 px-3 flex items-center text-gray-600" onclick="togglePasswordVisibility('fs_password', this)" tabindex="-1">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <!-- Confirm Password -->
                    <div class="relative">
                        <x-input-label for="fs_password_confirmation" :value="__('Confirm Password')" />
                        <div class="relative">
                            <x-text-input id="fs_password_confirmation" class="block mt-1 w-full pr-10 fs-input" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Confirm your password" />
                            <button type="button" class="absolute inset-y-0 end-0 px-3 flex items-center text-gray-600" onclick="togglePasswordVisibility('fs_password_confirmation', this)" tabindex="-1">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions Modal -->
            <div id="termsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 text-center">Terms and Conditions</h3>
                        <div id="termsContent" class="max-h-80 overflow-y-auto text-sm text-gray-700 mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <p><strong>1. Acceptance of Terms</strong></p>
                            <p>By registering for an account on this Counseling Guidance Appointment System (CGAS), you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not proceed with registration.</p>

                            <p><strong>2. User Eligibility</strong></p>
                            <p>You must be a current student, faculty member, or Non-Teaching Staff of the University of Science and Technology of Southern Philippines (USTP) to register. You must provide a valid, pre-authorized ID number issued by the counseling office.</p>

                            <p><strong>3. Account Security</strong></p>
                            <p>You are responsible for maintaining the confidentiality of your account credentials. You agree to notify the counseling office immediately of any unauthorized use of your account.</p>

                            <p><strong>4. Data Privacy</strong></p>
                            <p>Your personal information will be handled in accordance with the Data Privacy Act of 2012. Information provided during registration and counseling sessions will be kept confidential and used solely for counseling purposes.</p>

                            <p><strong>5. Appointment Scheduling</strong></p>
                            <p>Appointments are scheduled based on counselor availability. You agree to attend scheduled appointments or cancel them at least 24 hours in advance. Repeated no-shows may result in suspension of your account.</p>

                            <p><strong>6. Code of Conduct</strong></p>
                            <p>You agree to use the system respectfully and appropriately. Harassment, abuse, or misuse of the system may result in account termination.</p>

                            <p><strong>7. System Availability</strong></p>
                            <p>While we strive to maintain system availability, we do not guarantee uninterrupted access. The counseling office reserves the right to perform maintenance or updates that may temporarily affect system availability.</p>

                            <p><strong>8. Amendments</strong></p>
                            <p>These terms may be updated at any time. Continued use of the system after changes constitutes acceptance of the new terms.</p>

                            <p><strong>9. Contact Information</strong></p>
                            <p>For questions about these terms or the system, please contact the counseling office at counseling@ustp.edu.ph.</p>

                            <p><strong>10. Acknowledgment</strong></p>
                            <p>By clicking "I Agree" below, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</p>
                        </div>
                        <div class="flex items-center mb-4">
                            <input type="checkbox" id="agreeCheckbox" class="mr-2">
                            <label for="agreeCheckbox" class="text-sm text-gray-700">I have read and agree to the Terms and Conditions</label>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" id="disagreeBtn" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Disagree</button>
                            <button type="button" id="agreeBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled>Agree and Register</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="ms-4">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
    let selectedRole = 'student';

    function selectRole(role) {
        selectedRole = role;
        document.getElementById('roleInput').value = role;
        document.getElementById('roleSelectionModal').classList.add('hidden');
        document.getElementById('registrationForm').classList.remove('hidden');
        
        // Update role badge
        const roleText = document.getElementById('selectedRoleText');
        const roleBadge = document.getElementById('selectedRoleBadge');
        
        if (role === 'student') {
            roleText.textContent = 'Student';
            roleBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800';
            showStudentFields();
        } else if (role === 'faculty') {
            roleText.textContent = 'Faculty';
            roleBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
            showFacultyStaffFields('faculty');
        } else if (role === 'staff') {
            roleText.textContent = 'Non-Teaching Staff';
            roleBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800';
            showFacultyStaffFields('staff');
        }
    }

    function changeRole() {
        document.getElementById('roleSelectionModal').classList.remove('hidden');
        document.getElementById('registrationForm').classList.add('hidden');
    }

    function showStudentFields() {
        document.getElementById('studentFields').classList.remove('hidden');
        document.getElementById('facultyStaffFields').classList.add('hidden');
        
        // Enable student fields
        const studentFields = [
            'student_first_name', 'student_middle_name', 'student_last_name',
            'student_email', 'student_phone', 'student_id', 'course_id', 'year_level',
            'student_password', 'student_password_confirmation'
        ];
        
        studentFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                // Middle name and name extension should not be required for students
                if (fieldId !== 'student_middle_name' && fieldId !== 'student_name_extension') {
                    field.required = true;
                }
                field.disabled = false;
            }
        });
        
        // Disable faculty/staff fields
        disableFacultyStaffFields();
    }

    function showFacultyStaffFields(type) {
        document.getElementById('studentFields').classList.add('hidden');
        document.getElementById('facultyStaffFields').classList.remove('hidden');
        
        // Disable student fields
        const studentFields = [
            'student_first_name', 'student_middle_name', 'student_last_name', 'student_name_extension',
            'student_email', 'student_phone', 'student_id', 'course_id', 'year_level',
            'student_password', 'student_password_confirmation'
        ];
        
        studentFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.required = false;
                field.disabled = true;
            }
        });
        
        // Enable faculty/staff fields
        document.querySelectorAll('.fs-input').forEach(input => {
            if (input.name !== 'middle_name' && input.name !== 'name_extension') {
                input.required = true;
                input.disabled = false;
            }
        });
        
        // Show/hide appropriate ID field
        if (type === 'faculty') {
            document.getElementById('facultyIdField').classList.remove('hidden');
            document.getElementById('staffIdField').classList.add('hidden');
            const facultyIdField = document.getElementById('faculty_id');
            const staffIdField = document.getElementById('staff_id');
            if (facultyIdField) {
                facultyIdField.required = true;
                facultyIdField.disabled = false;
            }
            if (staffIdField) {
                staffIdField.required = false;
                staffIdField.disabled = true;
            }
        } else {
            document.getElementById('facultyIdField').classList.add('hidden');
            document.getElementById('staffIdField').classList.remove('hidden');
            const facultyIdField = document.getElementById('faculty_id');
            const staffIdField = document.getElementById('staff_id');
            if (facultyIdField) {
                facultyIdField.required = false;
                facultyIdField.disabled = true;
            }
            if (staffIdField) {
                staffIdField.required = true;
                staffIdField.disabled = false;
            }
        }
    }

    function disableFacultyStaffFields() {
        document.querySelectorAll('.fs-input').forEach(input => {
            input.required = false;
            input.disabled = true;
        });
        
        // Also disable ID fields
        const facultyIdField = document.getElementById('faculty_id');
        const staffIdField = document.getElementById('staff_id');
        if (facultyIdField) {
            facultyIdField.required = false;
            facultyIdField.disabled = true;
        }
        if (staffIdField) {
            staffIdField.required = false;
            staffIdField.disabled = true;
        }
    }

    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            button.querySelector("svg").innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.223-3.377M3 3l18 18M9.88 9.88a3 3 0 104.24 4.24" />';
        } else {
            input.type = 'password';
            button.querySelector("svg").innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registerForm');
        const modal = document.getElementById('termsModal');
        const agreeCheckbox = document.getElementById('agreeCheckbox');
        const agreeBtn = document.getElementById('agreeBtn');
        const disagreeBtn = document.getElementById('disagreeBtn');
        const termsContent = document.getElementById('termsContent');

        // Check if there are validation errors - if so, show the form with the appropriate role
        @if ($errors->any())
            const oldRole = '{{ old('role', 'student') }}';
            selectRole(oldRole);
            console.log('Validation errors detected, restoring role:', oldRole);
        @endif

        // Prevent form submission and show modal
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Log form data for debugging
            const formData = new FormData(form);
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Validate form before showing modal
            if (!form.checkValidity()) {
                console.log('Form validation failed');
                form.reportValidity();
                return;
            }
            
            console.log('Form validation passed, showing terms modal');
            modal.classList.remove('hidden');
            termsContent.scrollTop = 0;
            agreeCheckbox.checked = false;
            agreeBtn.disabled = true;
        });

        // Enable/disable agree button based on checkbox and scroll position
        agreeCheckbox.addEventListener('change', function() {
            if (this.checked && hasScrolledToBottom()) {
                agreeBtn.disabled = false;
            } else {
                agreeBtn.disabled = true;
            }
        });

        // Check scroll position
        termsContent.addEventListener('scroll', function() {
            if (agreeCheckbox.checked && hasScrolledToBottom()) {
                agreeBtn.disabled = false;
            } else {
                agreeBtn.disabled = true;
            }
        });

        function hasScrolledToBottom() {
            return termsContent.scrollTop + termsContent.clientHeight >= termsContent.scrollHeight - 10;
        }

        // Agree button submits the form
        agreeBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
            
            // Ensure all required fields are properly set before submission
            const role = selectedRole;
            const formData = new FormData(form);
            
            // Force validation and submit
            if (form.checkValidity()) {
                form.submit();
            } else {
                // If validation fails, show the issues
                form.reportValidity();
            }
        });

        // Disagree button closes modal
        disagreeBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    });
    </script>
</x-guest-layout>
