<?php

namespace App\Http\Controllers;

use App\Models\PersonalDataSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class PersonalDataSheetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('student');
    }

    public function show()
    {
        $user = Auth::user();
        $pds = $user->personalDataSheet;
        
        if (!$pds) {
            $pds = new PersonalDataSheet();
        }
        
        return view('pds.show', compact('pds'));
    }

    public function edit()
    {
        $user = Auth::user();
        $pds = $user->personalDataSheet;
        
        if (!$pds) {
            $pds = new PersonalDataSheet();
        }
        
        return view('pds.edit', compact('pds'));
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Please log in to continue.');
            }

            $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'age' => 'nullable|string|max:10',
            'birth_place' => 'nullable|string|max:255',
            'sex' => ['nullable', Rule::in(['male', 'female'])],
            'civil_status' => ['nullable', Rule::in(['single', 'married', 'widowed', 'separated', 'divorced'])],
            'religion' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'citizenship' => 'nullable|string|max:255',
            'height' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',
            'blood_type' => 'nullable|string|max:10',
            'mobile_number' => 'nullable|string|max:20',
            'telephone_number' => 'nullable|string|max:20',
            'permanent_address' => 'nullable|string|max:500',
            'present_address' => 'nullable|string|max:500',
            'father_name' => 'nullable|string|max:255',
            'father_age' => 'nullable|string|max:10',
            'father_occupation' => 'nullable|string|max:255',
            'father_contact' => 'nullable|string|max:20',
            'father_education' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_age' => 'nullable|string|max:10',
            'mother_occupation' => 'nullable|string|max:255',
            'mother_contact' => 'nullable|string|max:20',
            'mother_education' => 'nullable|string|max:255',
            'parents_address' => 'nullable|string|max:500',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_contact' => 'nullable|string|max:20',
            'spouse_occupation' => 'nullable|string|max:255',
            'spouse_education' => 'nullable|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_age' => 'nullable|string|max:10',
            'guardian_occupation' => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:20',
            'guardian_relationship' => 'nullable|string|max:100',
            'course' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'year_level' => 'nullable|string|max:50',
            'last_school' => 'nullable|string|max:255',
            'school_location' => 'nullable|string|max:255',
            'previous_course' => 'nullable|string|max:255',
            'elementary_school' => 'nullable|string|max:255',
            'elementary_year_graduated' => 'nullable|string|max:10',
            'high_school' => 'nullable|string|max:255',
            'high_school_year_graduated' => 'nullable|string|max:10',
            'college' => 'nullable|string|max:255',
            'college_year_graduated' => 'nullable|string|max:10',
            'student_id_number' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'emergency_contact_number' => 'nullable|string|max:20',
            'emergency_contact_address' => 'nullable|string|max:500',
            'medical_conditions' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:1000',
            'medications' => 'nullable|string|max:1000',
            'reason_for_course' => 'nullable|string|max:1000',
            'physical_conditions' => 'nullable|string|max:1000',
            'intervention_treatment' => 'nullable|boolean',
            'intervention_details' => 'nullable|string|max:2000',
            'awards' => 'nullable|array',
            'awards.*.award' => 'nullable|string|max:255',
            'awards.*.school' => 'nullable|string|max:255',
            'awards.*.year' => 'nullable|string|max:10',
            'hobbies' => 'nullable|string|max:1000',
            'interests' => 'nullable|string|max:1000',
            'goals' => 'nullable|string|max:1000',
            'concerns' => 'nullable|string|max:1000',
            'health_condition' => 'nullable|string|max:10',
            'health_condition_specify' => 'nullable|string|max:1000',
            'intervention' => 'nullable|string|max:10',
            'intervention_types' => 'nullable|array',
            'intervention_types.*' => 'nullable|string|max:50',
            'tutorial_subjects' => 'nullable|string|max:500',
            'intervention_other' => 'nullable|string|max:1000',
            'signature' => 'nullable|string|max:255',
            'signature_date' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $pds = $user->personalDataSheet;

        if (!$pds) {
            $pds = new PersonalDataSheet();
            $pds->user_id = $user->id;
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            Log::info('Photo file detected in request');
            // Delete old photo if exists
            if ($pds->photo && Storage::disk('public')->exists($pds->photo)) {
                Storage::disk('public')->delete($pds->photo);
            }

            // Store new photo
            $photoPath = $request->file('photo')->store('pds-photos', 'public');
            Log::info('Photo stored at path: ' . $photoPath);
            $validated['photo'] = $photoPath;
            Log::info('Photo path added to validated data: ' . $validated['photo']);
        } else {
            Log::info('No photo file in request');
        }

        Log::info('Validated data before fill: ' . json_encode($validated));
        $pds->fill($validated);
        $saved = $pds->save();
        Log::info('Save result: ' . ($saved ? 'success' : 'failed'));
        Log::info('PDS photo after save: ' . $pds->photo);

        return redirect()->route('pds.show')
            ->with('success', 'Personal Data Sheet updated successfully.');
        } catch (\Exception $e) {
            \Log::error('PDS Update Error: ' . $e->getMessage());
            \Log::error('PDS Update Error Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while saving the Personal Data Sheet. Please try again.');
        }
    }

    public function autoSave(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'sex' => ['nullable', Rule::in(['male', 'female'])],
            'civil_status' => ['nullable', Rule::in(['single', 'married', 'widowed', 'separated', 'divorced'])],
            'citizenship' => 'nullable|string|max:255',
            'height' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',
            'blood_type' => 'nullable|string|max:10',
            'mobile_number' => 'nullable|string|max:20',
            'telephone_number' => 'nullable|string|max:20',
            'permanent_address' => 'nullable|string|max:500',
            'present_address' => 'nullable|string|max:500',
            'father_name' => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'father_contact' => 'nullable|string|max:20',
            'mother_name' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'mother_contact' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_relationship' => 'nullable|string|max:100',
            'guardian_contact' => 'nullable|string|max:20',
            'elementary_school' => 'nullable|string|max:255',
            'elementary_year_graduated' => 'nullable|string|max:10',
            'high_school' => 'nullable|string|max:255',
            'high_school_year_graduated' => 'nullable|string|max:10',
            'college' => 'nullable|string|max:255',
            'college_year_graduated' => 'nullable|string|max:10',
            'course' => 'nullable|string|max:255',
            'year_level' => 'nullable|string|max:50',
            'student_id_number' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'emergency_contact_number' => 'nullable|string|max:20',
            'emergency_contact_address' => 'nullable|string|max:500',
            'medical_conditions' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:1000',
            'medications' => 'nullable|string|max:1000',
            'hobbies' => 'nullable|string|max:1000',
            'interests' => 'nullable|string|max:1000',
            'goals' => 'nullable|string|max:1000',
            'concerns' => 'nullable|string|max:1000',
        ]);

        $pds = $user->personalDataSheet;
        
        if (!$pds) {
            $pds = new PersonalDataSheet();
            $pds->user_id = $user->id;
        }
        
        $pds->fill($validated);
        $pds->save();

        return response()->json([
            'success' => true,
            'message' => 'Data auto-saved successfully',
            'completion_percentage' => $pds->getCompletionPercentage()
        ]);
    }

    /**
     * Return a printable HTML view of the authenticated student's PDS.
     */
    public function printView()
    {
        $user = Auth::user();
        $pds = $user->personalDataSheet;

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

        return view('pdfs.pds_html', [
            'student' => $user,
            'pds' => $pds,
            'logos' => $logos,
            'photoData' => $photoData,
        ]);
    }

    /**
     * Generate PDS PDF for the authenticated student, save to public storage, and return URL.
     */
    public function generatePdf(Request $request)
    {
        try {
            $user = Auth::user();
            $pds = $user->personalDataSheet;

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
                'student' => $user,
                'pds' => $pds,
                'logos' => $logos,
                'photoData' => $photoData,
            ];

            $pdf = Pdf::loadView('pdfs.pds', $data)->setPaper('A4', 'portrait');
            $output = $pdf->output();

            $filename = 'pds/pds_' . ($user->id ?? 'user') . '_' . time() . '.pdf';
            Storage::disk('public')->put($filename, $output);

            $url = asset('storage/' . $filename);
            return response()->json(['url' => $url]);
        } catch (\Throwable $e) {
            Log::error('Failed to generate personal PDS PDF: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'PDF generation failed'], 500);
        }
    }
} 