<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                @include('profile.partials.update-profile-information-form', ['user' => $user])
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Update Password</h2>
                @include('profile.partials.update-password-form')
            </div>

            @if(auth()->user() && auth()->user()->isCounselor())
            <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Create New Counselor</h2>

                @if(session('status') === 'counselor-created')
                    <div class="rounded-md bg-green-50 p-4 border border-green-200 mb-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">Counselor account created successfully. The new counselor will receive an email with login details.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="post" action="{{ route('profile.create-counselor') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="first_name" :value="__('First Name')" />
                            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                        </div>

                        <div>
                            <x-input-label for="last_name" :value="__('Last Name')" />
                            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="middle_name" :value="__('Middle Name')" />
                            <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" />
                            <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
                        </div>

                        <div>
                            <x-input-label for="name_extension" :value="__('Name Extension')" />
                            <x-text-input id="name_extension" name="name_extension" type="text" class="mt-1 block w-full" />
                            <x-input-error class="mt-2" :messages="$errors->get('name_extension')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <x-input-label for="phone_number" :value="__('Phone Number')" />
                        <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Create Counselor') }}</x-primary-button>
                    </div>
                </form>
            </div>

            @if(auth()->user() && auth()->user()->isCounselor())
            <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Counselor Availability</h2>

                @if(session('status') === 'counselor-deactivated')
                    <div class="rounded-md bg-green-50 p-4 border border-green-200 mb-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">You have been marked as inactive. Students will no longer see you as available for appointments.</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('status') === 'counselor-activated')
                    <div class="rounded-md bg-green-50 p-4 border border-green-200 mb-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">You have been reactivated. Students can now book appointments with you.</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->has('counselor_toggle'))
                    <div class="rounded-md bg-red-50 p-4 border border-red-200 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    {{ __('There were errors with your availability update:') }}
                                </h3>
                                <div class="mt-2 text-sm">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->getBag('counselor_toggle')->all() as $error)
                                            <li class="text-red-600 font-medium">* {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    @if(auth()->user()->isAvailable())
                    <p class="text-sm text-gray-600 mb-4">
                        Mark yourself as unavailable to prevent new appointment bookings. This action is permanent until reactivated, and students will see you as unavailable.
                    </p>
                    @else
                    <p class="text-sm text-gray-600 mb-4">
                        Mark yourself as available for new appointment bookings. This action is permanent until reactivated, and students will see you as available.
                    </p>
                    @endif

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Current Availability Status</h3>
                            <p class="text-sm text-gray-600">
                                You are currently <strong>{{ auth()->user()->isAvailable() ? 'Available' : 'Unavailable' }}</strong> for appointments
                            </p>
                        </div>

                        @if(auth()->user()->isAvailable())
                            <!-- Mark as Unavailable - Requires Confirmation -->
                            <button type="button" onclick="openUnavailableModal()" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                Mark as Unavailable
                            </button>
                        @else
                            <!-- Reactivate - Requires Confirmation -->
                            <button type="button" onclick="openReactivateModal()" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                Reactivate
                            </button>   
                        @endif
                    </div>


                </div>

                <!-- Confirmation Modal for Marking as Unavailable -->
                <div id="unavailableModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Unavailability</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                This action will mark you as unavailable for new appointments. Students will not be able to book appointments with you until you reactivate. This is a permanent action until manually reactivated.
                            </p>

                            <form method="post" action="{{ route('profile.toggle-active') }}" id="unavailableForm">
                                @csrf

                                <!-- Hidden confirmation fields -->
                                <input type="hidden" name="confirm_intent" value="1">
                                <input type="hidden" name="final_confirm" value="1">

                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="closeUnavailableModal()"
                                            class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        Mark as Unavailable
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Modal for Reactivating -->
                <div id="reactivateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Reactivation</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                This action will mark you as available for new appointments. Students will be able to book appointments with you again.
                            </p>

                            <form method="post" action="{{ route('profile.toggle-active') }}" id="reactivateForm">
                                @csrf

                                <!-- Hidden confirmation fields -->
                                <input type="hidden" name="confirm_intent" value="1">
                                <input type="hidden" name="final_confirm" value="1">

                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="closeReactivateModal()"
                                            class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        Reactivate
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(auth()->user() && auth()->user()->isCounselor())
            <div class="bg-white rounded-lg border border-red-200 p-4 sm:p-6">
                @if(session('status') === 'counselor-account-deactivated')
                    <div class="rounded-md bg-green-50 p-4 border border-green-200 mb-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">Your counselor account has been successfully deactivated. You will be logged out shortly.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <h2 class="text-lg sm:text-xl font-semibold text-red-900 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Danger Zone
                </h2>

                <div class="border border-red-300 rounded-lg p-4 bg-red-50">
                    <h3 class="text-sm font-medium text-red-800 mb-2">Deactivate Account</h3>
                    <p class="text-sm text-red-700 mb-4">
                        Deactivating your account will disable your access to the system and prevent future logins. Existing records will be preserved but your account will be marked as inactive. This action cannot be undone.
                    </p>

                    <button type="button" onclick="openDeactivateModal()" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Deactivate Account
                    </button>
                </div>

                <!-- Confirmation Modal for Deactivating Account -->
                <div id="deactivateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Account Deactivation</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                This action will deactivate your counselor account. You will lose access to the system and will not be able to log in. Existing records will be preserved, but your account will be marked as inactive. This action is permanent and cannot be undone.
                            </p>

                            <form method="post" action="{{ route('profile.deactivate-counselor') }}" id="deactivateForm">
                                @csrf

                                <div class="mb-4">
                                    <label for="confirm_text" class="block text-sm font-medium text-gray-700 mb-2">
                                        Type "DEACTIVATE" to confirm:
                                    </label>
                                    <input type="text" id="confirm_text" name="confirm_text"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                           placeholder="DEACTIVATE" required>
                                </div>

                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="closeDeactivateModal()"
                                            class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        Cancel
                                    </button>
                                    <button type="submit" id="deactivateBtn"
                                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Deactivate Account
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @endif

            <!-- Delete account (kept commented out) -->
            {{--
            <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Delete Account</h2>
                @include('profile.partials.delete-user-form')
            </div>
            --}}
        </div>
    </div>

    <script>
        function openUnavailableModal() {
            document.getElementById('unavailableModal').classList.remove('hidden');
        }

        function closeUnavailableModal() {
            document.getElementById('unavailableModal').classList.add('hidden');
            // Reset form
            document.getElementById('unavailableForm').reset();
        }

        function openReactivateModal() {
            document.getElementById('reactivateModal').classList.remove('hidden');
        }

        function closeReactivateModal() {
            document.getElementById('reactivateModal').classList.add('hidden');
            // Reset form
            document.getElementById('reactivateForm').reset();
        }

        function openDeactivateModal() {
            document.getElementById('deactivateModal').classList.remove('hidden');
        }

        function closeDeactivateModal() {
            document.getElementById('deactivateModal').classList.add('hidden');
            // Reset form
            document.getElementById('deactivateForm').reset();
            // Reset button state
            document.getElementById('deactivateBtn').disabled = true;
        }

        // Enable/disable deactivate button based on input
        document.getElementById('confirm_text').addEventListener('input', function() {
            const btn = document.getElementById('deactivateBtn');
            btn.disabled = this.value.toUpperCase() !== 'DEACTIVATE';
        });

        // Close modals when clicking outside
        document.getElementById('unavailableModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUnavailableModal();
            }
        });

        document.getElementById('reactivateModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReactivateModal();
            }
        });

        document.getElementById('deactivateModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeactivateModal();
            }
        });
    </script>
</x-app-layout>
