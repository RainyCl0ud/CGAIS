<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Activity Log Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('activity-logs.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Logs
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Activity Log Header -->
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-lg">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $activityLog->getActionIcon() !!}
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $activityLog->getActionLabel() }}</h3>
                                    <p class="text-gray-600">{{ $activityLog->description }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $activityLog->getActionColor() }}">
                                    {{ $activityLog->action }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Details Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- User Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">User Information</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-lg font-medium text-gray-700">
                                                {{ strtoupper(substr($activityLog->user->name ?? 'Unknown', 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-lg font-medium text-gray-900">
                                            {{ $activityLog->user->name ?? 'Unknown User' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $activityLog->user->email ?? 'No email' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Role: {{ ucfirst($activityLog->user->role ?? 'Unknown') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Activity Information</h4>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Action:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $activityLog->action }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Time:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $activityLog->created_at->format('F j, Y \a\t g:i A') }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">IP Address:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $activityLog->ip_address ?? 'Not recorded' }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">User Agent:</span>
                                    <span class="ml-2 text-sm text-gray-900 break-all">{{ Str::limit($activityLog->user_agent ?? 'Not recorded', 100) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Model Information -->
                    @if($activityLog->model_type && $activityLog->model_id)
                        <div class="mt-6 bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Related Model</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Model Type:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $activityLog->getModelLabel() }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Model ID:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $activityLog->model_id }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Changes Information -->
                    @if($activityLog->old_values || $activityLog->new_values)
                        <div class="mt-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Changes Made</h4>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                @if($activityLog->old_values)
                                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                        <h5 class="text-md font-semibold text-red-800 mb-3">Previous Values</h5>
                                        <div class="space-y-2">
                                            @foreach($activityLog->old_values as $field => $value)
                                                <div>
                                                    <span class="text-sm font-medium text-red-600">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                    <span class="ml-2 text-sm text-red-800">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($activityLog->new_values)
                                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                        <h5 class="text-md font-semibold text-green-800 mb-3">New Values</h5>
                                        <div class="space-y-2">
                                            @foreach($activityLog->new_values as $field => $value)
                                                <div>
                                                    <span class="text-sm font-medium text-green-600">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                    <span class="ml-2 text-sm text-green-800">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Additional Context -->
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Additional Context</h4>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Description:</span>
                                <p class="mt-1 text-sm text-gray-900">{{ $activityLog->description }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Created:</span>
                                <span class="ml-2 text-sm text-gray-900">{{ $activityLog->created_at->diffForHumans() }}</span>
                            </div>
                            @if($activityLog->updated_at != $activityLog->created_at)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Last Updated:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $activityLog->updated_at->diffForHumans() }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('activity-logs.index') }}" 
                           class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Back to Activity Logs
                        </a>
                        @if($activityLog->model_type && $activityLog->model_id)
                            <a href="#" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                View Related Record
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
