<?php

namespace App\Http\Controllers;

use App\Models\AuthorizedId;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AuthorizedIdController extends Controller
{
    public function __construct()
    {
        $this->middleware('counselor_only');
    }

    /**
     * Display a listing of authorized IDs
     */
    public function index(Request $request)
    {
        $query = AuthorizedId::with(['usedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }



        // Search by ID number
        if ($request->filled('search')) {
            $query->where('id_number', 'like', '%' . $request->search . '%');
        }

        $authorizedIds = $query->paginate(20);

        return view('authorized-ids.index', compact('authorizedIds'));
    }

    /**
     * Show the form for creating new authorized IDs
     */
    public function create()
    {
        return view('authorized-ids.create');
    }

    /**
     * Store newly created authorized IDs
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_number' => 'required|string|min:3|max:50',
            'type' => ['required', Rule::in(['student', 'faculty', 'staff'])],
        ]);

        $idNumber = trim($request->id_number);

        if (strlen($idNumber) < 3) {
            return back()->withErrors(['id_number' => 'ID number must be at least 3 characters long.']);
        }

        // Check if ID already exists
        $existingId = AuthorizedId::where('id_number', $idNumber)->first();
        if ($existingId) {
            return back()->withErrors(['id_number' => "ID '$idNumber' already exists."]);
        }

        // Check if ID is already assigned to an existing user
        $existingUser = null;
        if ($request->type === 'student') {
            $existingUser = User::where('student_id', $idNumber)->first();
        } elseif ($request->type === 'faculty') {
            $existingUser = User::where('faculty_id', $idNumber)->first();
        } elseif ($request->type === 'staff') {
            $existingUser = User::where('staff_id', $idNumber)->first();
        }

        if ($existingUser) {
            return back()->withErrors(['id_number' => "ID '$idNumber' is already assigned to an existing user and cannot be authorized."]);
        }

        try {
            AuthorizedId::create([
                'id_number' => $idNumber,
                'type' => $request->type,
            ]);
            return redirect()->route('authorized-ids.index')->with('success', 'Successfully created authorized ID.');
        } catch (\Exception $e) {
            return back()->withErrors(['id_number' => 'Failed to create the ID. Please try again.']);
        }
    }

    /**
     * Display the specified authorized ID
     */
    public function show(AuthorizedId $authorizedId)
    {
        $authorizedId->load(['registeredBy', 'usedBy']);
        return view('authorized-ids.show', compact('authorizedId'));
    }

    /**
     * Show the form for editing the specified authorized ID
     */
    public function edit(AuthorizedId $authorizedId)
    {
        return view('authorized-ids.edit', compact('authorizedId'));
    }

    /**
     * Update the specified authorized ID
     */
    public function update(Request $request, AuthorizedId $authorizedId)
    {
        $request->validate([
            'id_number' => ['required', 'string', 'max:50', Rule::unique('authorized_ids')->ignore($authorizedId->id)],
            'type' => ['required', Rule::in(['student', 'faculty', 'staff'])],
        ]);

        $authorizedId->update([
            'id_number' => $request->id_number,
            'type' => $request->type,
        ]);

        return redirect()->route('authorized-ids.show', $authorizedId)
            ->with('success', 'Authorized ID updated successfully.');
    }

    /**
     * Remove the specified authorized ID
     */
    public function destroy(AuthorizedId $authorizedId)
    {
        if (!$authorizedId->canBeDeleted()) {
            return back()->withErrors(['delete' => 'Cannot delete an ID that has already been used.']);
        }

        try {
            $authorizedId->delete();
            return redirect()->route('authorized-ids.index')
                ->with('success', 'Authorized ID deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['delete' => 'Failed to delete the ID. Please try again.']);
        }
    }

    /**
     * Bulk delete multiple authorized IDs
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:authorized_ids,id',
        ]);

        $ids = $request->ids;
        $deletedCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            $authorizedId = AuthorizedId::find($id);
            
            if (!$authorizedId->canBeDeleted()) {
                $errors[] = "ID '{$authorizedId->id_number}' cannot be deleted as it has been used.";
                continue;
            }

            try {
                $authorizedId->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                $errors[] = "ID '{$authorizedId->id_number}': Failed to delete.";
            }
        }

        if ($deletedCount > 0) {
            $message = "Successfully deleted $deletedCount authorized ID(s).";
            if (!empty($errors)) {
                $message .= " Some IDs could not be deleted due to errors.";
            }
            return redirect()->route('authorized-ids.index')->with('success', $message);
        }

        return back()->withErrors(['bulk_delete' => 'No IDs were deleted.']);
    }

    /**
     * Export authorized IDs to CSV
     */
    public function export(Request $request)
    {
        $query = AuthorizedId::with(['registeredBy', 'usedBy']);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            if ($request->status === 'available') {
                $query->available();
            } elseif ($request->status === 'used') {
                $query->used();
            }
        }

        $authorizedIds = $query->get();

        $filename = 'authorized_ids_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($authorizedIds) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID Number',
                'Type',
                'Status',
                'Used By',
                'Used Date',
                'Notes'
            ]);

            // CSV data
            foreach ($authorizedIds as $id) {
                fputcsv($file, [
                    $id->id_number,
                    $id->type_label,
                    $id->status_label,
                    $id->usedBy?->full_name ?? 'N/A',
                    $id->used_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    $id->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import authorized IDs from CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048', // 2MB max
        ]);

        $file = $request->file('csv_file');
        $successCount = 0;
        $errorRows = [];
        $rowNumber = 0;

        try {
            if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
                // Skip header row
                $header = fgetcsv($handle);
                if (!$header || !in_array('id_number', $header) || !in_array('type', $header)) {
                    fclose($handle);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid CSV format. Requires "id_number" and "type" columns.',
                        'errors' => ['header' => 'Missing required columns: id_number, type']
                    ], 422);
                }

                $validData = [];
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $rowNumber++;
                    if (empty(array_filter($data))) continue; // Skip empty rows

                    $idNumber = trim(($data[0] ?? '') ?: '');
                    $type = trim(strtolower(($data[1] ?? '') ?: ''));

                    if (empty($idNumber) || empty($type)) {
                        $errorRows[] = "Row $rowNumber: Missing id_number or type";
                        continue;
                    }

                    // Validate type
                    if (!in_array($type, ['student', 'faculty', 'staff'])) {
                        $errorRows[] = "Row $rowNumber: Invalid type '$type'. Must be 'student', 'faculty', or 'staff'";
                        continue;
                    }

                    if (strlen($idNumber) < 3 || strlen($idNumber) > 50) {
                        $errorRows[] = "Row $rowNumber: ID number must be 3-50 characters";
                        continue;
                    }

                    // Check if ID already exists
                    $existingId = AuthorizedId::where('id_number', $idNumber)->first();
                    if ($existingId) {
                        $errorRows[] = "Row $rowNumber: ID '$idNumber' already exists";
                        continue;
                    }

                    // Check if ID is assigned to existing user
                    $existingUser = null;
                    if ($type === 'student') {
                        $existingUser = User::where('student_id', $idNumber)->first();
                    } elseif ($type === 'faculty') {
                        $existingUser = User::where('faculty_id', $idNumber)->first();
                    } elseif ($type === 'staff') {
                        $existingUser = User::where('staff_id', $idNumber)->first();
                    }

                    if ($existingUser) {
                        $errorRows[] = "Row $rowNumber: ID '$idNumber' already assigned to user '{$existingUser->full_name}'";
                        continue;
                    }

                    $validData[] = [
                        'id_number' => $idNumber,
                        'type' => $type,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                fclose($handle);

                // Bulk insert valid data
                if (!empty($validData)) {
                    AuthorizedId::insert($validData);
                    $successCount = count($validData);
                }

                return response()->json([
                    'success' => true,
                    'message' => "Successfully imported $successCount IDs. " . (count($errorRows) ? count($errorRows) . " rows had errors." : ''),
                    'success_count' => $successCount,
                    'error_count' => count($errorRows),
                    'errors' => $errorRows
                ]);

            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File processing error: ' . $e->getMessage(),
                'errors' => ['file' => 'Unable to process CSV file']
            ], 500);
        }

        return response()->json([
            'success' => false,
            'message' => 'No valid data found.',
            'errors' => $errorRows
        ], 422);
    }
}
