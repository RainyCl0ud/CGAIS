<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use App\Models\CounselorUnavailableDate;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        
        if (!$user->canManageSchedules()) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to manage schedules.');
        }

        // Get existing schedules
        $existingSchedules = $user->schedules()->get()->keyBy('day_of_week');

        // Get unavailable dates for calendar
        $unavailableDates = CounselorUnavailableDate::where('counselor_id', $user->id)
            ->where('expires_at', '>', Carbon::now('Asia/Manila'))
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();
        
        // Define weekdays in order
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        // Create schedule data for all weekdays
        $scheduleData = [];
        $today = now();

        // Get the Monday of the current week
        $monday = $today->copy()->startOfWeek(); // Monday of current week

        // Day offsets from Monday
        $dayOffsets = [
            'monday' => 0,
            'tuesday' => 1,
            'wednesday' => 2,
            'thursday' => 3,
            'friday' => 4,
        ];

        foreach ($weekdays as $day) {
            $dateToUse = $monday->copy()->addDays($dayOffsets[$day]);
            $dateStr = $dateToUse->format('Y-m-d');
            $isPast = $dateToUse->lt($today); // Check if date is before today
            $isUnavailable = in_array($dateStr, $unavailableDates);
            $schedule = $existingSchedules->get($day);
            $isAvailable = $schedule ? $schedule->is_available : true;
            if ($isUnavailable || $isPast) {
                $isAvailable = false;
            }
            $scheduleData[$day] = [
                'day' => $day,
                'date' => $dateToUse->format('M d, Y'),
                'schedule' => $schedule,
                'has_schedule' => $existingSchedules->has($day),
                'is_available' => $isAvailable,
                'is_unavailable_date' => $isUnavailable || $isPast,
                'is_past' => $isPast,
            ];
        }
        
        return view('schedules.index', compact('scheduleData', 'unavailableDates'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        
        if (!$user->canManageSchedules()) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to manage schedules.');
        }

        return view('schedules.create');
    }

    // New method to get unavailable dates for logged-in counselor
    public function getUnavailableDates(Request $request)
    {
        $user = $request->user();

        if (!$user->canManageSchedules()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $dates = CounselorUnavailableDate::where('counselor_id', $user->id)
            ->where('expires_at', '>', Carbon::now('Asia/Manila'))
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'));

        return response()->json(['unavailableDates' => $dates]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user->canManageSchedules()) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to manage schedules.');
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

        if (!$user->canManageSchedules()) {
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
                'expires_at' => Carbon::parse($date, 'Asia/Manila')->addDay()->startOfDay(),
            ]);
            $status = 'unavailable';
        }

        return response()->json(['status' => $status, 'date' => $date]);
    }

    public function edit(Schedule $schedule, Request $request): View
    {
        $user = $request->user();
        
        if (!$user->canManageSchedules() || $schedule->counselor_id !== $user->id) {
            abort(403);
        }

        return view('schedules.edit', compact('schedule'));
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user->canManageSchedules() || $schedule->counselor_id !== $user->id) {
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
        
        if (!$user->canManageSchedules() || $schedule->counselor_id !== $user->id) {
            abort(403);
        }

        $schedule->delete();

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}
