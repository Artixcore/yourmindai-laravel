<?php

namespace App\Http\Controllers;

use App\Models\LifestyleLog;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class ClientLifestyleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $today = now()->toDateString();
        $logs = LifestyleLog::where('patient_id', $patient->id)
            ->where('logged_date', $today)
            ->orderBy('type')
            ->orderBy('created_at')
            ->get()
            ->groupBy('type');

        $recentLogs = LifestyleLog::where('patient_id', $patient->id)
            ->where('logged_date', '<', $today)
            ->orderBy('logged_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('client.lifestyle.index', compact('logs', 'recentLogs', 'today'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'type' => 'required|string|in:habit,diet,activity_note,lifestyle_error',
            'label' => 'nullable|string|max:255',
            'value' => 'nullable|string|max:2000',
            'logged_date' => 'required|date',
        ]);

        $validated['patient_id'] = $patient->id;
        LifestyleLog::create($validated);

        return redirect()->route('client.lifestyle.index')
            ->with('success', 'Lifestyle entry logged.');
    }
}
