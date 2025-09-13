<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4">
            <div class="w-full max-w-4xl mx-auto">
                <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
                        <div>
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900">Notifications</h1>
                            <p class="text-gray-600 text-xs sm:text-sm mt-1">Stay updated with your appointment notifications</p>
                        </div>
                        @if($notifications->where('read_at', null)->count() > 0)
                            <form method="POST" action="{{ route('notifications.read-all') }}" class="mt-3 sm:mt-0">
                                @csrf
                                <button type="submit" 
                                        class="px-3 sm:px-4 py-2 bg-blue-600 text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                    Mark All as Read
                                </button>
                            </form>
                        @endif
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-3 sm:p-4 bg-green-100 border border-green-400 text-green-700 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($notifications->count() > 0)
                        <div class="space-y-3 sm:space-y-4">
                            @foreach($notifications as $notification)
                                <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 {{ $notification->read_at ? 'opacity-75' : '' }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 sm:space-x-3">
                                                @if(!$notification->read_at)
                                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                @endif
                                                <h3 class="text-sm sm:text-base font-semibold text-gray-900">
                                                    {{ $notification->title }}
                                                </h3>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $notification->getTypeBadgeClass() }}">
                                                    {{ ucfirst($notification->type) }}
                                                </span>
                                            </div>
                                            <p class="text-xs sm:text-sm text-gray-600 mt-1 sm:mt-2">
                                                {{ $notification->message }}
                                            </p>
                                            <div class="flex items-center justify-between mt-2 sm:mt-3">
                                                <span class="text-xs text-gray-500">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </span>
                                                <div class="flex space-x-2">
                                                    @if(!$notification->read_at)
                                                        <form method="POST" action="{{ route('notifications.read', $notification) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="text-xs text-blue-600 hover:text-blue-800">
                                                                Mark as Read
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                onclick="return confirm('Are you sure you want to delete this notification?')"
                                                                class="text-xs text-red-600 hover:text-red-800">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 sm:mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 sm:py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2zM4 15h6v-2H4v2zM4 11h6V9H4v2zM4 7h6V5H4v2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                            <p class="mt-1 text-sm text-gray-500">You're all caught up! No new notifications.</p>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-app-layout> 