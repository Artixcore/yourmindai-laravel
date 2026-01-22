<?php

namespace App\Http\Controllers;

use App\Models\PatientMessage;
use App\Models\Patient;
use Illuminate\Http\Request;

class DoctorMessageController extends Controller
{
    /**
     * Display a listing of messages for the authenticated doctor
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get all messages where doctor is involved
        $query = PatientMessage::where('doctor_id', $user->id)
            ->with(['patient', 'doctor'])
            ->orderBy('created_at', 'desc');

        // Filter by patient if provided
        if ($request->has('patient_id') && $request->patient_id) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by read status
        if ($request->has('is_read') && $request->is_read !== '') {
            $query->where('is_read', $request->is_read === '1');
        }

        $messages = $query->get();

        // Group messages by patient
        $messagesByPatient = $messages->groupBy('patient_id');

        // Get unique patients
        $patientIds = $messages->pluck('patient_id')->unique();
        $patients = Patient::whereIn('id', $patientIds)->get()->keyBy('id');

        // Count unread messages per patient
        $unreadCounts = [];
        foreach ($messagesByPatient as $patientId => $patientMessages) {
            $unreadCounts[$patientId] = $patientMessages->where('is_read', false)
                ->where('sender_type', 'patient')
                ->count();
        }

        return view('doctors.messages.index', [
            'messages' => $messages,
            'messagesByPatient' => $messagesByPatient,
            'patients' => $patients,
            'unreadCounts' => $unreadCounts,
            'filterPatientId' => $request->patient_id,
            'filterIsRead' => $request->is_read,
        ]);
    }
}
