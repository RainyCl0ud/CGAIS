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

    <form method="POST" action="{{ route('register') }}">
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
                <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>
            <!-- Middle Name -->
            <div class="relative">
                <x-input-label for="middle_name" :value="__('Middle Name (optional)')" :error="$errors->has('middle_name')" :errorMessage="$errors->first('middle_name')" />
                <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name')" autocomplete="additional-name" />
                <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
            </div>
            <!-- Last Name -->
            <div class="relative">
                <x-input-label for="last_name" :value="__('Last Name')" :error="$errors->has('last_name')" :errorMessage="$errors->first('last_name')" />
                <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>
            <!-- Name Extension -->
            <div class="relative">
                <x-input-label for="name_extension" :value="__('Name Extension (optional)')" :error="$errors->has('name_extension')" :errorMessage="$errors->first('name_extension')" />
                <x-text-input id="name_extension" class="block mt-1 w-full" type="text" name="name_extension" :value="old('name_extension')" autocomplete="honorific-suffix" />
                <x-input-error :messages="$errors->get('name_extension')" class="mt-2" />
            </div>
            <!-- Email Address (span 2 columns) -->
            <div class="md:col-span-2 relative">
                <x-input-label for="email" :value="__('Email')" :error="$errors->has('email')" :errorMessage="$errors->first('email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <!-- Password -->
            <div class="relative">
                <x-input-label for="password" :value="__('Password')" :error="$errors->has('password')" :errorMessage="$errors->first('password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <!-- Confirm Password -->
            <div class="relative">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" :error="$errors->has('password_confirmation')" :errorMessage="$errors->first('password_confirmation')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <!-- Role Selection -->
            <div class="relative">
                <x-input-label for="role" :value="__('Register as')" :error="$errors->has('role')" :errorMessage="$errors->first('role')" />
            <select id="role" name="role" class="block mt-1 w-full" required onchange="toggleIdFields()">
                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty/Staff</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>
        <!-- Student ID -->
            <div class="relative" id="student_id_field">
                <x-input-label for="student_id" :value="__('Student ID')" :error="$errors->has('student_id')" :errorMessage="$errors->first('student_id')" />
                <x-text-input id="student_id" class="block mt-1 w-full" type="text" name="student_id" :value="old('student_id')" autocomplete="student-id" required />
                <div class="mt-1 text-xs text-gray-500">You must provide a valid Student ID that has been pre-authorized by a counselor.</div>
                <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
            </div>
        <!-- Faculty ID -->
            <div class="relative" id="faculty_id_field" style="display:none;">
                <x-input-label for="faculty_id" :value="__('Faculty/Staff ID')" :error="$errors->has('faculty_id')" :errorMessage="$errors->first('faculty_id')" />
                <x-text-input id="faculty_id" class="block mt-1 w-full" type="text" name="faculty_id" :value="old('faculty_id')" autocomplete="faculty-id" required />
                <div class="mt-1 text-xs text-gray-500">You must provide a valid Faculty ID that has been pre-authorized by a counselor.</div>
                <x-input-error :messages="$errors->get('faculty_id')" class="mt-2" />
            </div>
        </div>

        <script>
        function toggleIdFields() {
            var role = document.getElementById('role').value;
            var studentField = document.getElementById('student_id_field');
            var facultyField = document.getElementById('faculty_id_field');
            var studentInput = document.getElementById('student_id');
            var facultyInput = document.getElementById('faculty_id');
            
            if (role === 'student') {
                studentField.style.display = '';
                facultyField.style.display = 'none';
                studentInput.required = true;
                facultyInput.required = false;
                facultyInput.value = '';
            } else if (role === 'faculty') {
                studentField.style.display = 'none';
                facultyField.style.display = '';
                studentInput.required = false;
                facultyInput.required = true;
                studentInput.value = '';
            }
        }
        document.addEventListener('DOMContentLoaded', toggleIdFields);
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
