<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\SleepHygieneItem;
use App\Models\SleepHygieneLog;
use App\Http\Requests\StoreSleepHygieneLogRequest;
use Illuminate\Http\Request;

class ClientSleepHygieneController extends Controller
{
    private function getPatientId()
    {
        $user = auth()->user();
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        $patient = Patient::where('email', $user->email)->first();

        if ($patientProfile) {
            return ['id' => $patientProfile->id, 'is_profile' => true];
        }
        if ($patient) {
            return ['id' => $patient->id, 'is_profile' => false];
        }

        return null;
    }

    public function index(Request $request)
    {
        $patientInfo = $this->getPatientId();

        if (!$patientInfo) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Patient profile not found.');
        }

        $logDate = $request->input('date', now()->format('Y-m-d'));
        $items = SleepHygieneItem::where('is_default', true)->orderBy('sort_order')->get();

        $logs = SleepHygieneLog::where('log_date', $logDate)
            ->where($patientInfo['is_profile'] ? 'patient_profile_id' : 'patient_id', $patientInfo['id'])
            ->get()
            ->keyBy('sleep_hygiene_item_id');

        return view('client.sleep-hygiene.index', compact('items', 'logs', 'logDate'));
    }

    public function store(StoreSleepHygieneLogRequest $request)
    {
        $patientInfo = $this->getPatientId();

        if (!$patientInfo) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Patient profile not found.'], 403);
            }
            return redirect()->route('client.dashboard')->with('error', 'Patient profile not found.');
        }

        $validated = $request->validated();

        $log = SleepHygieneLog::where('sleep_hygiene_item_id', $validated['sleep_hygiene_item_id'])
            ->where('log_date', $validated['log_date'])
            ->where($patientInfo['is_profile'] ? 'patient_profile_id' : 'patient_id', $patientInfo['id'])
            ->first();

        $data = [
            'patient_profile_id' => $patientInfo['is_profile'] ? $patientInfo['id'] : null,
            'patient_id' => $patientInfo['is_profile'] ? null : $patientInfo['id'],
            'sleep_hygiene_item_id' => $validated['sleep_hygiene_item_id'],
            'log_date' => $validated['log_date'],
            'is_completed' => (bool) $validated['is_completed'],
            'notes' => $validated['notes'] ?? null,
        ];

        if ($log) {
            $log->update($data);
        } else {
            $log = SleepHygieneLog::create($data);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $item = $log->item;
            $logDate = $validated['log_date'];
            $html = view('client.sleep-hygiene._item', compact('item', 'log', 'logDate'))->render();
            return response()->json([
                'success' => true,
                'message' => 'Sleep hygiene item updated.',
                'html' => $html,
                'target' => '.sleep-hygiene-item[data-item-id=' . $item->id . ']',
                'replace' => true,
            ]);
        }

        return redirect()->route('client.sleep-hygiene.index', ['date' => $validated['log_date']])
            ->with('success', 'Sleep hygiene item updated.');
    }
}
