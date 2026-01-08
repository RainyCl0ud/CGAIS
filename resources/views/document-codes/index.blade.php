<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Document Code') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-white">
        <div class="w-full">
            <div class="bg-white min-h-screen">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Document Code Information</h3>
                        <p class="text-sm text-gray-600">Update the document code details that appear in student PDS forms.</p>
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
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('document-codes.update') }}" class="mx-4 mb-8">
                        @csrf
                        @method('PUT')

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
                                       placeholder="e.g., FM-USTP-GCS-02"
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
                                       placeholder="e.g., 1 of 2"
                                       required>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
