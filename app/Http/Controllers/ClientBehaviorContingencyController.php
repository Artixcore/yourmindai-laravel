<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\BehaviorContingencyPlan;
use App\Models\BehaviorContingencyCheckin;
use Illuminate\Http\Request;

class ClientBehaviorContingencyController extends Controller
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

    public function index()
    {
        $patientInfo = $this->getPatientId();

        if (!$patientInfo) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Patient profile not found.');
        }

        $plans = BehaviorContingencyPlan::where(
            $patientInfo['is_profile'] ? 'patient_profile_id' : 'patient_id',
            $patientInfo['id']
        )
            ->where('status', 'active')
            ->with('createdBy', 'items')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.behavior-contingency.index', compact('plans'));
    }

    public function show(BehaviorContingencyPlan $plan)
    {
        $this->authorize('view', $plan);

        $patientInfo = $this->getPatientId();
        if (!$patientInfo) {
            return redirect()->route('client.dashboard')->with('error', 'Patient profile not found.');
        }

        $planPatientId = $patientInfo['is_profile'] ? $plan->patient_profile_id : $plan->patient_id;
        if ($planPatientId != $patientInfo['id']) {
            return redirect()->route('client.contingency-plans.index')->with('error', 'Unauthorized access.');
        }

        $plan->load(['items' => fn ($q) => $q->where('is_active', true)], 'createdBy');
        $today = now()->format('Y-m-d');
        $todayCheckins = $plan->checkins()->where('date', $today)->get()->keyBy('plan_item_id');

        return view('client.behavior-contingency.show', compact('plan', 'todayCheckins', 'today'));
    }

    public function storeCheckin(Request $request, BehaviorContingencyPlan $plan)
    {
        $this->authorize('checkin', $plan);

        $request->validate([
            'checkins' => 'required|array',
            'checkins.*.plan_item_id' => 'required|exists:behavior_contingency_plan_items,id',
            'checkins.*.followed' => 'required|boolean',
            'checkins.*.client_note' => 'nullable|string|max:2000',
        ]);

        $today = now()->format('Y-m-d');

        foreach ($request->checkins as $data) {
            $item = $plan->items()->find($data['plan_item_id']);
            if (!$item || !$item->is_active) {
                continue;
            }

            BehaviorContingencyCheckin::updateOrCreate(
                [
                    'plan_item_id' => $item->id,
                    'date' => $today,
                ],
                [
                    'plan_id' => $plan->id,
                    'followed' => (bool) $data['followed'],
                    'client_note' => $data['client_note'] ?? null,
                ]
            );
        }

        return redirect()->route('client.contingency-plans.show', $plan)
            ->with('success', 'Check-in saved successfully.');
    }

    public function updateCheckin(Request $request, BehaviorContingencyPlan $plan, BehaviorContingencyCheckin $checkin)
    {
        $this->authorize('checkin', $plan);

        if ($checkin->plan_id != $plan->id) {
            abort(404);
        }

        $request->validate([
            'followed' => 'required|boolean',
            'client_note' => 'nullable|string|max:2000',
        ]);

        $checkin->update([
            'followed' => (bool) $request->followed,
            'client_note' => $request->client_note,
        ]);

        return redirect()->route('client.contingency-plans.show', $plan)
            ->with('success', 'Check-in updated successfully.');
    }

    public function history(BehaviorContingencyPlan $plan)
    {
        $this->authorize('view', $plan);

        $from = request('from', now()->subDays(30)->format('Y-m-d'));
        $to = request('to', now()->format('Y-m-d'));

        $checkins = $plan->checkins()
            ->with(['planItem', 'reviewer'])
            ->whereBetween('date', [$from, $to])
            ->orderBy('date', 'desc')
            ->orderBy('plan_item_id')
            ->get();

        return view('client.behavior-contingency.history', compact('plan', 'checkins', 'from', 'to'));
    }
}
