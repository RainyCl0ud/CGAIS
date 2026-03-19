<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4">
        <div class="w-full max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900">Authorized IDs Management</h1>
                        <p class="text-gray-600 text-xs sm:text-sm mt-1">Manage Student and Faculty ID numbers for registration</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                          <button id="import-btn" 
                           class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Import Student File
                        </button>
                        <a href="{{ route('authorized-ids.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New IDs
                        </a>
                        <a href="{{ route('authorized-ids.export') }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export CSV
                        </a>
                    </div>
                </div>
            </div>



            <!-- Filters and Search -->
            <div class="bg-white/80 rounded-lg shadow-lg border border-blue-100 p-4 sm:p-6 backdrop-blur mb-6">
                <form method="GET" action="{{ route('authorized-ids.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search ID Number</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter ID number...">
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Types</option>
                            <option value="student" {{ request('type') === 'student' ? 'selected' : '' }}>Student</option>
                            <option value="faculty" {{ request('type') === 'faculty' ? 'selected' : '' }}>Faculty</option>
                            <option value="staff" {{ request('type') === 'staff' ? 'selected' : '' }}>Non-Teaching Staff</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Authorized IDs Table -->
            <div class="bg-white/80 rounded-lg shadow-lg border border-blue-100 backdrop-blur">
                <div class="px-4 py-3 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Authorized IDs</h3>
                </div>
                
                @if($authorizedIds->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Used By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($authorizedIds as $authorizedId)
                                    <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150" onclick="window.location.href='{{ route('authorized-ids.show', $authorizedId) }}'">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="selected_ids[]" value="{{ $authorizedId->id }}" 
                                                   class="id-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $authorizedId->id_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $authorizedId->type === 'student' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ $authorizedId->type_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $authorizedId->status_badge_class }}">
                                                {{ $authorizedId->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($authorizedId->is_used)
                                                <div class="text-sm text-gray-900">{{ $authorizedId->usedBy?->full_name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $authorizedId->used_at?->format('M d, Y') ?? 'N/A' }}</div>
                                            @else
                                                <span class="text-sm text-gray-500">Not used</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $authorizedId->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation()">
                                            @if(!$authorizedId->is_used)
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('authorized-ids.edit', $authorizedId) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    <form method="POST" action="{{ route('authorized-ids.destroy', $authorizedId) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                                onclick="return confirm('Are you sure you want to delete this ID?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Bulk Actions -->
                    <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button id="bulk-delete-btn" disabled
                                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Delete Selected
                                </button>
                                <span id="selected-count" class="text-sm text-gray-600">0 IDs selected</span>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="flex items-center space-x-2">
                                {{ $authorizedIds->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="px-4 py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No authorized IDs found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding your first authorized ID numbers.</p>
                        <div class="mt-6">
                            <a href="{{ route('authorized-ids.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Authorized IDs
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bulk Delete Form -->
    <form id="bulk-delete-form" method="POST" action="{{ route('authorized-ids.bulk-destroy') }}" class="hidden">
        @csrf
        <input type="hidden" name="ids" id="bulk-delete-ids">
    </form>

    <!-- Import Modal -->
    <div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Import Authorized IDs (CSV)</h3>
                
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                    <h4 class="font-semibold text-blue-900 mb-2">📋 File Format Requirements:</h4>
                    <ul class="text-sm text-blue-800 space-y-1 ml-4">
                        <li>• CSV file only (max 2MB)</li>
                        <li>• <strong>Required columns (first row):</strong> <code>id_number,type</code></li>
                        <li>• <code>id_number</code>: Student/Faculty/Staff ID (3-50 chars)</li>
                        <li>• <code>type</code>: <code>student</code>, <code>faculty</code>, or <code>staff</code></li>
                    </ul>
                    <div class="mt-3 p-3 bg-gray-100 rounded border text-xs">
                        <strong>Sample CSV:</strong><br>
                        <code>id_number,type<br>20240001,student<br>20240002,student<br>F001,faculty</code>
                    </div>
                </div>

                <form id="import-form" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">Choose CSV File</label>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv" required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">Only CSV files are supported.</p>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" id="cancel-import" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" id="import-submit" disabled class="px-6 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 font-semibold disabled:opacity-50">
                            Import CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Results Toast -->
    <div id="import-toast" class="fixed top-4 right-4 z-50 hidden p-4 rounded-lg shadow-lg max-w-sm w-full mx-4">
        <div id="toast-content"></div>
    </div>

    <script>
        // Select all functionality
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.id-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Individual checkbox change
        document.querySelectorAll('.id-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.id-checkbox:checked');
            const count = checkboxes.length;
            const selectedCount = document.getElementById('selected-count');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            
            selectedCount.textContent = `${count} ID${count !== 1 ? 's' : ''} selected`;
            bulkDeleteBtn.disabled = count === 0;
        }

        // Bulk delete functionality
        document.getElementById('bulk-delete-btn').addEventListener('click', function(e) {
            e.preventDefault();
            
            const checkboxes = document.querySelectorAll('.id-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            
            if (ids.length === 0) {
                alert('Please select at least one ID to delete.');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${ids.length} selected ID(s)?`)) {
                document.getElementById('bulk-delete-ids').value = JSON.stringify(ids);
                document.getElementById('bulk-delete-form').submit();
            }
        });

        // Import functionality
        const importBtn = document.getElementById('import-btn');
        const importModal = document.getElementById('importModal');
        const cancelImport = document.getElementById('cancel-import');
        const importForm = document.getElementById('import-form');
        const csvFile = document.getElementById('csv_file');
        const importSubmit = document.getElementById('import-submit');
        const toast = document.getElementById('import-toast');
        const toastContent = document.getElementById('toast-content');

        // Open modal
        importBtn.addEventListener('click', () => {
            importModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });

        // Close modal
        cancelImport.addEventListener('click', closeImportModal);
        importModal.addEventListener('click', (e) => {
            if (e.target === importModal) closeImportModal();
        });

        function closeImportModal() {
            importModal.classList.add('hidden');
            document.body.style.overflow = '';
            importForm.reset();
            importSubmit.disabled = true;
        }

        // Enable/disable submit button
        csvFile.addEventListener('change', () => {
            importSubmit.disabled = !csvFile.files[0];
        });

        // Handle form submit
        importForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(importForm);
            importSubmit.disabled = true;
            importSubmit.textContent = 'Importing...';

            try {
                const response = await fetch('{{ route("authorized-ids.import") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showToast('success', result.message);
                    closeImportModal();
                    // Reload table data
                    window.location.reload();
                } else {
                    showToast('error', result.message || 'Import failed', result.errors);
                }
            } catch (error) {
                showToast('error', 'Network error. Please try again.');
            } finally {
                importSubmit.disabled = false;
                importSubmit.textContent = 'Import CSV';
            }
        });

        function showToast(type, message, errors = null) {
            toastContent.innerHTML = `
                <div class="flex">
                    <div class="${type === 'success' ? 'text-green-600' : 'text-red-600'} mr-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            ${type === 'success' ? 
                                '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>' : 
                                '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-2 0v4a1 1 0 102 0V5z"/>'}
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium">${message}</p>
                        ${errors && Array.isArray(errors) && errors.length ? 
                            `<ul class="mt-1 text-xs max-h-32 overflow-y-auto">${errors.map(err => `<li>• ${err}</li>`).join('')}</ul>` : ''}
                    </div>
                    <button onclick="this.parentElement.parentElement.parentElement.classList.add('hidden')" 
                            class="ml-2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                        </svg>
                    </button>
                </div>
            `;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 10000);
        }
    </script>
</x-app-layout>
