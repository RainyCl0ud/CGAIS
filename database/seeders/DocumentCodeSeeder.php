<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed PDS document code
        \App\Models\DocumentCode::updateOrCreate(
            ['type' => 'pds'],
            [
                'document_code_no' => 'FM-USTP-GCS-02',
                'revision_no' => '00',
                'effective_date' => '03.17.25',
                'page_no' => '1 of 2',
            ]
        );

        // Seed Feedback Form document code
        \App\Models\DocumentCode::updateOrCreate(
            ['type' => 'feedback_form'],
            [
                'document_code_no' => 'FM-USTP-GCS-01',
                'revision_no' => '00',
                'effective_date' => '03.17.25',
                'page_no' => '1 of 1',
            ]
        );
    }
}
