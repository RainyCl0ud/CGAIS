<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4">
        <div class="w-full max-w-4xl mx-auto">
            <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900">Notification Details</h1>
                            <p class="text-gray-600 text-xs sm:text-sm mt-1">View notification information</p>
                        </div>
                        <a href="{{ route('notifications.index') }}" 
                           class="px-3 sm:px-4 py-2 bg-gray-600 text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                            Back to Notifications
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-3 sm:p-4 bg-green-100 border border-green-400 text-green-700 rounded text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">{{ $notification->title }}</h2>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $notification->getTypeBadgeClass() }}">
                                {{ ucfirst($notification->type) }}
                            </span>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-sm sm:text-base text-gray-700">{{ $notification->message }}</p>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-600">Status:</span>
                                    <span class="ml-2 {{ $notification->read_at ? 'text-green-600' : 'text-blue-600' }}">
                                        {{ $notification->read_at ? 'Read' : 'Unread' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-600">Created:</span>
                                    <span class="ml-2 text-gray-700">{{ $notification->created_at->format('M d, Y \a\t g:i A') }}</span>
                                </div>
                                @if($notification->read_at)
                                <div>
                                    <span class="font-medium text-gray-600">Read at:</span>
                                    <span class="ml-2 text-gray-700">{{ $notification->read_at->format('M d, Y \a\t g:i A') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4 flex space-x-3">
                            @if(!$notification->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                        Mark as Read
                                    </button>
                                </form>
                            @endif
                            
                            <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to delete this notification?')"
                                        class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
