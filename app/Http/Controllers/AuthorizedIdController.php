<?php

namespace App\Http\Controllers;

use App\Models\AuthorizedId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $query = AuthorizedId::with(['registeredBy', 'usedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'available') {
                $query->available();
            } elseif ($request->status === 'used') {
                $query->used();
            }
        }

        // Search by ID number
        if ($request->filled('search')) {
            $query->where('id_number', 'like', '%' . $request->search . '%');
        }

        $authorizedIds = $query->paginate(20);

        $stats = [
            'total' => AuthorizedId::count(),
            'available' => AuthorizedId::available()->count(),
            'used' => AuthorizedId::used()->count(),
            'students' => AuthorizedId::student()->count(),
            'faculty' => AuthorizedId::faculty()->count(),
        ];

        return view('authorized-ids.index', compact('authorizedIds', 'stats'));
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
            'id_numbers' => 'required|string|min:1',
            'type' => ['required', Rule::in(['student', 'faculty'])],
            'notes' => 'nullable|string|max:500',
        ]);

        $idNumbers = array_filter(
            array_map('trim', explode("\n", $request->id_numbers)),
            function($id) { return !empty($id) && strlen($id) >= 3; }
        );

        // Remove duplicates within the same request
        $idNumbers = array_unique($idNumbers);

        if (empty($idNumbers)) {
            return back()->withErrors(['id_numbers' => 'Please provide at least one valid ID number (minimum 3 characters).']);
        }

        // Check if any IDs already exist in the database
        $existingIds = AuthorizedId::whereIn('id_number', $idNumbers)->pluck('id_number')->toArray();
        if (!empty($existingIds)) {
            return back()->withErrors(['id_numbers' => 'The following IDs already exist: ' . implode(', ', $existingIds)]);
        }

        $successCount = 0;
        $errors = [];

        foreach ($idNumbers as $idNumber) {
            try {
                AuthorizedId::create([
                    'id_number' => $idNumber,
                    'type' => $request->type,
                    'registered_by' => auth()->id(),
                    'notes' => $request->notes,
                ]);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID '$idNumber': Failed to create record.";
            }
        }

        if ($successCount > 0) {
            $message = "Successfully created $successCount authorized ID(s).";
            if (!empty($errors)) {
                $message .= " Some IDs could not be created due to errors.";
            }
            return redirect()->route('authorized-ids.index')->with('success', $message);
        }

        return back()->withErrors(['id_numbers' => 'No IDs were created. Please check the input and try again.']);
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
            'type' => ['required', Rule::in(['student', 'faculty'])],
            'notes' => 'nullable|string|max:500',
        ]);

        $authorizedId->update([
            'id_number' => $request->id_number,
            'type' => $request->type,
            'notes' => $request->notes,
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
                'Registered By',
                'Registered Date',
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
                    $id->registeredBy?->full_name ?? 'N/A',
                    $id->created_at->format('Y-m-d H:i:s'),
                    $id->usedBy?->full_name ?? 'N/A',
                    $id->used_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    $id->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
