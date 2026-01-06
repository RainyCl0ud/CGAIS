<section>
    <header>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" id="profile-update-form">
        @csrf
        @method('patch')

        <!-- Success Message -->
        @if (session('status') === 'profile-updated')
            <div class="rounded-md bg-green-50 p-4 border border-green-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ __('Profile updated successfully!') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            {{ __('There were errors with your profile update:') }}
                        </h3>
                        <div class="mt-2 text-sm">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-red-600 font-medium">* {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" 
                             name="first_name" 
                             type="text" 
                             class="mt-1 block w-full {{ $errors->has('first_name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}" 
                             :value="old('first_name', $user->first_name)" 
                             required 
                             autofocus 
                             autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>

            <div>
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" 
                             name="last_name" 
                             type="text" 
                             class="mt-1 block w-full {{ $errors->has('last_name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}" 
                             :value="old('last_name', $user->last_name)" 
                             required 
                             autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="middle_name" :value="__('Middle Name')" />
                <x-text-input id="middle_name" 
                             name="middle_name" 
                             type="text" 
                             class="mt-1 block w-full {{ $errors->has('middle_name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}" 
                             :value="old('middle_name', $user->middle_name)" 
                             autocomplete="additional-name" />
                <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
            </div>

            <div>
                <x-input-label for="name_extension" :value="__('Name Extension')" />
                <x-text-input id="name_extension" 
                             name="name_extension" 
                             type="text" 
                             class="mt-1 block w-full {{ $errors->has('name_extension') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}" 
                             :value="old('name_extension', $user->name_extension)" 
                             autocomplete="honorific-suffix" />
                <x-input-error class="mt-2" :messages="$errors->get('name_extension')" />
            </div>
        </div>

        <div>
            <x-input-label for="phone_number" :value="__('Phone Number')" />
            <x-text-input id="phone_number" 
                         name="phone_number" 
                         type="text" 
                         class="mt-1 block w-full {{ $errors->has('phone_number') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}" 
                         :value="old('phone_number', $user->phone_number)" 
                         autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" 
                         name="email" 
                         type="email" 
                         class="mt-1 block w-full {{ $errors->has('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}" 
                         :value="old('email', $user->email)" 
                         required 
                         autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user->hasPendingEmailChange())
                <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-yellow-800">
                        {{ __('Email change pending verification') }}
                    </p>
                    <p class="text-sm text-yellow-700 mt-1">
                        {{ __('New email:') }} <strong>{{ $user->pending_email }}</strong>
                    </p>
                    <p class="text-xs text-yellow-600 mt-2">
                        {{ __('Please check your new email for the verification link. If you did not receive it, you can cancel this change and try again.') }}
                    </p>
                    <form method="post" action="{{ route('pending-email.cancel') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 underline">
                            {{ __('Cancel pending email change') }}
                        </button>
                    </form>
                </div>
            @elseif ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif

            @if (session('status') === 'email-change-pending')
                <div class="rounded-md bg-green-50 p-4 border border-green-200 mt-2">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ __('Email change pending verification. Please check your new email for the verification link.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('status') === 'email-verified-and-updated')
                <div class="rounded-md bg-green-50 p-4 border border-green-200 mt-2">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ __('Email verified and updated successfully!') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('status') === 'pending-email-cancelled')
                <div class="rounded-md bg-blue-50 p-4 border border-blue-200 mt-2">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">
                                {{ __('Pending email change cancelled.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>



        <div class="flex items-center gap-4 mt-6">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 4000)"
                    class="text-sm font-medium text-green-600"
                >{{ __('Profile updated successfully!') }}</p>
            @endif
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profile-update-form');
            const submitButton = form.querySelector('button[type="submit"]');
            
            form.addEventListener('submit', function() {
                // Disable submit button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '{{ __("Saving...") }}';
                submitButton.classList.add('opacity-75', 'cursor-not-allowed');
            });
        });
    </script>
</section>
