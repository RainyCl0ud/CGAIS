<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">Edit Authorized ID</h2>
                        <a href="{{ route('authorized-ids.show', $authorizedId) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Back</a>
                    </div>
                    
                    <form method="POST" action="{{ route('authorized-ids.update', $authorizedId) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="id_number" class="block text-sm font-medium text-gray-700">ID Number</label>
                            <input type="text" name="id_number" id="id_number" value="{{ old('id_number', $authorizedId->id_number) }}" required 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('id_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="type" id="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="student" {{ old('type', $authorizedId->type) === 'student' ? 'selected' : '' }}>Student</option>
                                <option value="faculty" {{ old('type', $authorizedId->type) === 'faculty' ? 'selected' : '' }}>Faculty</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        

                        
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('authorized-ids.show', $authorizedId) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Update ID
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
