<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Manage Services</h2>
                    <a href="{{ route('courses.index') }}" class="text-sm text-gray-500">Back to Courses</a>
                </div>

                @if(session('status') === 'service-created')
                    <div class="rounded-md bg-green-50 p-4 border border-green-200 mt-4">
                        <p class="text-sm font-medium text-green-800">Service created.</p>
                    </div>
                @elseif(session('status') === 'service-updated')
                    <div class="rounded-md bg-green-50 p-4 border border-green-200 mt-4">
                        <p class="text-sm font-medium text-green-800">Service updated.</p>
                    </div>
                @elseif(session('status') === 'service-toggled')
                    <div class="rounded-md bg-blue-50 p-4 border border-blue-200 mt-4">
                        <p class="text-sm font-medium text-blue-800">Service status updated.</p>
                    </div>
                @endif

                <div class="mt-4">
                    <form method="post" action="{{ route('services.store') }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="name" :value="__('Service Name')" />
                                <x-text-input id="name" name="name" required class="mt-1 block w-full" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Short Description')" />
                                <textarea id="description" name="description" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>
                        </div>
                        <div class="mt-3">
                            <x-primary-button>{{ __('Add Service') }}</x-primary-button>
                        </div>
                    </form>
                </div>

                <div class="mt-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($services as $service)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $service->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($service->description, 120) }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($service->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Muted</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm">
                                            <div class="flex items-center justify-end gap-2">
                                                <button x-data @click="$dispatch('toggle-edit-{{ $service->id }}')" class="text-blue-600 hover:text-blue-900">Edit</button>

                                                <form method="post" action="{{ route('services.toggle', $service) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <button type="submit" class="text-sm px-3 py-1 rounded-md border {{ $service->is_active ? 'border-red-200 text-red-600' : 'border-green-200 text-green-700' }}">{{ $service->is_active ? 'Mute' : 'Unmute' }}</button>
                                                </form>
                                            </div>

                                            <div x-data="{ open: false }" x-on:toggle-edit-{{ $service->id }}.window="open = !open" x-show="open" x-cloak class="mt-3">
                                                <form method="post" action="{{ route('services.update', $service) }}" class="space-y-2">
                                                    @csrf
                                                    @method('put')
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                        <div>
                                                            <x-text-input name="name" value="{{ old('name', $service->name) }}" class="block w-full" required />
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <textarea name="description" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $service->description) }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                                                        <button type="button" @click="open = false" class="text-sm text-gray-600">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-sm text-gray-600">No services found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
