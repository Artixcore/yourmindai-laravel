<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientResource;
use App\Http\Requests\StorePatientResourceRequest;
use App\Http\Requests\UpdatePatientResourceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PatientResourceController extends Controller
{
    /**
     * Get patient ID from authenticated user
     * Prioritizes patients table since PatientResource uses Patient::class relationship
     */
    private function getPatientId()
    {
        $user = auth()->user();
        
        // Prioritize patients table since PatientResource belongsTo Patient::class
        $patient = Patient::where('email', $user->email)->first();
        if ($patient) {
            return $patient->id;
        }
        
        // Fallback to patient_profiles if patients record doesn't exist
        $patientProfile = \App\Models\PatientProfile::where('user_id', $user->id)->first();
        if ($patientProfile) {
            return $patientProfile->id;
        }
        
        return null;
    }

    /**
     * Display a listing of resources for the authenticated patient
     */
    public function patientIndex(Request $request)
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Patient profile not found.');
        }
        
        $query = PatientResource::where('patient_id', $patientId)
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

        // Get sessions for filters (if patient has sessions)
        $sessions = \App\Models\Session::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $sessionDays = collect();
        if ($request->session_id) {
            $sessionDays = \App\Models\SessionDay::where('session_id', $request->session_id)
                ->orderBy('day_date', 'desc')
                ->get();
        }

        return view('patient.resources.index', compact('resources', 'sessions', 'sessionDays'));
    }

    /**
     * Display a listing of the resource (for doctors).
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
        try {
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
                
                if (!$path) {
                    Log::error('Failed to store patient resource file', [
                        'patient_id' => $patient->id,
                        'filename' => $filename,
                        'user_id' => $user->id,
                    ]);
                    return back()
                        ->withInput()
                        ->withErrors(['file' => 'Failed to upload file. Please try again.']);
                }
                
                // Verify file was actually saved
                if (!Storage::disk('public')->exists($path)) {
                    Log::error('Patient resource file not found after upload', [
                        'patient_id' => $patient->id,
                        'file_path' => $path,
                        'user_id' => $user->id,
                    ]);
                    return back()
                        ->withInput()
                        ->withErrors(['file' => 'File upload verification failed. Please try again.']);
                }
                
                $data['file_path'] = $path;
                Log::info('Patient resource file uploaded successfully', [
                    'patient_id' => $patient->id,
                    'file_path' => $path,
                    'user_id' => $user->id,
                ]);
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

            $resource = PatientResource::create($data);
            
            // Log successful creation
            Log::info('Patient resource created successfully', [
                'resource_id' => $resource->id,
                'patient_id' => $patient->id,
                'type' => $data['type'],
                'file_path' => $resource->file_path ?? null,
                'user_id' => $user->id,
            ]);

            return redirect()
                ->route('patients.resources.index', $patient)
                ->with('success', 'Resource created successfully!');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'You do not have permission to perform this action.']);
        } catch (\Exception $e) {
            Log::error('Failed to create patient resource', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'patient_id' => $patient->id,
                'user_id' => $request->user()->id ?? null,
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while saving the resource. Please try again.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientResourceRequest $request, Patient $patient, PatientResource $resource)
    {
        try {
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
                
                if (!$path) {
                    Log::error('Failed to store patient resource file during update', [
                        'patient_id' => $patient->id,
                        'resource_id' => $resource->id,
                        'filename' => $filename,
                    ]);
                    return back()
                        ->withInput()
                        ->withErrors(['file' => 'Failed to upload file. Please try again.']);
                }
                
                // Verify file was actually saved
                if (!Storage::disk('public')->exists($path)) {
                    Log::error('Patient resource file not found after upload during update', [
                        'patient_id' => $patient->id,
                        'resource_id' => $resource->id,
                        'file_path' => $path,
                    ]);
                    return back()
                        ->withInput()
                        ->withErrors(['file' => 'File upload verification failed. Please try again.']);
                }
                
                $data['file_path'] = $path;
                Log::info('Patient resource file updated successfully', [
                    'patient_id' => $patient->id,
                    'resource_id' => $resource->id,
                    'file_path' => $path,
                ]);
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
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'You do not have permission to perform this action.']);
        } catch (\Exception $e) {
            Log::error('Failed to update patient resource', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'resource_id' => $resource->id ?? null,
                'patient_id' => $patient->id,
                'user_id' => $request->user()->id ?? null,
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while saving the resource. Please try again.']);
        }
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
     * Download the resource file for patients.
     */
    public function patientDownload(PatientResource $resource)
    {
        $user = auth()->user();
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            abort(403, 'Patient profile not found.');
        }
        
        // Verify resource belongs to the authenticated patient
        if ($resource->patient_id != $patientId) {
            abort(403, 'You do not have permission to download this resource.');
        }
        
        // Check if file exists
        if (!$resource->file_path) {
            Log::error('Patient resource download failed: file_path is null', [
                'resource_id' => $resource->id,
                'patient_id' => $patientId,
                'user_id' => $user->id,
            ]);
            abort(404, 'File not found. The file may have been deleted.');
        }
        
        if (!Storage::disk('public')->exists($resource->file_path)) {
            Log::error('Patient resource download failed: file does not exist in storage', [
                'resource_id' => $resource->id,
                'file_path' => $resource->file_path,
                'patient_id' => $patientId,
                'user_id' => $user->id,
            ]);
            abort(404, 'File not found. The file may have been deleted.');
        }

        $path = Storage::disk('public')->path($resource->file_path);
        $filename = Str::slug($resource->title) . '.' . pathinfo($resource->file_path, PATHINFO_EXTENSION);

        return response()->download($path, $filename);
    }

    /**
     * Download the resource file (for doctors/admins).
     */
    public function download(Patient $patient, PatientResource $resource)
    {
        $this->authorize('view', $resource);

        if (!$resource->file_path) {
            Log::error('Resource download failed: file_path is null', [
                'resource_id' => $resource->id,
                'patient_id' => $patient->id,
            ]);
            abort(404, 'File not found. The file may have been deleted.');
        }
        
        if (!Storage::disk('public')->exists($resource->file_path)) {
            Log::error('Resource download failed: file does not exist in storage', [
                'resource_id' => $resource->id,
                'file_path' => $resource->file_path,
                'patient_id' => $patient->id,
            ]);
            abort(404, 'File not found. The file may have been deleted.');
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
