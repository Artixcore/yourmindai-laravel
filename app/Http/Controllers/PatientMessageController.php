<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\PatientMessage;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;

class PatientMessageController extends Controller
{
    /**
     * Get patient ID from authenticated user (patients table ID for PatientMessage).
     */
    private function getPatientId()
    {
        $user = auth()->user();
        
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        $patient = Patient::where('email', $user->email)->first();
        
        if ($patient) {
            return $patient->id;
        }
        
        if ($patientProfile) {
            $patient = Patient::where('email', $user->email)->first();
            return $patient ? $patient->id : null;
        }
        
        return null;
    }

    /**
     * Get doctor_id for the patient (for sending messages).
     */
    private function getPatientDoctorId()
    {
        $user = auth()->user();
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        $patient = Patient::where('email', $user->email)->first();
        
        if ($patient) {
            return $patient->doctor_id;
        }
        if ($patientProfile) {
            return $patientProfile->doctor_id;
        }
        return null;
    }

    /**
     * Display a listing of messages for the authenticated patient
     */
    public function index()
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Patient profile not found.');
        }
        
        $messages = PatientMessage::where('patient_id', $patientId)
            ->with(['doctor'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('patient.messages.index', compact('messages'));
    }

    /**
     * Store a new message (API: patient or doctor sending).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'doctor_id' => 'nullable|exists:users,id',
            'patient_id' => 'nullable|exists:patients,id',
            'sender_type' => 'nullable|in:patient,doctor',
        ]);

        $user = auth()->user();
        $patientId = $this->getPatientId();
        $doctorId = $this->getPatientDoctorId();

        if ($user->role === 'doctor') {
            $doctorId = $user->id;
            $patientId = $validated['patient_id'] ?? null;
            if (!$patientId) {
                return response()->json(['success' => false, 'message' => 'Patient ID required'], 422);
            }
            $senderType = 'doctor';
        } else {
            if (!$patientId || !$doctorId) {
                return response()->json(['success' => false, 'message' => 'Patient or doctor not found'], 422);
            }
            $doctorId = $validated['doctor_id'] ?? $doctorId;
            $senderType = $validated['sender_type'] ?? 'patient';
        }

        $message = PatientMessage::create([
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'sender_type' => $senderType,
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        $receiver = $senderType === 'patient'
            ? \App\Models\User::find($doctorId)
            : $this->resolvePatientUser($patientId);
        $msgPreview = \Str::limit($validated['message'], 50);
        $url = $senderType === 'patient'
            ? route('doctors.messages.index') . '?patient_id=' . $patientId
            : route('patient.messages.index');

        if ($receiver) {
            $receiver->notify(new NewMessageNotification(
                $message,
                'New Message',
                $msgPreview,
                $url,
                $senderType === 'patient' ? null : $user->id,
                $senderType
            ));
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return back()->with('success', 'Message sent.');
    }

    /**
     * Mark a message as read (API).
     */
    public function markAsRead(Request $request, $id)
    {
        $message = PatientMessage::findOrFail($id);
        $user = auth()->user();

        if ($user->role === 'doctor') {
            if ($message->doctor_id != $user->id) {
                abort(403);
            }
        } else {
            $patientId = $this->getPatientId();
            if ($message->patient_id != $patientId) {
                abort(403);
            }
        }

        $message->update(['is_read' => true, 'read_at' => now()]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Message marked as read.');
    }

    private function resolvePatientUser($patientId)
    {
        $patient = Patient::find($patientId);
        if (!$patient) {
            return null;
        }
        return \App\Models\User::where('email', $patient->email)->first();
    }
}
