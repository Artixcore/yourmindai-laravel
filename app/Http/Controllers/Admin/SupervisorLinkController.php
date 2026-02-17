<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupervisorLink;
use App\Models\User;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class SupervisorLinkController extends Controller
{
    public function index(Request $request)
    {
        $query = SupervisorLink::with(['supervisor', 'patient.user']);

        if ($request->filled('supervisor_id')) {
            $query->where('supervisor_id', $request->supervisor_id);
        }
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $links = $query->orderBy('created_at', 'desc')->paginate(20);
        $supervisors = User::where('role', 'supervision')->get();
        $patients = PatientProfile::with('user')->get();

        return view('admin.supervisor-links.index', compact('links', 'supervisors', 'patients'));
    }

    public function create()
    {
        $supervisors = User::where('role', 'supervision')->get();
        $patients = PatientProfile::with('user')->get();

        return view('admin.supervisor-links.create', compact('supervisors', 'patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id',
            'patient_id' => 'required|exists:patient_profiles,id',
        ]);

        $user = User::findOrFail($validated['supervisor_id']);
        if (strtolower((string) $user->role) !== 'supervision') {
            return back()->withErrors(['supervisor_id' => 'Selected user must have supervision role.'])->withInput();
        }

        $existing = SupervisorLink::where('supervisor_id', $validated['supervisor_id'])
            ->where('patient_id', $validated['patient_id'])
            ->first();

        if ($existing) {
            return back()->with('error', 'This supervisor is already linked to this client.')->withInput();
        }

        SupervisorLink::create($validated);

        return redirect()->route('admin.supervisor-links.index')
            ->with('success', 'Supervisor linked to client successfully.');
    }

    public function destroy(SupervisorLink $supervisorLink)
    {
        $supervisorLink->delete();

        return redirect()->route('admin.supervisor-links.index')
            ->with('success', 'Supervisor link removed.');
    }
}
