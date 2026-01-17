<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientResourceController extends Controller
{
    /**
     * List all resources for authenticated patient
     */
    public function index(Request $request)
    {
        $patient = $request->user();

        $resources = PatientResource::where('patient_id', $patient->id)
            ->with(['session:id,title', 'sessionDay:id,day_date'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $resources->map(function ($resource) {
                $data = [
                    'id' => $resource->id,
                    'type' => $resource->type,
                    'title' => $resource->title,
                    'session' => $resource->session ? [
                        'id' => $resource->session->id,
                        'title' => $resource->session->title,
                    ] : null,
                    'session_day' => $resource->sessionDay ? [
                        'id' => $resource->sessionDay->id,
                        'day_date' => $resource->sessionDay->day_date->format('Y-m-d'),
                    ] : null,
                    'created_at' => $resource->created_at->toISOString(),
                    'updated_at' => $resource->updated_at->toISOString(),
                ];

                // Add file URL for PDFs (signed URL)
                if ($resource->type === 'pdf' && $resource->file_path) {
                    try {
                        // Generate signed URL that expires in 1 hour
                        $data['file_url'] = Storage::disk('public')->temporaryUrl(
                            $resource->file_path,
                            now()->addHour()
                        );
                    } catch (\Exception $e) {
                        // Fallback to regular URL if signed URLs not supported
                        $data['file_url'] = Storage::disk('public')->url($resource->file_path);
                    }
                }

                // Add YouTube URL for YouTube resources
                if ($resource->type === 'youtube' && $resource->youtube_url) {
                    $data['youtube_url'] = $resource->youtube_url;
                    $data['youtube_embed_url'] = $resource->youtube_embed_url;
                }

                return $data;
            }),
        ]);
    }
}
