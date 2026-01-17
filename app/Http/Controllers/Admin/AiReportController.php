<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiReport;
use App\Models\Patient;
use App\Models\User;
use App\Jobs\GeneratePatientReportJob;
use App\Jobs\GenerateDoctorReportJob;
use App\Jobs\GenerateClinicReportJob;
use Illuminate\Http\Request;

class AiReportController extends Controller
{
    public function index(Request $request)
    {
        $query = AiReport::with(['patient', 'session', 'doctor', 'requestedBy']);
        
        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }
        
        $reports = $query->latest()->paginate(20);
        
        return view('admin.ai-reports.index', compact('reports'));
    }
    
    public function show($id)
    {
        $aiReport = AiReport::with(['patient', 'session', 'doctor', 'requestedBy'])->findOrFail($id);
        return view('admin.ai-reports.show', compact('aiReport'));
    }
    
    public function generatePatient(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'days' => 'nullable|integer|min:1|max:365',
        ]);
        
        $patient = Patient::findOrFail($request->patient_id);
        
        $report = AiReport::create([
            'scope' => 'patient',
            'patient_id' => $patient->id,
            'requested_by' => auth()->id(),
            'status' => 'queued',
            'model' => config('openai.model', 'gpt-4o-mini'),
        ]);
        
        GeneratePatientReportJob::dispatch($report->id, $patient->id, $request->days ?? 30);
        
        return redirect()->route('admin.ai-reports.show', $report)
            ->with('success', 'Patient report generation queued.');
    }
    
    public function generateDoctor(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
        ]);
        
        $doctor = User::findOrFail($request->doctor_id);
        
        if ($doctor->role !== 'doctor') {
            return back()->with('error', 'Selected user is not a doctor.');
        }
        
        $report = AiReport::create([
            'scope' => 'doctor',
            'doctor_id' => $doctor->id,
            'requested_by' => auth()->id(),
            'status' => 'queued',
            'model' => config('openai.model', 'gpt-4o-mini'),
        ]);
        
        GenerateDoctorReportJob::dispatch($report->id, $doctor->id);
        
        return redirect()->route('admin.ai-reports.show', $report)
            ->with('success', 'Doctor report generation queued.');
    }
    
    public function generateClinic(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);
        
        $report = AiReport::create([
            'scope' => 'clinic',
            'requested_by' => auth()->id(),
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'status' => 'queued',
            'model' => config('openai.model', 'gpt-4o-mini'),
        ]);
        
        GenerateClinicReportJob::dispatch($report->id, $request->date_from, $request->date_to);
        
        return redirect()->route('admin.ai-reports.show', $report)
            ->with('success', 'Clinic report generation queued.');
    }
    
    public function regenerate($id)
    {
        $aiReport = AiReport::findOrFail($id);
        
        if ($aiReport->status === 'running') {
            return back()->with('error', 'Report is currently being generated.');
        }
        
        $aiReport->update(['status' => 'queued']);
        
        switch ($aiReport->scope) {
            case 'patient':
                if ($aiReport->patient_id) {
                    GeneratePatientReportJob::dispatch($aiReport->id, $aiReport->patient_id, 30);
                }
                break;
            case 'doctor':
                if ($aiReport->doctor_id) {
                    GenerateDoctorReportJob::dispatch($aiReport->id, $aiReport->doctor_id);
                }
                break;
            case 'clinic':
                if ($aiReport->date_from && $aiReport->date_to) {
                    GenerateClinicReportJob::dispatch($aiReport->id, $aiReport->date_from, $aiReport->date_to);
                }
                break;
        }
        
        return back()->with('success', 'Report regeneration queued.');
    }
}
