<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Feedback Report') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('reports.export', ['type' => 'feedback', 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}" 
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
                        <h3 class="text-2xl font-bold text-gray-900">Feedback Analytics</h3>
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Feedback</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $statistics['total_feedback'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Average Rating</p>
                                    <p class="text-2xl font-bold text-green-900">{{ number_format($statistics['average_rating'], 1) }}/5</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600">This Month</p>
                                    <p class="text-2xl font-bold text-yellow-900">{{ $statistics['this_month'] }}</p>
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
                                    <p class="text-sm font-medium text-purple-600">Response Rate</p>
                                    <p class="text-2xl font-bold text-purple-900">{{ number_format($statistics['response_rate'], 1) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Distribution -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Rating Breakdown -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Rating Distribution</h4>
                            <div class="space-y-3">
                                @for($i = 5; $i >= 1; $i--)
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-600 mr-2">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</span>
                                            <div class="flex">
                                                @for($j = 1; $j <= 5; $j++)
                                                    <svg class="w-4 h-4 {{ $j <= $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                                <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $statistics['total_feedback'] > 0 ? ($ratingDistribution[$i] / $statistics['total_feedback']) * 100 : 0 }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $ratingDistribution[$i] }}</span>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Feedback Categories -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Feedback Categories</h4>
                            <div class="space-y-3">
                                @foreach($categoryDistribution as $category => $count)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $category)) }}</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $statistics['total_feedback'] > 0 ? ($count / $statistics['total_feedback']) * 100 : 0 }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Trends -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Monthly Feedback Trends</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($monthlyTrends as $month => $data)
                                <div class="bg-white p-3 rounded-lg border">
                                    <div class="text-sm font-medium text-gray-600">{{ $month }}</div>
                                    <div class="text-lg font-bold text-gray-900">{{ $data['count'] }}</div>
                                    <div class="text-xs text-gray-500">
                                        Avg: {{ number_format($data['average_rating'], 1) }}/5
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Recent Feedback Table -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Recent Feedback</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comment</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($recentFeedback as $feedback)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">
                                                                {{ strtoupper(substr($feedback->user->name, 0, 2)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $feedback->user->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $feedback->user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <svg class="w-4 h-4 {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                    <span class="ml-2 text-sm text-gray-900">{{ $feedback->rating }}/5</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ ucfirst(str_replace('_', ' ', $feedback->category)) }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ Str::limit($feedback->comment, 100) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $feedback->created_at->format('M j, Y') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                                No feedback found for this period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Feedback Insights -->
                    <div class="mt-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h4 class="text-lg font-semibold text-blue-800 mb-4">Key Insights</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h5 class="text-sm font-medium text-blue-700 mb-2">Top Strengths</h5>
                                <ul class="text-sm text-blue-600 space-y-1">
                                    @foreach($insights['strengths'] as $strength)
                                        <li class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $strength }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-blue-700 mb-2">Areas for Improvement</h5>
                                <ul class="text-sm text-blue-600 space-y-1">
                                    @foreach($insights['improvements'] as $improvement)
                                        <li class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $improvement }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
