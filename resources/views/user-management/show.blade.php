<x-app-layout>
    <div class="flex flex-col items-center justify-start py-8 px-4">
            <div class="w-full max-w-4xl mx-auto">
                <div class="bg-white/90 rounded-2xl shadow-2xl border border-blue-100 p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-blue-900">User Details</h1>
                        <div class="flex space-x-3">
                            <a href="{{ route('users.edit', $user) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Edit User
                            </a>
                            <a href="{{ route('users.index') }}" 
                               class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                Back to Users
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- User Details -->
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold text-blue-900 mb-4">User Details</h2>
                            <div class="space-y-3">
                                <div>
                                    <span class="font-medium text-gray-700">Full Name:</span>
                                    <p class="text-gray-900">{{ $user->full_name }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Email:</span>
                                    <p class="text-gray-900">{{ $user->email }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Role:</span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $user->role === 'counselor' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $user->role === 'assistant' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $user->role === 'student' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $user->role === 'faculty' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $user->role === 'staff' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                                @if($user->student_id)
                                    <div>
                                        <span class="font-medium text-gray-700">Student ID:</span>
                                        <p class="text-gray-900">{{ $user->student_id }}</p>
                                    </div>
                                @endif
                                @if($user->faculty_id)
                                    <div>
                                        <span class="font-medium text-gray-700">Faculty ID:</span>
                                        <p class="text-gray-900">{{ $user->faculty_id }}</p>
                                    </div>
                                @endif
                                @if($user->staff_id)
                                    <div>
                                        <span class="font-medium text-gray-700">Staff ID:</span>
                                        <p class="text-gray-900">{{ $user->staff_id }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold text-green-900 mb-4">Account Information</h2>
                            <div class="space-y-3">
                                <div>
                                    <span class="font-medium text-gray-700">Account Created:</span>
                                    <p class="text-gray-900">{{ $user->created_at->format('M d, Y g:i A') }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Last Updated:</span>
                                    <p class="text-gray-900">{{ $user->updated_at->format('M d, Y g:i A') }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Email Verified:</span>
                                    <p class="text-gray-900">
                                        @if($user->email_verified_at)
                                            <span class="text-green-600">✓ Verified on {{ $user->email_verified_at->format('M d, Y') }}</span>
                                        @else
                                            <span class="text-red-600">✗ Not verified</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Statistics -->
                    <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">User Statistics</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $user->appointments()->count() }}</div>
                                <div class="text-sm text-gray-600">Total Appointments</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $user->appointments()->where('status', 'completed')->count() }}</div>
                                <div class="text-sm text-gray-600">Completed Appointments</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ $user->appointments()->where('status', 'pending')->count() }}</div>
                                <div class="text-sm text-gray-600">Pending Appointments</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    @if($user->appointments()->count() > 0)
                        <div class="mt-8 bg-white p-6 rounded-lg border border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Appointments</h2>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($user->appointments()->latest()->take(5)->get() as $appointment)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $appointment->appointment_date->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $appointment->start_time->format('g:i A') }} - {{ $appointment->end_time->format('g:i A') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->getTypeBadgeClass() }}">
                                                        {{ ucfirst($appointment->type) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Danger Zone -->
                    @if($user->id !== auth()->id())
                        <div class="mt-8 bg-red-50 p-6 rounded-lg border border-red-200">
                            <h2 class="text-xl font-semibold text-red-900 mb-4">Danger Zone</h2>
                            <p class="text-sm text-gray-700 mb-4">Once you delete a user, there is no going back. Please be certain.</p>
                            <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                  onsubmit="return confirm('Are you absolutely sure you want to delete this user? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    Delete User
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
