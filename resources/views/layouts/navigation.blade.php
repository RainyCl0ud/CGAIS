<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-40 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Left side - Mobile menu button and Page Title -->
                                    <div class="flex items-center space-x-4">
                            <!-- Mobile menu button -->
                            <button @click="window.dispatchEvent(new CustomEvent('toggle-sidebar'))" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>

            <!-- Right side - User Profile -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19A2 2 0 004 6v10a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-1.81 1.19z"></path>
                    </svg>
                    @if(auth()->user()->getUnreadNotificationsCount() > 0)
                        <span class="absolute top-1 right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                            {{ auth()->user()->getUnreadNotificationsCount() }}
                        </span>
                    @endif
                </a>

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-blue-600 font-medium text-sm">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    </span>
                                </div>
                                <span class="hidden sm:block">{{ Auth::user()->full_name }}</span>
                            </div>

                            <div class="ml-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-sm">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-sm">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>
