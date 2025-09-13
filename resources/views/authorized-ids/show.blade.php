<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">Authorized ID Details</h2>
                        <div class="space-x-2">
                            @if(!$authorizedId->is_used)
                                <a href="{{ route('authorized-ids.edit', $authorizedId) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Edit</a>
                            @endif
                            <a href="{{ route('authorized-ids.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Back</a>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Basic Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">ID Number</label>
                                    <p class="text-lg font-semibold">{{ $authorizedId->id_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Type</label>
                                    <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full {{ $authorizedId->type === 'student' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $authorizedId->type_label }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Status</label>
                                    <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full {{ $authorizedId->status_badge_class }}">
                                        {{ $authorizedId->status_label }}
                                    </span>
                                </div>
                                @if($authorizedId->notes)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Notes</label>
                                        <p>{{ $authorizedId->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Registration Details</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Registered By</label>
                                    <p>{{ $authorizedId->registeredBy?->full_name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500">{{ $authorizedId->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                @if($authorizedId->is_used)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Used By</label>
                                        <p>{{ $authorizedId->usedBy?->full_name ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-500">{{ $authorizedId->used_at?->format('M d, Y H:i') ?? 'N/A' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if(!$authorizedId->is_used)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <form method="POST" action="{{ route('authorized-ids.destroy', $authorizedId) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" 
                                        onclick="return confirm('Are you sure you want to delete this ID?')">
                                    Delete ID
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
