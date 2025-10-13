<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use App\Models\CounselorUnavailableDate;

class ScheduleController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        
        if (!$user->isCounselor()) {
            return redirect()->route('dashboard')
                ->with('error', 'Only counselors can manage schedules.');
        }

        // Get existing schedules
        $existingSchedules = $user->schedules()->get()->keyBy('day_of_week');

        // Get unavailable dates for calendar
        $unavailableDates = CounselorUnavailableDate::where('counselor_id', $user->id)
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();
        
        // Define weekdays in order
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        
        // Create schedule data for all weekdays
        $scheduleData = [];
        $today = now();
        
        foreach ($weekdays as $day) {
            $currentWeekDate = $today->copy()->next($day);
            // If today is the same weekday, use today instead of next occurrence
            if ($today->isSameDay($currentWeekDate) || $today->dayOfWeek === $currentWeekDate->dayOfWeek) {
                $dateToUse = $today->copy();
            } else {
                $dateToUse = $currentWeekDate;
            }
            $dateStr = $dateToUse->format('Y-m-d');
            $isUnavailable = in_array($dateStr, $unavailableDates);
            $schedule = $existingSchedules->get($day);
            $isAvailable = $schedule ? $schedule->is_available : true;
            if ($isUnavailable) {
                $isAvailable = false;
            }
            $scheduleData[$day] = [
                'day' => $day,
                'date' => $dateToUse->format('M d, Y'),
                'schedule' => $schedule,
                'has_schedule' => $existingSchedules->has($day),
                'is_available' => $isAvailable,
                'is_unavailable_date' => $isUnavailable,
            ];
        }
        
        return view('schedules.index', compact('scheduleData', 'unavailableDates'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        
        if (!$user->isCounselor()) {
            return redirect()->route('dashboard')
                ->with('error', 'Only counselors can manage schedules.');
        }

        return view('schedules.create');
    }

    // New method to get unavailable dates for logged-in counselor
    public function getUnavailableDates(Request $request)
    {
        $user = $request->user();

        if (!$user->isCounselor()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $dates = CounselorUnavailableDate::where('counselor_id', $user->id)
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'));

        return response()->json(['unavailableDates' => $dates]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user->isCounselor()) {
            return redirect()->route('dashboard')
                ->with('error', 'Only counselors can manage schedules.');
        }

        $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'max_appointments' => 'required|integer|min:1|max:10',
            'is_available' => 'boolean',
        ]);

        // Check if schedule already exists for this day
        $existingSchedule = Schedule::where('counselor_id', $user->id)
            ->where('day_of_week', $request->day_of_week)
            ->first();

        if ($existingSchedule) {
            return back()->withErrors(['day_of_week' => 'Schedule already exists for this day.']);
        }

        Schedule::create([
            'counselor_id' => $user->id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'max_appointments' => $request->max_appointments,
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule created successfully.');
    }

    // New method to toggle unavailable date for logged-in counselor
    public function toggleUnavailableDate(Request $request)
    {
        $user = $request->user();

        if (!$user->isCounselor()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->input('date');

        $unavailableDate = CounselorUnavailableDate::where('counselor_id', $user->id)
            ->where('date', $date)
            ->first();

        if ($unavailableDate) {
            // If exists, remove unavailability (toggle off)
            $unavailableDate->delete();
            $status = 'available';
        } else {
            // Otherwise, mark as unavailable
            CounselorUnavailableDate::create([
                'counselor_id' => $user->id,
                'date' => $date,
                'is_unavailable' => true,
            ]);
            $status = 'unavailable';
        }

        return response()->json(['status' => $status, 'date' => $date]);
    }

    public function edit(Schedule $schedule, Request $request): View
    {
        $user = $request->user();
        
        if (!$user->isCounselor() || $schedule->counselor_id !== $user->id) {
            abort(403);
        }

        return view('schedules.edit', compact('schedule'));
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user->isCounselor() || $schedule->counselor_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'max_appointments' => 'required|integer|min:1|max:10',
            'is_available' => 'boolean',
        ]);

        $schedule->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'max_appointments' => $request->max_appointments,
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Schedule $schedule, Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user->isCounselor() || $schedule->counselor_id !== $user->id) {
            abort(403);
        }

        $schedule->delete();

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}
