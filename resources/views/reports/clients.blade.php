<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Client Report') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('reports.export', ['type' => 'clients', 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </a>
                <a href="{{ route('reports.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Reports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Report Header -->
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Client Engagement Analytics</h3>
                        <p class="text-gray-600 mt-1">
                            Report period: {{ \Carbon\Carbon::parse($dateFrom)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M j, Y') }}
                        </p>
                    </div>

                    <!-- Key Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Clients</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $statistics['total_clients'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Active Clients</p>
                                    <p class="text-2xl font-bold text-green-900">{{ $statistics['active_clients'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600">Avg Appointments</p>
                                    <p class="text-2xl font-bold text-yellow-900">{{ number_format($statistics['avg_appointments_per_client'], 1) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-600">Completion Rate</p>
                                    <p class="text-2xl font-bold text-purple-900">{{ number_format($statistics['completion_rate'], 1) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Client Demographics -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Course Distribution -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Course Distribution</h4>
                            <div class="space-y-3">
                                @foreach($courseDistribution as $course => $count)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">{{ $course ?: 'Not Specified' }}</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $statistics['total_clients'] > 0 ? ($count / $statistics['total_clients']) * 100 : 0 }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Year Level Distribution -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Year Level Distribution</h4>
                            <div class="space-y-3">
                                @foreach($yearLevelDistribution as $year => $count)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">{{ $year ?: 'Not Specified' }}</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $statistics['total_clients'] > 0 ? ($count / $statistics['total_clients']) * 100 : 0 }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Client Engagement Metrics -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Client Engagement Metrics</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-white p-3 rounded-lg border">
                                <div class="text-sm font-medium text-gray-600">New Clients</div>
                                <div class="text-lg font-bold text-gray-900">{{ $statistics['new_clients'] }}</div>
                                <div class="text-xs text-gray-500">This period</div>
                            </div>
                            <div class="bg-white p-3 rounded-lg border">
                                <div class="text-sm font-medium text-gray-600">Returning Clients</div>
                                <div class="text-lg font-bold text-gray-900">{{ $statistics['returning_clients'] }}</div>
                                <div class="text-xs text-gray-500">This period</div>
                            </div>
                            <div class="bg-white p-3 rounded-lg border">
                                <div class="text-sm font-medium text-gray-600">High Engagement</div>
                                <div class="text-lg font-bold text-gray-900">{{ $statistics['high_engagement_clients'] }}</div>
                                <div class="text-xs text-gray-500">3+ appointments</div>
                            </div>
                            <div class="bg-white p-3 rounded-lg border">
                                <div class="text-sm font-medium text-gray-600">PDS Completion</div>
                                <div class="text-lg font-bold text-gray-900">{{ $statistics['pds_completion_rate'] }}%</div>
                                <div class="text-xs text-gray-500">Complete PDS</div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Clients Table -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Top Clients by Engagement</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course & Year</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Appointments</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Visit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PDS Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($topClients as $client)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">
                                                                {{ strtoupper(substr($client->name, 0, 2)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $client->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $client->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $client->course ?: 'Not specified' }} - {{ $client->year_level ?: 'Not specified' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $client->appointments_count }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $client->completed_appointments_count }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $client->last_appointment ? $client->last_appointment->diffForHumans() : 'Never' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($client->personalDataSheet && $client->personalDataSheet->isComplete())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Complete
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Incomplete
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                                No clients found for this period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
