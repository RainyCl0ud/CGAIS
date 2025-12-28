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
</x-app-layout>
