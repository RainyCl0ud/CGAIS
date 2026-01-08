<?php

namespace App\Http\Controllers;

use App\Models\DocumentCode;
use Illuminate\Http\Request;

class DocumentCodeController extends Controller
{
    /** 
     * Display the document code management page.
     */
    public function index()
    {
        $documentCode = DocumentCode::first();

        // If no document code exists, create a default one
        if (!$documentCode) {
            $documentCode = DocumentCode::create([
                'document_code_no' => 'FM-USTP-GCS-02',
                'revision_no' => '00',
                'effective_date' => '03.17.25',
                'page_no' => '1 of 2',
            ]);
        }

        return view('document-codes.index', compact('documentCode'));
    }

    /**
     * Update the document code.
     */
    public function update(Request $request)
    {
        $request->validate([
            'document_code_no' => 'required|string|max:255',
            'revision_no' => 'required|string|max:255',
            'effective_date' => 'required|string|max:255',
            'page_no' => 'required|string|max:255',
        ]);

        $documentCode = DocumentCode::first();

        if (!$documentCode) {
            $documentCode = new DocumentCode();
        }

        $documentCode->update($request->only([
            'document_code_no',
            'revision_no',
            'effective_date',
            'page_no',
        ]));

        return redirect()->route('document-codes.index')->with('success', 'Document code updated successfully.');
    }
}
