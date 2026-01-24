<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SessionReport;
use App\Models\PatientProfile;
use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;

class SessionReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = SessionReport::where('created_by', $user->id)
            ->with(['patient.user', 'session']);

        // Apply filters
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
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

        if ($request->filled('finalized')) {
            if ($request->finalized == 'yes') {
                $query->whereNotNull('finalized_at');
            } else {
                $query->whereNull('finalized_at');
            }
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get doctor's patients for filter dropdown
        $patients = PatientProfile::where('doctor_id', $user->id)->with('user')->get();

        // Calculate statistics
        $stats = [
            'total' => SessionReport::where('created_by', $user->id)->count(),
            'finalized' => SessionReport::where('created_by', $user->id)->whereNotNull('finalized_at')->count(),
            'draft' => SessionReport::where('created_by', $user->id)->whereNull('finalized_at')->count(),
            'this_week' => SessionReport::where('created_by', $user->id)
                ->where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
        ];

        return view('doctor.session-reports.index', compact('reports', 'patients', 'stats'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $patientId = $request->input('patient_id');
        
        // Get doctor's patients
        $patients = PatientProfile::where('doctor_id', $user->id)->with('user')->get();
        
        // If patient_id provided, get their sessions
        $sessions = null;
        $patient = null;
        if ($patientId) {
            $patient = PatientProfile::where('id', $patientId)
                ->where('doctor_id', $user->id)
                ->with('user')
                ->firstOrFail();
            
        $sessions = Session::where('doctor_id', $user->id)
            ->whereHas('days', function($q) use ($patientId) {
                $q->where('patient_id', $patientId);
            })
            ->get();
        }

        return view('doctor.session-reports.create', compact('patients', 'sessions', 'patient'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'session_id' => 'nullable|exists:sessions,id',
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'assessments_summary' => 'nullable|string',
            'techniques_assigned' => 'nullable|string',
            'progress_notes' => 'nullable|string',
            'next_steps' => 'nullable|string',
            'shared_with_patient' => 'boolean',
            'shared_with_parents' => 'boolean',
            'shared_with_others' => 'boolean',
            'status' => 'required|in:draft,completed,reviewed',
        ]);

        // Verify doctor has access to this patient
        $patient = PatientProfile::where('id', $validated['patient_id'])
            ->where('doctor_id', $user->id)
            ->firstOrFail();

        $validated['created_by'] = $user->id;
        $validated['shared_with_patient'] = $request->has('shared_with_patient');
        $validated['shared_with_parents'] = $request->has('shared_with_parents');
        $validated['shared_with_others'] = $request->has('shared_with_others');

        $report = SessionReport::create($validated);

        return redirect()->route('doctor.session-reports.show', $report->id)
            ->with('success', 'Session report created successfully.');
    }

    public function show($id)
    {
        $user = auth()->user();
        
        $report = SessionReport::with(['patient.user', 'session'])
            ->findOrFail($id);

        // Verify doctor owns this report or is admin
        if ($report->created_by != $user->id && $user->role != 'admin') {
            abort(403, 'Unauthorized access to report');
        }

        return view('doctor.session-reports.show', compact('report'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        
        $report = SessionReport::findOrFail($id);

        // Verify doctor owns this report
        if ($report->created_by != $user->id) {
            abort(403, 'Unauthorized access to report');
        }

        // Cannot edit finalized reports
        if ($report->finalized_at) {
            return redirect()->route('doctor.session-reports.show', $report->id)
                ->with('error', 'Cannot edit finalized report.');
        }

        $patient = $report->patient;
        $patients = PatientProfile::where('doctor_id', $user->id)->with('user')->get();
        
        $sessions = Session::where('doctor_id', $user->id)
            ->whereHas('days', function($q) use ($patient) {
                $q->where('patient_id', $patient->id);
            })
            ->get();

        return view('doctor.session-reports.edit', compact('report', 'patients', 'sessions', 'patient'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        $report = SessionReport::findOrFail($id);

        // Verify doctor owns this report
        if ($report->created_by != $user->id) {
            abort(403, 'Unauthorized access to report');
        }

        // Cannot edit finalized reports
        if ($report->finalized_at) {
            return redirect()->route('doctor.session-reports.show', $report->id)
                ->with('error', 'Cannot edit finalized report.');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'session_id' => 'nullable|exists:sessions,id',
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'assessments_summary' => 'nullable|string',
            'techniques_assigned' => 'nullable|string',
            'progress_notes' => 'nullable|string',
            'next_steps' => 'nullable|string',
            'shared_with_patient' => 'boolean',
            'shared_with_parents' => 'boolean',
            'shared_with_others' => 'boolean',
            'status' => 'required|in:draft,completed,reviewed',
        ]);

        $validated['shared_with_patient'] = $request->has('shared_with_patient');
        $validated['shared_with_parents'] = $request->has('shared_with_parents');
        $validated['shared_with_others'] = $request->has('shared_with_others');

        $report->update($validated);

        return redirect()->route('doctor.session-reports.show', $report->id)
            ->with('success', 'Session report updated successfully.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        
        $report = SessionReport::findOrFail($id);

        // Verify doctor owns this report
        if ($report->created_by != $user->id) {
            abort(403, 'Unauthorized access to report');
        }

        // Cannot delete finalized reports
        if ($report->finalized_at) {
            return redirect()->back()
                ->with('error', 'Cannot delete finalized report.');
        }

        $report->delete();

        return redirect()->route('doctor.session-reports.index')
            ->with('success', 'Session report deleted successfully.');
    }

    public function finalize(Request $request, $id)
    {
        $user = auth()->user();
        
        $report = SessionReport::findOrFail($id);

        // Verify doctor owns this report
        if ($report->created_by != $user->id) {
            abort(403, 'Unauthorized access to report');
        }

        // Cannot finalize already finalized reports
        if ($report->finalized_at) {
            return redirect()->back()
                ->with('error', 'Report is already finalized.');
        }

        $report->update([
            'finalized_at' => now(),
            'status' => 'completed'
        ]);

        return redirect()->route('doctor.session-reports.show', $report->id)
            ->with('success', 'Session report finalized successfully. It can no longer be edited.');
    }

    public function share(Request $request, $id)
    {
        $user = auth()->user();
        
        $report = SessionReport::findOrFail($id);

        // Verify doctor owns this report
        if ($report->created_by != $user->id) {
            abort(403, 'Unauthorized access to report');
        }

        $validated = $request->validate([
            'shared_with_patient' => 'boolean',
            'shared_with_parents' => 'boolean',
            'shared_with_others' => 'boolean',
        ]);

        $validated['shared_with_patient'] = $request->has('shared_with_patient');
        $validated['shared_with_parents'] = $request->has('shared_with_parents');
        $validated['shared_with_others'] = $request->has('shared_with_others');

        $report->update($validated);

        return redirect()->back()
            ->with('success', 'Sharing settings updated successfully.');
    }
}
