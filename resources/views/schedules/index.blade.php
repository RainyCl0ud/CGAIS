<x-app-layout>
    <div 
        x-data="calendarComponent()" 
        x-cloak 
        class="flex flex-col items-center justify-start py-4 sm:py-8 px-2 sm:px-4 overflow-auto"
    >
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
                            <div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-md">
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
                                            @click="handleDateClick(date)"
                                            :class="{
                                                'bg-gray-200 text-gray-400 cursor-not-allowed opacity-50 rounded-md py-2 focus:outline-none': date.isWeekend || date.isPast,
                                                'bg-red-500 text-white rounded-md py-2 focus:outline-none cursor-pointer': date.isUnavailable && !date.isWeekend && !date.isPast,
                                                'bg-gray-900 text-white rounded-md py-2 focus:outline-none': date.isToday || date.isSelected,
                                                'text-gray-400 rounded-md py-2 focus:outline-none': !date.isCurrentMonth,
                                                'hover:bg-gray-200 rounded-md py-2 focus:outline-none': date.isCurrentMonth && !date.isSelected && !date.isUnavailable && !date.isWeekend && !date.isPast,
                                                'py-2 focus:outline-none': true
                                            }"
                                            x-text="date.day"
                                            :aria-label="date.ariaLabel"
                                            :disabled="!date.isCurrentMonth || date.isWeekend || date.isPast"
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
                                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                                        @elseif($data['is_unavailable_date'])
                                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Unavailable</span>
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

        <!-- ✅ FIXED MODAL INSIDE SAME X-DATA SCOPE -->
        <div 
            x-show="showConfirmModal"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500 bg-opacity-75"
        >
            <div @click.away="cancelConfirm()" class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mark Date as Unavailable?</h3>
                <div class="flex justify-center space-x-3">
                    <button
                        type="button"
                        @click="confirmToggle()"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                    >
                        Confirm
                    </button>
                    <button
                        type="button"
                        @click="cancelConfirm()"
                        class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calendarComponent() {
            return {
                today: new Date(),
                selectedDate: null,
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                unavailableDates: @json($unavailableDates),

                showConfirmModal: false,
                pendingDate: null,

                get monthYear() {
                    return new Date(this.currentYear, this.currentMonth)
                        .toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                },

                get calendarDays() {
                    const days = [];
                    const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                    const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
                    const firstWeekday = firstDay.getDay();
                    const totalDays = lastDay.getDate();

                    for (let i = firstWeekday - 1; i >= 0; i--) {
                        const d = new Date(this.currentYear, this.currentMonth, -i);
                        days.push(this.makeDayObj(d, false));
                    }
                    for (let i = 1; i <= totalDays; i++) {
                        const d = new Date(this.currentYear, this.currentMonth, i);
                        days.push(this.makeDayObj(d, true));
                    }
                    const remaining = 42 - days.length;
                    for (let i = 1; i <= remaining; i++) {
                        const d = new Date(this.currentYear, this.currentMonth + 1, i);
                        days.push(this.makeDayObj(d, false));
                    }
                    return days;
                },

                makeDayObj(date, isCurrentMonth) {
                    const ds = date.toISOString().split('T')[0];
                    const dayOfWeek = date.getDay();
                    const isPast = date.getFullYear() < this.today.getFullYear() ||
                                   (date.getFullYear() === this.today.getFullYear() && date.getMonth() < this.today.getMonth()) ||
                                   (date.getFullYear() === this.today.getFullYear() && date.getMonth() === this.today.getMonth() && date.getDate() < this.today.getDate());
                    return {
                        date,
                        day: date.getDate(),
                        isCurrentMonth,
                        isToday: this.isSameDate(date, this.today),
                        isSelected: this.selectedDate && this.isSameDate(date, this.selectedDate),
                        isUnavailable: this.unavailableDates.includes(ds),
                        isWeekend: dayOfWeek === 0 || dayOfWeek === 6,
                        isPast: isPast,
                        ariaLabel: date.toDateString()
                    };
                },

                isSameDate(a, b) {
                    return a.getFullYear() === b.getFullYear() &&
                           a.getMonth() === b.getMonth() &&
                           a.getDate() === b.getDate();
                },

                prevMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else this.currentMonth--;
                },
                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else this.currentMonth++;
                },

                handleDateClick(date) {
                    if (!date.isCurrentMonth || date.isWeekend || date.isPast) return;
                    const dateStr = date.date.toISOString().split('T')[0];

                    if (date.isUnavailable) {
                        this.toggleUnavailableDate(dateStr);
                    } else {
                        this.pendingDate = dateStr;
                        this.showConfirmModal = true;
                    }
                },

                confirmToggle() {
                    if (!this.pendingDate) return;
                    this.toggleUnavailableDate(this.pendingDate);
                    this.pendingDate = null;
                    this.showConfirmModal = false;
                },

                cancelConfirm() {
                    this.pendingDate = null;
                    this.showConfirmModal = false;
                },

                toggleUnavailableDate(dateStr) {
                    fetch("{{ route('schedules.toggleUnavailableDate') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                        },
                        body: JSON.stringify({ date: dateStr })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'unavailable') {
                            this.unavailableDates.push(dateStr);
                        } else {
                            this.unavailableDates = this.unavailableDates.filter(d => d !== dateStr);
                        }
                        this.selectedDate = new Date(dateStr);
                        this.updateTableStatus(dateStr, data.status === 'available');
                    })
                    .catch(e => console.error('Error toggling date:', e));
                },

                updateTableStatus(toggledDateStr, isAvailable) {
                    const rows = document.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const dateDiv = row.querySelector('td:first-child > div:last-child');
                        if (dateDiv) {
                            let text = dateDiv.textContent.trim();
                            let match = text.match(/(?:Next|Today):\s*(.+)/);
                            if (match) {
                                let parsedDate = new Date(match[1].trim());
                                if (!isNaN(parsedDate)) {
                                    let rowDateStr = parsedDate.toISOString().split('T')[0];
                                    if (rowDateStr === toggledDateStr) {
                                        const span = row.querySelector('td:last-child span');
                                        if (span) {
                                            span.className = 'px-2 py-1 text-xs font-semibold rounded-full ' +
                                                (isAvailable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                                            span.textContent = isAvailable ? 'Available' : 'Unavailable';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>
