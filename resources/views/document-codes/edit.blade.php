<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit ') . $typeName . __(' Document Code') }}
            </h2>
            <a href="{{ route('document-codes.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ← Back to Selection
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-white">
        <div class="w-full">
            <div class="bg-white min-h-screen">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $typeName }} Document Code</h3>
                        <p class="text-sm text-gray-600">Update the document code details that appear in {{ strtolower($typeName) }} forms.</p>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach>
                            </ul>
                        </div>
                    @endif

                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                        <form method="POST" action="{{ route('document-codes.update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="type" value="{{ $type }}">

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="document_code_no" class="block text-sm font-medium text-gray-700 mb-2">
                                        Document Code No.
                                    </label>
                                    <input type="text"
                                           id="document_code_no"
                                           name="document_code_no"
                                           value="{{ old('document_code_no', $documentCode->document_code_no) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="e.g., FM-USTP-GCS-{{ $type === 'pds' ? '02' : '01' }}"
                                           required>
                                </div>

                                <div>
                                    <label for="revision_no" class="block text-sm font-medium text-gray-700 mb-2">
                                        Rev. No.
                                    </label>
                                    <input type="text"
                                           id="revision_no"
                                           name="revision_no"
                                           value="{{ old('revision_no', $documentCode->revision_no) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="e.g., 00"
                                           required>
                                </div>

                                <div>
                                    <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Effective Date
                                    </label>
                                    <input type="text"
                                           id="effective_date"
                                           name="effective_date"
                                           value="{{ old('effective_date', $documentCode->effective_date) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="e.g., 03.17.25"
                                           required>
                                </div>

                                <div>
                                    <label for="page_no" class="block text-sm font-medium text-gray-700 mb-2">
                                        Page No.
                                    </label>
                                    <input type="text"
                                           id="page_no"
                                           name="page_no"
                                           value="{{ old('page_no', $documentCode->page_no) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="e.g., {{ $type === 'pds' ? '1 of 2' : '1 of 1' }}"
                                           required>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <a href="{{ route('document-codes.index') }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
