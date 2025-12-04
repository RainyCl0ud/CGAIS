<x-app-layout>
    <div class="flex flex-col items-center justify-start py-8 px-4">
            <div class="w-full max-w-4xl mx-auto">
                <div class="bg-white/90 rounded-2xl shadow-2xl border border-blue-100 p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-blue-900">Edit User</h1>
                        <div class="flex space-x-3">
                            <a href="{{ route('users.show', $user) }}" 
                               class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                View User
                            </a>
                            <a href="{{ route('users.index') }}" 
                               class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                Back to Users
                            </a>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- User Details -->
                            <div class="space-y-4">
                                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">User Details</h2>
                                
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="name_extension" class="block text-sm font-medium text-gray-700 mb-1">Name Extension</label>
                                    <input type="text" id="name_extension" name="name_extension" value="{{ old('name_extension', $user->name_extension) }}"
                                           placeholder="Jr., Sr., III, etc."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div class="space-y-4">
                                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">Account Information</h2>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                                    <select id="role" name="role" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Role</option>
                                        <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="faculty" {{ old('role', $user->role) === 'faculty' ? 'selected' : '' }}>Faculty</option>
                                        <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                                    <input type="text" id="student_id" name="student_id" value="{{ old('student_id', $user->student_id) }}"
                                           placeholder="e.g., 2024-0001"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="faculty_id" class="block text-sm font-medium text-gray-700 mb-1">Faculty ID</label>
                                    <input type="text" id="faculty_id" name="faculty_id" value="{{ old('faculty_id', $user->faculty_id) }}"
                                           placeholder="e.g., F20240001"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-1">Staff ID</label>
                                    <input type="text" id="staff_id" name="staff_id" value="{{ old('staff_id', $user->staff_id) }}"
                                           placeholder="e.g., S20240001"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Password (Optional) -->
                        <div class="space-y-4">
                            <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">Password (Optional)</h2>
                            <p class="text-sm text-gray-600">Leave blank to keep the current password</p>
                            
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                    <input type="password" id="password" name="password"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('users.show', $user) }}" 
                               class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
