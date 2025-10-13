<x-app-layout>
    <div class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4 overflow-auto">
        <div class="w-full max-w-6xl mx-auto">
            <div class="bg-white/80 rounded-lg sm:rounded-2xl shadow-lg sm:shadow-2xl border border-blue-100 p-4 sm:p-8 backdrop-blur">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900">Schedule Management</h1>
                        <p class="text-gray-600 text-xs sm:text-sm mt-1">Manage your availability for appointments (Monday & Friday only)</p>
                    </div>
                </div>

                <div class="flex space-x-6">
                    <div class="w-1/2">
                <div id="calendar" style="height: 400px;">
                    <div x-data="calendarComponent()" class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-lg font-semibold text-gray-900" x-text="monthYear"></div>
                            <div class="flex flex-col space-y-1">
                                <button @click="prevMonth" class="text-gray-600 hover:text-gray-900 focus:outline-none" aria-label="Previous Month">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                </button>
                                <button @click="nextMonth" class="text-gray-600 hover:text-gray-900 focus:outline-none" aria-label="Next Month">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-gray-500">
                            <template x-for="day in daysOfWeek" :key="day">
                                <div x-text="day"></div>
                            </template>
                        </div>
                        <div class="grid grid-cols-7 gap-1 mt-2 text-center">
                            <template x-for="date in calendarDays" :key="date.date">
                                <button
                                    @click="selectDate(date)"
                                    :class="{
                                        'bg-gray-900 text-white': date.isToday || date.isSelected,
                                        'text-gray-400': !date.isCurrentMonth,
                                        'hover:bg-gray-200': date.isCurrentMonth && !date.isSelected,
                                        'rounded-md': true,
                                        'py-2': true,
                                        'focus:outline-none': true
                                    }"
                                    x-text="date.day"
                                    :aria-label="date.ariaLabel"
                                    :disabled="!date.isCurrentMonth"
                                ></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-1/2 overflow-auto max-h-[400px]">
                        @if(!empty($scheduleData) && count($scheduleData) > 0)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($scheduleData as $day => $data)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                    <div>
                                                        <div class="font-medium">{{ ucfirst($day) }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            @if(\Carbon\Carbon::parse($data['date'])->isToday())
                                                                <p class="text-green-500 text-xs">Today: {{ $data['date'] }}</p>
                                                            @else
                                                                Next: {{ $data['date'] }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                    @if($data['is_available'])
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                            Available
                                                        </span>
                                                    @elseif($data['is_unavailable_date'])
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                            Unavailable
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">No Schedule</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @else
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
            </div>
        </div>
    </div>

    {{-- <div x-data="calendarComponent()" class="max-w-md mx-auto p-4 bg-red-500 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="text-lg font-semibold text-gray-900" x-text="monthYear"></div>
            <div class="flex flex-col space-y-1">
                <button @click="prevMonth" class="text-gray-600 hover:text-gray-900 focus:outline-none" aria-label="Previous Month">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                </button>
                <button @click="nextMonth" class="text-gray-600 hover:text-gray-900 focus:outline-none" aria-label="Next Month">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-gray-500">
            <template x-for="day in daysOfWeek" :key="day">
                <div x-text="day"></div>
            </template>
        </div>
        <div class="grid grid-cols-7 gap-1 mt-2 text-center">
            <template x-for="date in calendarDays" :key="date.date">
                <button
                    @click="selectDate(date)"
                    :class="{
                        'bg-gray-900 text-white': date.isToday || date.isSelected,
                        'text-gray-400': !date.isCurrentMonth,
                        'hover:bg-gray-200': date.isCurrentMonth && !date.isSelected,
                        'rounded-md': true,
                        'py-2': true,
                        'focus:outline-none': true
                    }"
                    x-text="date.day"
                    :aria-label="date.ariaLabel"
                    :disabled="!date.isCurrentMonth"
                ></button>
            </template>
        </div>
    </div> --}}

    <script>
        function calendarComponent() {
            return {
                today: new Date(),
                selectedDate: null,
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                unavailableDates: @json($unavailableDates),
                get monthYear() {
                    return new Date(this.currentYear, this.currentMonth).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                },
                get calendarDays() {
                    const days = [];
                    const firstDayOfMonth = new Date(this.currentYear, this.currentMonth, 1);
                    const lastDayOfMonth = new Date(this.currentYear, this.currentMonth + 1, 0);
                    const firstDayWeekday = firstDayOfMonth.getDay();
                    const daysInMonth = lastDayOfMonth.getDate();

                    // Previous month's days to fill first week
                    for (let i = firstDayWeekday - 1; i >= 0; i--) {
                        const date = new Date(this.currentYear, this.currentMonth, -i);
                        days.push({
                            date,
                            day: date.getDate(),
                            isCurrentMonth: false,
                            isToday: false,
                            isSelected: false,
                            isUnavailable: false,
                            ariaLabel: date.toDateString()
                        });
                    }

                    // Current month's days
                    for (let i = 1; i <= daysInMonth; i++) {
                        const date = new Date(this.currentYear, this.currentMonth, i);
                        const dateStr = date.toISOString().split('T')[0];
                        days.push({
                            date,
                            day: i,
                            isCurrentMonth: true,
                            isToday: this.isSameDate(date, this.today),
                            isSelected: this.selectedDate ? this.isSameDate(date, this.selectedDate) : false,
                            isUnavailable: this.unavailableDates.includes(dateStr),
                            ariaLabel: date.toDateString()
                        });
                    }

                    // Next month's days to fill last week
                    const remaining = 42 - days.length; // 6 weeks * 7 days
                    for (let i = 1; i <= remaining; i++) {
                        const date = new Date(this.currentYear, this.currentMonth + 1, i);
                        days.push({
                            date,
                            day: date.getDate(),
                            isCurrentMonth: false,
                            isToday: false,
                            isSelected: false,
                            isUnavailable: false,
                            ariaLabel: date.toDateString()
                        });
                    }

                    return days;
                },
                isSameDate(d1, d2) {
                    return d1.getFullYear() === d2.getFullYear() &&
                        d1.getMonth() === d2.getMonth() &&
                        d1.getDate() === d2.getDate();
                },
                prevMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                    this.selectedDate = null;
                },
                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                    this.selectedDate = null;
                },
                selectDate(date) {
                    if (!date.isCurrentMonth) return;
                    const dateStr = date.date.toISOString().split('T')[0];
                    // Toggle unavailable date via API
                    fetch("{{ route('schedules.toggleUnavailableDate') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ date: dateStr })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'unavailable') {
                            this.unavailableDates.push(dateStr);
                        } else if (data.status === 'available') {
                            this.unavailableDates = this.unavailableDates.filter(d => d !== dateStr);
                        }
                        this.selectedDate = date.date;
                        this.updateTableStatus(date.date, data.status === 'available');
                    })
                    .catch(error => {
                        console.error('Error toggling unavailable date:', error);
                    });
                },
                updateTableStatus(date, isAvailable) {
                    const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                    const dayName = dayNames[date.getDay()];
                    const rows = document.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const dayCell = row.querySelector('td:first-child div:first-child');
                        if (dayCell && dayCell.textContent.toLowerCase() === dayName) {
                            const statusSpan = row.querySelector('td:last-child span');
                            if (statusSpan) {
                                statusSpan.className = 'px-2 py-1 text-xs font-semibold rounded-full ' + (isAvailable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                                statusSpan.textContent = isAvailable ? 'Available' : 'Unavailable';
                            }
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>
