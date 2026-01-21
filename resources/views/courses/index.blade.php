<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4">
        <div class="w-full max-w-6xl mx-auto">
            <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                <div class="mb-4 sm:mb-6">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900 mb-1 flex items-center gap-4">
                        <span>Manage Courses</span>
                    </h1>
                    <div class="text-gray-600 text-xs sm:text-sm">{{ now()->format('l, F d, Y') }} | Course Management</div>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-3 sm:p-4 bg-green-100 border-green-400 text-green-700 rounded text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="mb-6 sm:mb-8">
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <a href="{{ route('courses.create') }}"
                           class="px-3 sm:px-6 py-2 sm:py-3 bg-[#FFD700] text-[#1E3A8A] font-semibold rounded-lg hover:bg-[#FFE44D] transition-colors text-sm">
                            Add New Course
                        </a>
                    </div>
                </div>

                <!-- Courses Table -->
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($courses as $course)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                            {{ $course->id }}
                                        </td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                            {{ $course->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                            {{ $course->code ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm text-gray-900">
                                            {{ $course->description ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                                            @if($course->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                                            <a href="{{ route('courses.edit', $course) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                            <form method="POST" action="{{ route('courses.toggle', $course) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-orange-600 hover:text-orange-900" onclick="return confirm('Are you sure you want to {{ $course->is_active ? 'deactivate' : 'activate' }} this course?')">
                                                    {{ $course->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 sm:px-6 py-4 text-center text-gray-500">
                                            No courses found.
                                        </td>
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
