<?php

namespace App\Http\Controllers;

use App\Models\DocumentCode;
use Illuminate\Http\Request;

class DocumentCodeController extends Controller
{
    /**
     * Display the document code selection page.
     */
    public function index()
    {
        return view('document-codes.index');
    }

    /**
     * Show the form for editing a specific document code type.
     */
    public function edit($type)
    {
        if (!in_array($type, ['pds', 'feedback_form'])) {
            abort(404);
        }

        $documentCode = DocumentCode::where('type', $type)->first();

        // If document code doesn't exist, create a default one
        if (!$documentCode) {
            $defaults = [
                'pds' => [
                    'document_code_no' => 'FM-USTP-GCS-02',
                    'revision_no' => '00',
                    'effective_date' => '03.17.25',
                    'page_no' => '1 of 2',
                ],
                'feedback_form' => [
                    'document_code_no' => 'FM-USTP-GCS-01',
                    'revision_no' => '00',
                    'effective_date' => '03.17.25',
                    'page_no' => '1 of 1',
                ]
            ];

            $documentCode = DocumentCode::create(array_merge($defaults[$type], ['type' => $type]));
        }

        $typeName = $type === 'pds' ? 'Personal Data Sheet (PDS)' : 'Feedback Form';

        return view('document-codes.edit', compact('documentCode', 'type', 'typeName'));
    }

    /**
     * Update the document code.
     */
    public function update(Request $request)
    {
        $request->validate([
            'type' => 'required|in:pds,feedback_form',
            'document_code_no' => 'required|string|max:255',
            'revision_no' => 'required|string|max:255',
            'effective_date' => 'required|string|max:255',
        ]);

        $documentCode = DocumentCode::where('type', $request->type)->first();

        if (!$documentCode) {
            $documentCode = new DocumentCode(['type' => $request->type]);
        }

        $documentCode->update($request->only([
            'document_code_no',
            'revision_no',
            'effective_date',
        ]));

        $typeName = $request->type === 'pds' ? 'PDS' : 'Feedback Form';
        return redirect()->route('document-codes.index')->with('success', "{$typeName} document code updated successfully.");
    }
}
