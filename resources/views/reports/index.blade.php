<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4">
            <div class="w-full max-w-7xl mx-auto">
                <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
                        <div>
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900">Analytics & Reports</h1>
                            <p class="text-gray-600 text-xs sm:text-sm mt-1">Comprehensive insights into your counseling practice</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 mt-3 sm:mt-0">
                            <a href="{{ route('reports.export', ['type' => 'appointments', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" 
                               class="px-4 sm:px-6 py-2 sm:py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors text-sm text-center">
                                📊 Export Reports
                            </a>
                        </div>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="bg-gray-50 p-4 sm:p-6 rounded-lg mb-6">
                        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" 
                                       class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" 
                                       class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-900 text-white font-semibold rounded-lg hover:bg-blue-800 transition-colors">
                                🔍 Update Reports
                            </button>
                        </form>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
                        <div class="bg-blue-50 p-3 sm:p-4 rounded-lg border border-blue-200">
                            <div class="text-xs sm:text-sm font-medium text-gray-600">Total Appointments</div>
                            <div class="text-lg sm:text-xl font-semibold text-blue-900">{{ $appointmentStats['total'] }}</div>
                        </div>
                        <div class="bg-green-50 p-3 sm:p-4 rounded-lg border border-green-200">
                            <div class="text-xs sm:text-sm font-medium text-gray-600">Completed</div>
                            <div class="text-lg sm:text-xl font-semibold text-green-900">{{ $appointmentStats['completed'] }}</div>
                        </div>
                        <div class="bg-purple-50 p-3 sm:p-4 rounded-lg border border-purple-200">
                            <div class="text-xs sm:text-sm font-medium text-gray-600">Total Clients</div>
                            <div class="text-lg sm:text-xl font-semibold text-purple-900">{{ $clientStats['total_clients'] }}</div>
                        </div>
                        <!-- Feedback quick stat removed for counselors/assistants as requested -->
                    </div>

                    <!-- Detailed Statistics -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Appointment Status Distribution -->
                        <div class="bg-white p-4 sm:p-6 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Status</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Completed</span>
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $appointmentStats['total'] > 0 ? ($appointmentStats['completed'] / $appointmentStats['total']) * 100 : 0 }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium">{{ $appointmentStats['completed'] }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Cancelled</span>
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ $appointmentStats['total'] > 0 ? ($appointmentStats['cancelled'] / $appointmentStats['total']) * 100 : 0 }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium">{{ $appointmentStats['cancelled'] }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">No Show</span>
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $appointmentStats['total'] > 0 ? ($appointmentStats['no_show'] / $appointmentStats['total']) * 100 : 0 }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium">{{ $appointmentStats['no_show'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Appointment Types -->
                        <div class="bg-white p-4 sm:p-6 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Types</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Regular</span>
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $appointmentStats['total'] > 0 ? ($appointmentStats['regular'] / $appointmentStats['total']) * 100 : 0 }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium">{{ $appointmentStats['regular'] }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Urgent</span>
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ $appointmentStats['total'] > 0 ? ($appointmentStats['urgent'] / $appointmentStats['total']) * 100 : 0 }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium">{{ $appointmentStats['urgent'] }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Follow Up</span>
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $appointmentStats['total'] > 0 ? ($appointmentStats['follow_up'] / $appointmentStats['total']) * 100 : 0 }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium">{{ $appointmentStats['follow_up'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Client Statistics -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white p-4 sm:p-6 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Client Overview</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">Total Clients</span>
                                    <span class="text-lg font-semibold text-blue-900">{{ $clientStats['total_clients'] }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">New Clients</span>
                                    <span class="text-lg font-semibold text-green-900">{{ $clientStats['new_clients'] }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">Returning Clients</span>
                                    <span class="text-lg font-semibold text-purple-900">{{ $clientStats['returning_clients'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Feedback analytics removed from reports view for counselor/assistant dashboards -->
                    </div>

                    <!-- Category Distribution -->
                    @if(count($categoryDistribution) > 0)
                    <div class="bg-white p-4 sm:p-6 rounded-lg border border-gray-200 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Counseling Categories</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($categoryDistribution as $category)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">
                                        @switch($category->counseling_category)
                                            @case('conduct_intake_interview')
                                                Intake Interview
                                                @break
                                            @case('information_services')
                                                Information Services
                                                @break
                                            @case('internal_referral_services')
                                                Internal Referral
                                                @break
                                            @case('counseling_services')
                                                Counseling Services
                                                @break
                                            @case('conduct_exit_interview')
                                                Exit Interview
                                                @break
                                            @case('consultation')
                                                Consultation
                                                @break
                                            @default
                                                {{ ucfirst(str_replace('_', ' ', $category->counseling_category)) }}
                                        @endswitch
                                    </span>
                                    <span class="text-lg font-semibold text-blue-900">{{ $category->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Report Navigation -->
                    <div class="bg-gray-50 p-4 sm:p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed Reports</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <a href="{{ route('reports.appointments', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" 
                               class="p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-semibold text-gray-900">Appointment Report</h4>
                                        <p class="text-xs text-gray-500">Detailed appointment analysis</p>
                                    </div>
                                </div>
                            </a>
                            
                            <a href="{{ route('reports.clients', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" 
                               class="p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-semibold text-gray-900">Client Report</h4>
                                        <p class="text-xs text-gray-500">Client engagement analysis</p>
                                    </div>
                                </div>
                            </a>
                            
                            <!-- Feedback Report link removed from Detailed Reports -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
