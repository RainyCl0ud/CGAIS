<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use Illuminate\View\View;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\PersonalDataSheet;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentManagementController extends Controller
{
    /**
     * Display the student directory
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'student')
            ->with(['personalDataSheet', 'appointments']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->whereHas('course', function ($q) use ($request) {
                $q->where('name', $request->course);
            });
        }

        // Filter by year level
        if ($request->filled('year_level')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('personalDataSheet', function ($subQ) use ($request) {
                    $subQ->where('year_level', $request->year_level);
                })->orWhere('year_level', $request->year_level);
            });
        }

        // Filter by PDS completion status
        if ($request->filled('pds_status')) {
            if ($request->pds_status === 'complete') {
                $query->whereHas('personalDataSheet', function ($q) {
                    $q->whereNotNull('birth_date')
                      ->whereNotNull('birth_place')
                      ->whereNotNull('sex')
                      ->whereNotNull('mobile_number')
                      ->whereNotNull('permanent_address');
                });
            } elseif ($request->pds_status === 'incomplete') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('personalDataSheet')
                      ->orWhereHas('personalDataSheet', function ($subQ) {
                          $subQ->whereNull('birth_date')
                               ->orWhereNull('birth_place')
                               ->orWhereNull('sex')
                               ->orWhereNull('mobile_number')
                               ->orWhereNull('permanent_address');
                      });
                });
            }
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'last_name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['last_name', 'first_name', 'email', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $students = $query->paginate(15);

        // Get statistics
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'complete_pds' => User::where('role', 'student')
                ->whereHas('personalDataSheet', function ($q) {
                    $q->whereNotNull('birth_date')
                      ->whereNotNull('birth_place')
                      ->whereNotNull('sex')
                      ->whereNotNull('mobile_number')
                      ->whereNotNull('permanent_address');
                })->count(),
            'incomplete_pds' => User::where('role', 'student')
                ->where(function ($q) {
                    $q->whereDoesntHave('personalDataSheet')
                      ->orWhereHas('personalDataSheet', function ($subQ) {
                          $subQ->whereNull('birth_date')
                               ->orWhereNull('birth_place')
                               ->orWhereNull('sex')
                               ->orWhereNull('mobile_number')
                               ->orWhereNull('permanent_address');
                      });
                })->count(),
        ];

        // Get unique courses and year levels for filters
        $courses = Course::active()->pluck('name')->sort()->values();

        $pdsYearLevels = PersonalDataSheet::whereNotNull('year_level')
            ->distinct()
            ->pluck('year_level');
            
        $userYearLevels = User::where('role', 'student')
            ->whereNotNull('year_level')
            ->distinct()
            ->pluck('year_level');
            
        $yearLevels = $pdsYearLevels->merge($userYearLevels)->sort()->values();

        return view('student-management.index', compact(
            'students',
            'stats',
            'courses',
            'yearLevels'
        ));
    }

    /**
     * Display a specific student's profile
     */
    public function show(User $student): View
    {
        // Ensure the user is a student
        if ($student->role !== 'student') {
            abort(404);
        }

        // Load relationships
        $student->load(['personalDataSheet', 'appointments.counselor']);

        // Get appointment statistics
        $appointmentStats = [
            'total' => $student->appointments()->count(),
            'completed' => $student->appointments()->where('status', 'completed')->count(),
            'pending' => $student->appointments()->where('status', 'pending')->count(),
            'cancelled' => $student->appointments()->where('status', 'cancelled')->count(),
        ];

        // Log the access
ActivityLogService::log(
    Auth::id(), // causer (User model or null)
    'viewed_student_pds', // event name
    $student, // subject (User model of the student being viewed)
    ['student_id' => $student->id] // properties as array, not User::class
);



        return view('student-management.show', compact('student', 'appointmentStats'));
    }

    /**
     * Display a student's PDS
     */
    public function showPds(User $student): View
    {
        // Ensure the user is a student
        if ($student->role !== 'student') {
            abort(404);
        }

        // Load PDS
        $student->load('personalDataSheet');

        // Log the access
        ActivityLogService::log(
            Auth::id(),
            'viewed_student_pds',
            $student,
            ['student_id' => $student->id]
        );

        return view('student-management.pds', compact('student'));
    }

    /**
     * Print the student's PDS as a server-side generated PDF (DOMPDF).
     */
    public function printPds(User $student)
    {
        try {
            if ($student->role !== 'student') {
                abort(404);
            }

            $student->load('personalDataSheet');

            // Prepare logos (from public path)
            $logos = [];
            $logoPath = public_path('images/ustp-logo.png');
            if (file_exists($logoPath)) {
                $contents = file_get_contents($logoPath);
                $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents);
                $logos['logo'] = 'data:' . ($mime ?: 'image/png') . ';base64,' . base64_encode($contents);
            }

            // Prepare student photo (from storage public disk)
            $photoData = null;
            $pds = $student->personalDataSheet;
            if ($pds && ! empty($pds->photo)) {
                // assume photo stored on public disk (storage/app/public/...)
                $storagePath = storage_path('app/public/' . ltrim($pds->photo, '/'));
                if (file_exists($storagePath)) {
                    $contents = file_get_contents($storagePath);
                    $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents);
                    $photoData = 'data:' . ($mime ?: 'image/jpeg') . ';base64,' . base64_encode($contents);
                }
            }

            $data = [
                'student' => $student,
                'pds' => $pds,
                'logos' => $logos,
                'photoData' => $photoData,
            ];

            $pdf = Pdf::loadView('pdfs.pds', $data)->setPaper('A4', 'portrait');

            return $pdf->stream('pds_' . ($student->id ?? 'student') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Failed to generate PDS PDF: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'PDF generation failed'], 500);
        }
    }

    /**
     * Return a printable HTML view of the PDS (client-side print).
     */
    public function printViewPds(User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        $student->load('personalDataSheet');

        $logos = [];
        $logoPath = public_path('images/ustp-logo.png');
        if (file_exists($logoPath)) {
            $contents = file_get_contents($logoPath);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents);
            $logos['logo'] = 'data:' . ($mime ?: 'image/png') . ';base64,' . base64_encode($contents);
        }

        $photoData = null;
        $pds = $student->personalDataSheet;
        if ($pds && ! empty($pds->photo)) {
            $storagePath = storage_path('app/public/' . ltrim($pds->photo, '/'));
            if (file_exists($storagePath)) {
                $contents = file_get_contents($storagePath);
                $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents);
                $photoData = 'data:' . ($mime ?: 'image/jpeg') . ';base64,' . base64_encode($contents);
            }
        }

        return view('pdfs.pds_html', [
            'student' => $student,
            'pds' => $pds,
            'logos' => $logos,
            'photoData' => $photoData,
        ]);
    }

    /**
     * Generate a PDF from the PDS view and save it to public storage, returning URL.
     */
    public function generatePdsPdf(Request $request, User $student)
    {
        try {
            if ($student->role !== 'student') {
                abort(404);
            }

            $student->load('personalDataSheet');
            $pds = $student->personalDataSheet;

            $logos = [];
            $logoPath = public_path('images/ustp-logo.png');
            if (file_exists($logoPath)) {
                $contents = file_get_contents($logoPath);
                $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents);
                $logos['logo'] = 'data:' . ($mime ?: 'image/png') . ';base64,' . base64_encode($contents);
            }

            $photoData = null;
            if ($pds && ! empty($pds->photo)) {
                $storagePath = storage_path('app/public/' . ltrim($pds->photo, '/'));
                if (file_exists($storagePath)) {
                    $contents = file_get_contents($storagePath);
                    $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents);
                    $photoData = 'data:' . ($mime ?: 'image/jpeg') . ';base64,' . base64_encode($contents);
                }
            }

            $data = [
                'student' => $student,
                'pds' => $pds,
                'logos' => $logos,
                'photoData' => $photoData,
            ];

            $pdf = Pdf::loadView('pdfs.pds', $data)->setPaper('A4', 'portrait');
            $output = $pdf->output();

            $filename = 'pds/pds_' . ($student->id ?? 'student') . '_' . time() . '.pdf';
            // ensure directory exists
            Storage::disk('public')->put($filename, $output);

            $url = asset('storage/' . $filename);
            return response()->json(['url' => $url]);
        } catch (\Throwable $e) {
            Log::error('Failed to generate & save PDS PDF: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'PDF generation failed'], 500);
        }
    }

    /**
     * Get student statistics for dashboard
     */
    public function getStatistics(): JsonResponse
    {
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'complete_pds' => User::where('role', 'student')
                ->whereHas('personalDataSheet', function ($q) {
                    $q->whereNotNull('birth_date')
                      ->whereNotNull('birth_place')
                      ->whereNotNull('sex')
                      ->whereNotNull('mobile_number')
                      ->whereNotNull('permanent_address');
                })->count(),
            'incomplete_pds' => User::where('role', 'student')
                ->where(function ($q) {
                    $q->whereDoesntHave('personalDataSheet')
                      ->orWhereHas('personalDataSheet', function ($subQ) {
                          $subQ->whereNull('birth_date')
                               ->orWhereNull('birth_place')
                               ->orWhereNull('sex')
                               ->orWhereNull('mobile_number')
                               ->orWhereNull('permanent_address');
                      });
                })->count(),
            'course_distribution' => PersonalDataSheet::whereNotNull('course')
                ->select('course', DB::raw('count(*) as count'))
                ->groupBy('course')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
            'year_level_distribution' => PersonalDataSheet::whereNotNull('year_level')
                ->select('year_level', DB::raw('count(*) as count'))
                ->groupBy('year_level')
                ->orderBy('year_level')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Export student directory
     */
    public function export(Request $request)
    {
        $query = User::where('role', 'student')
            ->with(['personalDataSheet', 'appointments']);

        // Apply same filters as index method
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course')) {
            $query->whereHas('course', function ($q) use ($request) {
                $q->where('name', $request->course);
            });
        }

        if ($request->filled('year_level')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('personalDataSheet', function ($subQ) use ($request) {
                    $subQ->where('year_level', $request->year_level);
                })->orWhere('year_level', $request->year_level);
            });
        }

        $students = $query->get();

        $filename = 'student_directory_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Student ID',
                'Name',
                'Email',
                'Course',
                'Year Level',
                'Mobile Number',
                'PDS Completion %',
                'Total Appointments',
                'Completed Appointments',
                'Last Appointment Date'
            ]);

            foreach ($students as $student) {
                $pds = $student->personalDataSheet;
                $appointments = $student->appointments;
                
                fputcsv($file, [
                    $student->student_id ?? 'N/A',
                    $student->getFullNameAttribute(),
                    $student->email,
                    $student->course?->name ?? $pds->course ?? $student->course_category ?? 'N/A',
                    $pds->year_level ?? $student->year_level ?? 'N/A',
                    $pds->mobile_number ?? $student->phone_number ?? 'N/A',
                    $pds ? $pds->getCompletionPercentage() . '%' : '0%',
                    $appointments->count(),
                    $appointments->where('status', 'completed')->count(),
                    $appointments->max('appointment_date') ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
