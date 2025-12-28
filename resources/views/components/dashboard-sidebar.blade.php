<div x-data="{ open: false }"
     @toggle-sidebar.window="open = !open"
     @keydown.escape.window="open = false"
     class="h-full w-64 bg-white shadow-lg z-50 border-r border-gray-200 overflow-y-auto scrollbar-hide transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:flex-shrink-0 pointer-events-auto" 
     :class="{'translate-x-0': open, '-translate-x-full': !open}"
     style="scroll-behavior: smooth;">
    <!-- Mobile Overlay (does not cover the sidebar area) -->
    <div x-show="open"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-0 right-0 bottom-0 bg-gray-600 bg-opacity-75 lg:hidden z-30"
         :class="open ? 'left-64' : 'left-0'"
         @click="open = false"></div>
    
    <!-- Logo and Title -->
    <div class="text-center py-6 border-b border-gray-500 relative">
        <!-- Mobile Close Button -->
        <button @click="open = false" class="lg:hidden absolute top-4 right-4 text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <div class="mx-auto mb-4 flex items-center justify-center">
            <x-application-logo class="h-10 w-auto" />
        </div>
        <h2 class="text-xs font-medium text-gray-600 leading-tight px-4">
            Cloud-Based Guidance and<br />
                   Counseling System
        </h2>
        <div class="w-12 h-0.5 bg-gradient-to-r from-blue-500 to-yellow-500 mx-auto mt-3"></div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="mt-6 px-4 space-y-1 pb-4">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : '' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="font-medium text-sm">Dashboard</span>
        </a>
        
        @if(auth()->user()->isStudent())
            <!-- Student Navigation -->
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">Appointments</h3>
                <a href="{{ route('student.appointments.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('student.appointments.index') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium text-sm">My Appointments</span>
                </a>
                <a href="{{ route('student.appointments.session-history') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('student.appointments.session-history') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium text-sm">Session History</span>
                </a>
            </div>
            
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">Personal</h3>
                <a href="{{ route('pds.show') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('pds.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium text-sm">Personal Data Sheet</span>
                </a>
                <!-- <a href="{{ route('feedback.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('feedback.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span class="font-medium text-sm">Feedback</span>
                </a> -->
                <!-- <a href="{{ route('notifications.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('notifications.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2zM4 15h6v-2H4v2zM4 11h6V9H4v2zM4 7h6V5H4v2z"></path>
                    </svg>
                    <span class="font-medium text-sm">Notifications</span>
                </a> -->
            </div>
            
        @elseif(auth()->user()->isCounselor())
            <!-- Counselor Navigation (Full Privileges) -->
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">Appointments</h3>
                <a href="{{ route('appointments.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('appointments.index') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium text-sm">Manage Appointments</span>
                </a>
                <a href="{{ route('appointments.session-history') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('appointments.session-history') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium text-sm">Session History</span>
                </a>
            </div>

            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">Management</h3>
                <a href="{{ route('schedules.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('schedules.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium text-sm">Manage Schedule</span>
                </a>
                <a href="{{ route('users.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('users.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="font-medium text-sm">User Management</span>
                </a>
                <a href="{{ route('students.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('students.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="font-medium text-sm">Student Directory</span>
                </a>
                <a href="{{ route('courses.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('courses.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span class="font-medium text-sm">Manage Courses</span>
                </a>
                <a href="{{ route('services.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('services.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <span class="font-medium text-sm">Manage Services</span>
                </a>
            </div>
            
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">Analytics & Reports</h3>
                <!-- Reports link hidden for counselor/assistant as requested -->
                <a href="{{ route('activity-logs.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('activity-logs.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium text-sm">Activity Logs</span>
                </a>
            </div>
            
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">System</h3>
                <a href="{{ route('authorized-ids.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('authorized-ids.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="font-medium text-sm">Authorized IDs</span>
                </a>
                <a href="{{ route('system.backup') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('system.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span class="font-medium text-sm">System Backup</span>
                </a>
            </div>
            
        @elseif(auth()->user()->isAssistant())
            <!-- Assistant Navigation (Same privileges as Counselor) -->
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">Appointments</h3>
                <a href="{{ route('appointments.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('appointments.index') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium text-sm">Manage Appointments</span>
                </a>
                <a href="{{ route('appointments.session-history') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('appointments.session-history') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium text-sm">Session History</span>
                </a>
            </div>
            
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">Management</h3>
                <a href="{{ route('schedules.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('schedules.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium text-sm">Manage Schedule</span>
                </a>
                <a href="{{ route('users.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('users.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="font-medium text-sm">User Management</span>
                </a>
                <a href="{{ route('students.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('students.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="font-medium text-sm">Student Directory</span>
                </a>
            </div>
            
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">Analytics & Reports</h3>
                <!-- Reports link hidden for counselor/assistant as requested -->
                <a href="{{ route('activity-logs.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('activity-logs.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium text-sm">Activity Logs</span>
                </a>
            </div>
            
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">System</h3>
                <a href="{{ route('authorized-ids.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('authorized-ids.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="font-medium text-sm">Authorized IDs</span>
                </a>
            </div>
            
        @else
            <!-- Faculty and Staff Navigation same as Student -->
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">Appointments</h3>
                <a href="{{ route('student.appointments.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('student.appointments.index') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium text-sm">My Appointments</span>
                </a>
                <a href="{{ route('student.appointments.session-history') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('student.appointments.session-history') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium text-sm">Session History</span>
                </a>
            </div>

            {{-- <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">Personal</h3>
                {{-- <!-- Personal Data Sheet link removed for Staff and Faculty as requested -->
                <!-- <a href="{{ route('pds.show') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('pds.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium text-sm">Personal Data Sheet</span>
                </a> --> --}}
                {{-- <!-- <a href="{{ route('feedback.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('feedback.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03 8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span class="font-medium text-sm">Feedback</span>
                </a> --> --}}
                {{-- <!-- <a href="{{ route('notifications.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('notifications.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2zM4 15h6v-2H4v2zM4 11h6V9H4v2zM4 7h6V5H4v2z"></path>
                    </svg>
                    <span class="font-medium text-sm">Notifications</span>
                </a> -->
            </div> --}}
        @endif

    </nav>
</div> 