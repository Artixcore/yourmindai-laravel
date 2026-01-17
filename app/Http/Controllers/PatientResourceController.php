<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientResource;
use App\Http\Requests\StorePatientResourceRequest;
use App\Http\Requests\UpdatePatientResourceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatientResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Patient $patient, Request $request)
    {
        $this->authorize('view', $patient);

        $query = PatientResource::where('patient_id', $patient->id)
            ->with(['doctor', 'session', 'sessionDay'])
            ->orderBy('created_at', 'desc');

        // Filter by session
        if ($request->has('session_id') && $request->session_id) {
            $query->where('session_id', $request->session_id);
        }

        // Filter by session day
        if ($request->has('session_day_id') && $request->session_day_id) {
            $query->where('session_day_id', $request->session_day_id);
        }

        $resources = $query->get();

        // Get sessions and session days for filters
        $sessions = $patient->sessions()->orderBy('created_at', 'desc')->get();
        $sessionDays = collect();
        if ($request->session_id) {
            $sessionDays = \App\Models\SessionDay::where('session_id', $request->session_id)
                ->orderBy('day_date', 'desc')
                ->get();
        }

        // Get all session days grouped by session for modal
        $allSessionDays = \App\Models\SessionDay::whereIn('session_id', $sessions->pluck('id'))
            ->orderBy('day_date', 'desc')
            ->get()
            ->groupBy('session_id')
            ->map(function ($days) {
                return $days->map(function ($day) {
                    return [
                        'id' => $day->id,
                        'day_date' => $day->day_date->format('M d, Y'),
                    ];
                })->values();
            });

        return view('patients.resources', [
            'patient' => $patient,
            'resources' => $resources,
            'sessions' => $sessions,
            'sessionDays' => $sessionDays,
            'allSessionDays' => $allSessionDays,
            'selectedSessionId' => $request->session_id,
            'selectedSessionDayId' => $request->session_day_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientResourceRequest $request, Patient $patient)
    {
        $this->authorize('create', PatientResource::class);
        $this->authorize('view', $patient);

        $data = $request->validated();
        $user = $request->user();

        // Handle file upload if PDF
        if ($request->hasFile('file') && $data['type'] === 'pdf') {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = 'resource_' . time() . '_' . Str::random(10) . '.' . $extension;
            $path = $file->storeAs("patients/{$patient->id}/resources", $filename, 'public');
            
            $data['file_path'] = $path;
        }

        // Normalize YouTube URL if provided
        if ($data['type'] === 'youtube' && isset($data['youtube_url'])) {
            $data['youtube_url'] = $this->normalizeYouTubeUrl($data['youtube_url']);
        } else {
            $data['youtube_url'] = null;
        }

        // Ensure file_path is null for YouTube resources
        if ($data['type'] === 'youtube') {
            $data['file_path'] = null;
        }

        $data['doctor_id'] = $user->id;
        $data['patient_id'] = $patient->id;

        PatientResource::create($data);

        return redirect()
            ->route('patients.resources.index', $patient)
            ->with('success', 'Resource created successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientResourceRequest $request, Patient $patient, PatientResource $resource)
    {
        $this->authorize('update', $resource);

        $data = $request->validated();

        // Handle file upload/replacement if PDF
        if ($request->hasFile('file') && $data['type'] === 'pdf') {
            // Delete old file if exists
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }

            // Store new file
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = 'resource_' . time() . '_' . Str::random(10) . '.' . $extension;
            $path = $file->storeAs("patients/{$patient->id}/resources", $filename, 'public');
            
            $data['file_path'] = $path;
        } elseif ($data['type'] === 'youtube') {
            // If changing to YouTube, delete old file
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
            $data['file_path'] = null;
        } elseif ($data['type'] === 'pdf' && !$request->hasFile('file')) {
            // Keep existing file_path if not uploading new file
            $data['file_path'] = $resource->file_path;
        }

        // Normalize YouTube URL if provided
        if ($data['type'] === 'youtube' && isset($data['youtube_url'])) {
            $data['youtube_url'] = $this->normalizeYouTubeUrl($data['youtube_url']);
        } elseif ($data['type'] === 'pdf') {
            $data['youtube_url'] = null;
        } elseif ($data['type'] === 'youtube' && !isset($data['youtube_url'])) {
            // Keep existing youtube_url if not changing
            $data['youtube_url'] = $resource->youtube_url;
        }

        $resource->update($data);

        return redirect()
            ->route('patients.resources.index', $patient)
            ->with('success', 'Resource updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient, PatientResource $resource)
    {
        $this->authorize('delete', $resource);

        // Delete file from storage if PDF
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return redirect()
            ->route('patients.resources.index', $patient)
            ->with('success', 'Resource deleted successfully!');
    }

    /**
     * Download the resource file.
     */
    public function download(Patient $patient, PatientResource $resource)
    {
        $this->authorize('view', $resource);

        if (!$resource->file_path || !Storage::disk('public')->exists($resource->file_path)) {
            abort(404, 'File not found');
        }

        $path = Storage::disk('public')->path($resource->file_path);
        $filename = Str::slug($resource->title) . '.' . pathinfo($resource->file_path, PATHINFO_EXTENSION);

        return response()->download($path, $filename);
    }

    /**
     * Normalize YouTube URL to standard format.
     */
    private function normalizeYouTubeUrl(string $url): string
    {
        // Extract video ID
        $videoId = null;
        
        // Pattern 1: https://www.youtube.com/watch?v=VIDEO_ID
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // Pattern 2: https://youtu.be/VIDEO_ID
        elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // Pattern 3: https://www.youtube.com/embed/VIDEO_ID
        elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // Pattern 4: https://www.youtube.com/v/VIDEO_ID
        elseif (preg_match('/youtube\.com\/v\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }

        if ($videoId) {
            return "https://www.youtube.com/watch?v={$videoId}";
        }

        return $url; // Return original if we can't parse it
    }
}
