<x-app-layout>
    <div class="flex flex-col items-center justify-start py-8 px-4 overflow-auto">
            <div class="w-full max-w-6xl mx-auto">
                <div class="bg-white/90 rounded-2xl shadow-2xl border border-blue-100 p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-blue-900">User Management</h1>
                        <a href="{{ route('users.create') }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Add New User
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Search and Filter -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <form method="GET" action="{{ route('users.index') }}" class="space-y-4 sm:space-y-0 sm:flex sm:flex-wrap sm:gap-4">
                            <div class="flex-1 min-w-0 sm:min-w-64">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Search by name, email, or ID..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="w-full sm:w-auto sm:min-w-32">
                                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Roles</option>
                                    <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="faculty" {{ request('role') === 'faculty' ? 'selected' : '' }}>Faculty</option>
                                    <option value="counselor" {{ request('role') === 'counselor' ? 'selected' : '' }}>Counselor</option>
                                    <option value="assistant" {{ request('role') === 'assistant' ? 'selected' : '' }}>Assistant</option>
                                    <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                            </div>
                            <div class="flex gap-2 sm:flex-none">
                                <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Search
                                </button>
                                <a href="{{ route('users.index') }}" class="flex-1 sm:flex-none px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                                    Clear
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Users Table -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Email</th>
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">ID Number</th>
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Created</th>

                                    </tr>
                                </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150" onclick="window.location.href='{{ route('users.show', $user) }}'">
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10">
                                                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-blue-600 font-semibold text-xs sm:text-sm">
                                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-2 sm:ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                                    <div class="text-xs text-gray-500 sm:hidden">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $user->role === 'counselor' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $user->role === 'assistant' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $user->role === 'student' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $user->role === 'faculty' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $user->role === 'staff' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 hidden md:table-cell">
                                            {{ $user->student_id ?? $user->faculty_id ?? $user->staff_id ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation()">
                                            <div class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-2">
                                                <a href="{{ route('users.edit', $user) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900 text-xs sm:text-sm">Edit</a>
                                                @if($user->id !== auth()->id())
                                                    <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                                          class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs sm:text-sm">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($users->hasPages())
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
