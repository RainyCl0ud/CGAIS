<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4 overflow-auto">
            <div class="w-full max-w-6xl mx-auto">
                <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
                        <div>
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900">Schedule Management</h1>
                            <p class="text-gray-600 text-xs sm:text-sm mt-1">Manage your availability for appointments (Monday & Friday only)</p>
                        </div>
                        <a href="{{ route('schedules.create') }}" 
                           class="mt-3 sm:mt-0 px-4 sm:px-6 py-2 sm:py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors text-sm">
                            Add Schedule
                        </a>
                    </div>

                 @if(!empty($scheduleData) && count($scheduleData) > 0)
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Appointments</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($scheduleData as $day => $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                <div>
                                    <div class="font-medium">{{ ucfirst($day) }}</div>
                                    <div class="text-xs text-gray-500">Next: {{ $data['date'] }}</div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                {{ $data['has_schedule'] ? $data['schedule']->getFormattedTime() : '—' }}
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                {{ $data['has_schedule'] ? $data['schedule']->max_appointments : '—' }}
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                @if($data['has_schedule'])
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $data['schedule']->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $data['schedule']->is_available ? 'Available' : 'Unavailable' }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">No Schedule</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                                @if($data['has_schedule'])
                                    <div class="flex space-x-2">
                                        <a href="{{ route('schedules.edit', $data['schedule']) }}" 
                                           class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <form method="POST" action="{{ route('schedules.destroy', $data['schedule']) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Are you sure you want to delete this schedule?')"
                                                    class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                @else
                                    <a href="{{ route('schedules.create') }}" 
                                       class="text-green-600 hover:text-green-900">Add</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else mistralai/Mixtral-8x7B-Instruct-v0.1

    <div class="text-center py-8 sm:py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No schedules</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by adding your availability.</p>
        <div class="mt-6">
            <a href="{{ route('schedules.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                Add Schedule
            </a>
        </div>
    </div>
@endif

                </div>
            </div>
        </main>
    </div>
</x-app-layout> 