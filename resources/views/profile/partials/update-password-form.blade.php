<section>
    <header>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6" id="password-update-form">
        @csrf
        @method('put')
        
        <!-- Success Message -->
        @if (session('status') === 'password-updated')
            <div class="rounded-md bg-green-50 p-4 border border-green-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ __('Password updated successfully!') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if ($errors->updatePassword->any())
            <div class="rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            {{ __('There were errors with your password update:') }}
                        </h3>
                        <div class="mt-2 text-sm">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->updatePassword->all() as $error)
                                    <li class="text-red-600 font-medium">* {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" 
                         name="current_password" 
                         type="password" 
                         class="mt-1 block w-full {{ $errors->updatePassword->has('current_password') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}" 
                         autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" 
                         name="password" 
                         type="password" 
                         class="mt-1 block w-full {{ $errors->updatePassword->has('password') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}" 
                         autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" 
                         name="password_confirmation" 
                         type="password" 
                         class="mt-1 block w-full {{ $errors->updatePassword->has('password_confirmation') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}" 
                         autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Update Password') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 4000)"
                    class="text-sm font-medium text-green-600"
                >{{ __('Password updated successfully!') }}</p>
            @endif
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('password-update-form');
            const submitButton = form.querySelector('button[type="submit"]');
            
            form.addEventListener('submit', function() {
                // Disable submit button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '{{ __("Updating...") }}';
                submitButton.classList.add('opacity-75', 'cursor-not-allowed');
            });
        });
    </script>
</section>
