<?php

namespace App\Http\Controllers;

use App\Models\AppContext;
use App\Models\Feedback;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\PatientResource;
use Illuminate\Http\Request;

class ClientResourceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();

        $resources = collect();
        $patientId = null;
        if ($patientProfile) {
            $patientId = Patient::where('email', $user->email)->first()?->id;
            if ($patientId) {
                $resources = PatientResource::where('patient_id', $patientId)
                    ->with(['doctor', 'session', 'sessionDay'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return view('client.resources.index', compact('resources', 'patientProfile'));
    }

    public function storeFeedback(Request $request)
    {
        $user = $request->user();
        $patientProfile = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'feedback_text' => 'required|string|max:5000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $appContext = AppContext::firstOrFail();

        Feedback::create([
            'feedbackable_type' => AppContext::class,
            'feedbackable_id' => $appContext->id,
            'patient_id' => $patientProfile->id,
            'source' => 'self',
            'source_user_id' => $user->id,
            'feedback_text' => $validated['feedback_text'],
            'rating' => $validated['rating'] ?? null,
        ]);

        return redirect()->route('client.resources.index')->with('success', 'Thank you! Your feedback has been submitted.');
    }
}
