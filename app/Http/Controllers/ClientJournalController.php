<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\PatientJournalEntry;
use Illuminate\Http\Request;

class ClientJournalController extends Controller
{
    /**
     * Get patient ID for journal (patient_journal_entries.patient_id references patients.id).
     */
    private function getPatientIdForJournal(): ?int
    {
        $user = auth()->user();
        $patient = Patient::where('email', $user->email)->first();
        if ($patient) {
            return $patient->id;
        }
        return null;
    }

    public function index()
    {
        $patientId = $this->getPatientIdForJournal();

        if (!$patientId) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Patient profile not found. Journal is not available.');
        }

        $journalEntries = PatientJournalEntry::where('patient_id', $patientId)
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.journal.index', compact('journalEntries'));
    }

    public function store(Request $request)
    {
        $patientId = $this->getPatientIdForJournal();

        if (!$patientId) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Patient profile not found.');
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

        return redirect()->route('client.journal.index')
            ->with('success', 'Journal entry saved.');
    }
}
