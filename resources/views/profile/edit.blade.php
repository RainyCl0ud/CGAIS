<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4">
            <div class="w-full max-w-4xl mx-auto">
               
                    <div class="space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Profile Information</h2>
                    @include('profile.partials.update-profile-information-form')
            </div>

                        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Update Password</h2>
                    @include('profile.partials.update-password-form')
            </div>
<!-- 
                        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Delete Account</h2>
                    @include('profile.partials.delete-user-form')
                </div> -->
            </div>
        </div>
            </div>
        </main>
    </div>
</x-app-layout>
