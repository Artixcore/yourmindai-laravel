<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Mail\SessionReportShared;
use App\Services\SessionReportPdfService;
use Illuminate\Http\Request;
use App\Models\SessionReport;
use App\Models\PatientProfile;
use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class SessionReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', SessionReport::class);
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

            $sessions = $patient->sessions()->with('days')->orderBy('created_at', 'desc')->get();
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

        return redirect()->route('session-reports.show', $report->id)
            ->with('success', 'Session report created successfully.');
    }

    public function show($id)
    {
        $report = SessionReport::with(['patient.user', 'session'])->findOrFail($id);
        $this->authorize('view', $report);

        return view('doctor.session-reports.show', compact('report'));
    }

    public function edit($id)
    {
        $report = SessionReport::findOrFail($id);
        $this->authorize('update', $report);

        if ($report->finalized_at) {
            return redirect()->route('session-reports.show', $report->id)
                ->with('error', 'Cannot edit finalized report.');
        }

        $patient = $report->patient;
        $patients = PatientProfile::where('doctor_id', auth()->id())->with('user')->get();

        $sessions = $patient ? $patient->sessions()->with('days')->orderBy('created_at', 'desc')->get() : collect();

        return view('doctor.session-reports.edit', compact('report', 'patients', 'sessions', 'patient'));
    }

    public function update(Request $request, $id)
    {
        $report = SessionReport::findOrFail($id);
        $this->authorize('update', $report);

        if ($report->finalized_at) {
            return redirect()->route('session-reports.show', $report->id)
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

        return redirect()->route('session-reports.show', $report->id)
            ->with('success', 'Session report updated successfully.');
    }

    public function destroy($id)
    {
        $report = SessionReport::findOrFail($id);
        $this->authorize('delete', $report);

        // Cannot delete finalized reports
        if ($report->finalized_at) {
            return redirect()->back()
                ->with('error', 'Cannot delete finalized report.');
        }

        $report->delete();

        return redirect()->route('session-reports.index')
            ->with('success', 'Session report deleted successfully.');
    }

    public function finalize(Request $request, $id)
    {
        $report = SessionReport::findOrFail($id);
        $this->authorize('finalize', $report);

        // Cannot finalize already finalized reports
        if ($report->finalized_at) {
            return redirect()->back()
                ->with('error', 'Report is already finalized.');
        }

        $report->update([
            'finalized_at' => now(),
            'status' => 'finalized',
        ]);

        // Generate and store PDF when finalizing
        app(SessionReportPdfService::class)->generateAndStore($report->fresh());

        return redirect()->route('session-reports.show', $report->id)
            ->with('success', 'Session report finalized successfully. It can no longer be edited.');
    }

    /**
     * Generate PDF for the report and store in system.
     */
    public function generatePdf(Request $request, $id)
    {
        $report = SessionReport::findOrFail($id);
        $this->authorize('view', $report);

        $path = app(SessionReportPdfService::class)->generateAndStore($report->fresh());

        if (!$path) {
            return redirect()->back()->with('error', 'Failed to generate PDF.');
        }

        return redirect()->back()->with('success', 'PDF generated and stored successfully.');
    }

    /**
     * Download the report PDF (generates if not yet stored).
     */
    public function downloadPdf(Request $request, $id)
    {
        $report = SessionReport::with(['patient.user', 'createdByDoctor'])->findOrFail($id);
        $this->authorize('view', $report);

        $service = app(SessionReportPdfService::class);
        if (!$report->pdf_path || !Storage::disk('public')->exists($report->pdf_path)) {
            $service->generateAndStore($report);
            $report->refresh();
        }

        if (!$report->pdf_path || !Storage::disk('public')->exists($report->pdf_path)) {
            return redirect()->back()->with('error', 'Could not generate or find PDF.');
        }

        $filename = 'session-report-' . $report->id . '-' . $report->title . '.pdf';
        $filename = preg_replace('/[^a-zA-Z0-9\-_.]/', '-', $filename);

        return Storage::disk('public')->download($report->pdf_path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function share(Request $request, $id)
    {
        $report = SessionReport::findOrFail($id);
        $this->authorize('update', $report);

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

    /**
     * Get a signed URL for sharing the report PDF (valid 7 days).
     */
    public function shareLink($report)
    {
        $report = $report instanceof SessionReport ? $report : SessionReport::findOrFail($report);
        $this->authorize('view', $report);

        $link = URL::temporarySignedRoute(
            'report.download-public',
            now()->addDays(7),
            ['report' => $report->id]
        );

        return response()->json(['url' => $link]);
    }

    /**
     * Send report via email (to patient or custom address).
     */
    public function sendViaEmail(Request $request, $report)
    {
        $report = $report instanceof SessionReport ? $report : SessionReport::with(['patient.user', 'session'])->findOrFail($report);
        $this->authorize('view', $report);

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $service = app(SessionReportPdfService::class);
        if (!$report->pdf_path || !Storage::disk('public')->exists($report->pdf_path)) {
            $service->generateAndStore($report);
            $report->refresh();
        }

        $shareLink = URL::temporarySignedRoute(
            'report.download-public',
            now()->addDays(7),
            ['report' => $report->id]
        );

        Mail::to($validated['email'])->send(new SessionReportShared($report, $shareLink));

        return redirect()->back()->with('success', 'Report link has been sent to ' . $validated['email']);
    }
}
