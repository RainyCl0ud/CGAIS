<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PdfImageHelper;

class PdfPreviewController extends Controller
{
    /**
     * Generate a server-side PDF preview for the authenticated user.
     */
    public function previewPdf(Request $request)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return redirect()->route('login');
            }

            // List of candidate models to include in the PDF (silently skip missing models)
            $candidateModels = [
                \App\Models\PersonalDataSheet::class,
                \App\Models\Appointment::class,
                \App\Models\ActivityLog::class,
                \App\Models\FeedbackForm::class,
            ];

            $records = [];

            foreach ($candidateModels as $model) {
                if (! class_exists($model)) {
                    continue;
                }

                try {
                    // Attempt to fetch records that belong to the user (common pattern)
                    if (method_exists($model, 'where')) {
                        $items = $model::where('user_id', $user->id)->get();
                    } else {
                        $items = collect();
                    }

                    foreach ($items as $item) {
                        $arr = $item->toArray();
                        $arr['_embedded_images'] = [];

                        // Find string attributes that look like remote image URLs and fetch them
                        foreach ($arr as $key => $value) {
                            if (! is_string($value)) {
                                continue;
                            }

                            // only attempt to fetch http(s) URLs
                            if (preg_match('/^https?:\/\//i', $value)) {
                                try {
                                    $dataUri = PdfImageHelper::fetchImageAsDataUri($value);
                                    if ($dataUri) {
                                        $arr['_embedded_images'][$key] = $dataUri;
                                    }
                                } catch (\Throwable $e) {
                                    Log::warning("Failed to fetch image for user {$user->id} URL={$value}: {$e->getMessage()}");
                                }
                            }
                        }

                        $records[] = [
                            'model' => class_basename($model),
                            'data' => $arr,
                        ];
                    }
                } catch (\Throwable $e) {
                    Log::warning("Error querying model {$model} for user {$user->id}: {$e->getMessage()}");
                }
            }

            // Load static logos from public_path and convert to data URIs
            $logos = [];
            $staticLogos = [
                'logo' => public_path('images/logo.png'),
                'seal' => public_path('images/seal.png'),
            ];

            foreach ($staticLogos as $key => $path) {
                if (file_exists($path)) {
                    try {
                        $contents = file_get_contents($path);
                        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents);
                        $logos[$key] = "data:{$mime};base64," . base64_encode($contents);
                    } catch (\Throwable $e) {
                        Log::warning("Failed to load static logo {$path}: {$e->getMessage()}");
                    }
                }
            }

            $data = [
                'user' => $user,
                'records' => $records,
                'logos' => $logos,
            ];

            $pdf = Pdf::loadView('pdfs.preview', $data)->setPaper('A4', 'portrait');

            return $pdf->stream('pds_preview.pdf');
        } catch (\Throwable $e) {
            Log::error('PDF generation failed: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'PDF generation failed'], 500);
        }
    }
}
