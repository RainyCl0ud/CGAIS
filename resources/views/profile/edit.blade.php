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
