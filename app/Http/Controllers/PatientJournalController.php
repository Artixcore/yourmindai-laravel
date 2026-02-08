<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientJournalEntry;
use Illuminate\Http\Request;

class PatientJournalController extends Controller
{
    /**
     * Get patient ID for journal (patient_journal_entries.patient_id references patients.id only).
     */
    private function getPatientId()
    {
        $user = auth()->user();
        $patient = Patient::where('email', $user->email)->first();

        return $patient?->id;
    }

    /**
     * Display a listing of journal entries for the authenticated patient
     */
    public function index()
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Journal is not available. No patient record linked to your account.');
        }

        $journalEntries = PatientJournalEntry::where('patient_id', $patientId)
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('patient.journal.index', compact('journalEntries'));
    }

    /**
     * Store a newly created journal entry
     */
    public function store(Request $request)
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Journal is not available. No patient record linked to your account.');
        }

        $request->validate([
            'mood_score' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:5000',
        ]);
        
        PatientJournalEntry::create([
            'patient_id' => $patientId,
            'mood_score' => $request->mood_score,
            'notes' => $request->notes,
            'entry_date' => now(),
        ]);
        
        return redirect()->route('patient.journal.index')
            ->with('success', 'Journal entry created successfully.');
    }
}
