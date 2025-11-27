<x-guest-layout>
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
                    <p>To register an account, you must provide a valid Student ID or Faculty ID that has been pre-authorized by a counselor. If you don't have an authorized ID, please contact the counseling office.</p>
                </div>
            </div>
        </div>
    </div>

    <form id="registerForm" method="POST" action="{{ route('register') }}">
        @csrf

        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 border border-red-300 text-red-700 text-sm">
                {{ $errors->first() }}
        </div>
        @endif

        <!-- Responsive Form Grid: 2 columns on md+, 1 on mobile -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- First Name -->
            <div class="relative">
                <x-input-label for="first_name" :value="__('First Name')" :error="$errors->has('first_name')" :errorMessage="$errors->first('first_name')" />
                <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" placeholder="Juan" />
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>
            <!-- Middle Name -->
            <div class="relative">
                <x-input-label for="middle_name" :value="__('Middle Name (optional)')" :error="$errors->has('middle_name')" :errorMessage="$errors->first('middle_name')" />
                <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name')" autocomplete="additional-name" placeholder="Dela Cruz" />
                <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
            </div>
            <!-- Last Name -->
            <div class="relative">
                <x-input-label for="last_name" :value="__('Last Name')" :error="$errors->has('last_name')" :errorMessage="$errors->first('last_name')" />
                <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" placeholder="Dela Cruz" />
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>
            <!-- Name Extension -->
            <div class="relative">
                <x-input-label for="name_extension" :value="__('Name Extension (optional)')" :error="$errors->has('name_extension')" :errorMessage="$errors->first('name_extension')" />
                <x-text-input id="name_extension" class="block mt-1 w-full" type="text" name="name_extension" :value="old('name_extension')" autocomplete="honorific-suffix" placeholder="Jr." />
                <x-input-error :messages="$errors->get('name_extension')" class="mt-2" />
            </div>
            <!-- Email Address (span 2 columns) -->
            <div class="md:col-span-2 relative">
                <x-input-label for="email" :value="__('Email')" :error="$errors->has('email')" :errorMessage="$errors->first('email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="juan.delacruz@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
            <!-- Password -->
            <div class="relative">
                <x-input-label for="password" :value="__('Password')" :error="$errors->has('password')" :errorMessage="$errors->first('password')" />
                <div class="relative">
                    <x-text-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" required autocomplete="new-password" placeholder="Enter your password" />
                    <button type="button" class="absolute inset-y-0 end-0 px-3 flex items-center text-gray-600" onclick="togglePasswordVisibility('password', this)" tabindex="-1">
                        <svg id="password-eye" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
            <!-- Confirm Password -->
            <div class="relative">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" :error="$errors->has('password_confirmation')" :errorMessage="$errors->first('password_confirmation')" />
                <div class="relative">
                    <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" />
                    <button type="button" class="absolute inset-y-0 end-0 px-3 flex items-center text-gray-600" onclick="togglePasswordVisibility('password_confirmation', this)" tabindex="-1">
                        <svg id="password_confirmation-eye" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <!-- Role Selection -->
            <div class="relative">
                <x-input-label for="role" :value="__('Register as')" :error="$errors->has('role')" :errorMessage="$errors->first('role')" />
            <select id="role" name="role" class="block mt-1 w-full" required onchange="toggleIdFields()">
                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>
        <!-- Student ID -->
            <div class="relative" id="student_id_field">
                <x-input-label for="student_id" :value="__('Student ID')" :error="$errors->has('student_id')" :errorMessage="$errors->first('student_id')" />
                <x-text-input id="student_id" class="block mt-1 w-full" type="text" name="student_id" :value="old('student_id')" autocomplete="student-id" required placeholder="2021000001" />
                <div class="mt-1 text-xs text-gray-500">You must provide a valid Student ID that has been pre-authorized by a counselor.</div>
                <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
            </div>
        <!-- Faculty ID -->
            <div class="relative" id="faculty_id_field" style="display:none;">
                <x-input-label for="faculty_id" :value="__('Faculty ID')" :error="$errors->has('faculty_id')" :errorMessage="$errors->first('faculty_id')" />
                <x-text-input id="faculty_id" class="block mt-1 w-full" type="text" name="faculty_id" :value="old('faculty_id')" autocomplete="faculty-id" required placeholder="FAC001" />
                <div class="mt-1 text-xs text-gray-500">You must provide a valid Faculty ID that has been pre-authorized by a counselor.</div>
                <x-input-error :messages="$errors->get('faculty_id')" class="mt-2" />
            </div>
        <!-- Staff ID -->
            <div class="relative" id="staff_id_field" style="display:none;">
                <x-input-label for="staff_id" :value="__('Staff ID')" :error="$errors->has('staff_id')" :errorMessage="$errors->first('staff_id')" />
                <x-text-input id="staff_id" class="block mt-1 w-full" type="text" name="staff_id" :value="old('staff_id')" autocomplete="staff-id" required placeholder="STAFF001" />
                <div class="mt-1 text-xs text-gray-500">You must provide a valid Staff ID that has been pre-authorized by a counselor.</div>
                <x-input-error :messages="$errors->get('staff_id')" class="mt-2" />
            </div>
        </div>

        <script>
        function toggleIdFields() {
            var role = document.getElementById('role').value;
            var studentField = document.getElementById('student_id_field');
            var facultyField = document.getElementById('faculty_id_field');
            var staffField = document.getElementById('staff_id_field');
            var studentInput = document.getElementById('student_id');
            var facultyInput = document.getElementById('faculty_id');
            var staffInput = document.getElementById('staff_id');

            if (role === 'student') {
                studentField.style.display = '';
                facultyField.style.display = 'none';
                staffField.style.display = 'none';
                studentInput.required = true;
                facultyInput.required = false;
                staffInput.required = false;
                facultyInput.value = '';
                staffInput.value = '';
            } else if (role === 'faculty') {
                studentField.style.display = 'none';
                facultyField.style.display = '';
                staffField.style.display = 'none';
                studentInput.required = false;
                facultyInput.required = true;
                staffInput.required = false;
                studentInput.value = '';
                staffInput.value = '';
            } else if (role === 'staff') {
                studentField.style.display = 'none';
                facultyField.style.display = 'none';
                staffField.style.display = '';
                studentInput.required = false;
                facultyInput.required = false;
                staffInput.required = true;
                studentInput.value = '';
                facultyInput.value = '';
            }
        }
        document.addEventListener('DOMContentLoaded', toggleIdFields);
        </script>
        <script>
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
        </script>

        <!-- Terms and Conditions Modal -->
        <div id="termsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4 text-center">Terms and Conditions</h3>
                    <div id="termsContent" class="max-h-80 overflow-y-auto text-sm text-gray-700 mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <p><strong>1. Acceptance of Terms</strong></p>
                        <p>By registering for an account on this Counseling Guidance Appointment System (CGAS), you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not proceed with registration.</p>

                        <p><strong>2. User Eligibility</strong></p>
                        <p>You must be a current student, faculty member, or staff of the University of Science and Technology of Southern Philippines (USTP) to register. You must provide a valid, pre-authorized ID number issued by the counseling office.</p>

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

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const modal = document.getElementById('termsModal');
            const agreeCheckbox = document.getElementById('agreeCheckbox');
            const agreeBtn = document.getElementById('agreeBtn');
            const disagreeBtn = document.getElementById('disagreeBtn');
            const termsContent = document.getElementById('termsContent');

            // Prevent form submission and show modal
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                modal.classList.remove('hidden');
                termsContent.scrollTop = 0; // Reset scroll position
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
                return termsContent.scrollTop + termsContent.clientHeight >= termsContent.scrollHeight - 10; // 10px tolerance
            }

            // Agree button submits the form
            agreeBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
                form.submit();
            });

            // Disagree button closes modal
            disagreeBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
        });
        </script>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
