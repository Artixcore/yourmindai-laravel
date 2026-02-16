<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\BehaviorContingencyPlan;
use App\Models\BehaviorContingencyPlanItem;
use App\Models\BehaviorContingencyCheckin;
use Illuminate\Http\Request;

class BehaviorContingencyController extends Controller
{
    private function getPatientProfile(Patient $patient)
    {
        if ($patient->email) {
            $user = \App\Models\User::where('email', $patient->email)->first();
            if ($user) {
                $patientProfile = PatientProfile::where('user_id', $user->id)->first();
                if ($patientProfile) {
                    return $patientProfile;
                }
            }
        }

        $patientProfile = PatientProfile::where('doctor_id', $patient->doctor_id)
            ->where(function ($query) use ($patient) {
                $query->where('full_name', $patient->name)
                    ->orWhere('phone', $patient->phone);
            })
            ->first();

        return $patientProfile;
    }

    public function index(Patient $patient)
    {
        $patientProfile = $this->getPatientProfile($patient);

        if (!$patientProfile) {
            return back()->with('error', 'Patient profile not found. Please ensure the patient has a profile.');
        }

        $plans = BehaviorContingencyPlan::where(function ($query) use ($patientProfile, $patient) {
            $query->where('patient_profile_id', $patientProfile->id)
                ->orWhere('patient_id', $patient->id);
        })
            ->when(auth()->user()->role !== 'admin', fn ($q) => $q->where('created_by', auth()->id()))
            ->with('createdBy', 'items')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.patients.behavior-contingency.index', compact('patient', 'patientProfile', 'plans'));
    }

    public function create(Patient $patient)
    {
        $patientProfile = $this->getPatientProfile($patient);
        if (!$patientProfile) {
            return back()->with('error', 'Patient profile not found.');
        }

        return view('doctor.patients.behavior-contingency.create', compact('patient', 'patientProfile'));
    }

    public function store(Request $request, Patient $patient)
    {
        $patientProfile = $this->getPatientProfile($patient);
        if (!$patientProfile) {
            return back()->with('error', 'Patient profile not found.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|in:active,archived',
            'items' => 'required|array|min:1',
            'items.*.target_behavior' => 'required|string|max:1000',
            'items.*.condition_stimulus' => 'required|string|max:1000',
            'items.*.reward_if_followed' => 'nullable|string|max:1000',
            'items.*.punishment_if_not_followed' => 'nullable|string|max:1000',
        ]);

        $plan = BehaviorContingencyPlan::create([
            'patient_id' => $patient->id,
            'patient_profile_id' => $patientProfile->id,
            'created_by' => auth()->id(),
            'title' => $request->title,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'status' => $request->status,
        ]);

        foreach ($request->items as $i => $item) {
            BehaviorContingencyPlanItem::create([
                'plan_id' => $plan->id,
                'sort_order' => $i,
                'target_behavior' => $item['target_behavior'],
                'condition_stimulus' => $item['condition_stimulus'],
                'reward_if_followed' => $item['reward_if_followed'] ?? null,
                'punishment_if_not_followed' => $item['punishment_if_not_followed'] ?? null,
                'is_active' => true,
            ]);
        }

        return redirect()->route('patients.behavior-contingency-plans.index', $patient)
            ->with('success', 'Behavior contingency plan created successfully.');
    }

    public function show(Patient $patient, BehaviorContingencyPlan $plan)
    {
        $this->authorize('view', $plan);

        $patientProfile = $this->getPatientProfile($patient);
        $plan->load('items', 'createdBy', 'checkins');

        return view('doctor.patients.behavior-contingency.show', compact('patient', 'patientProfile', 'plan'));
    }

    public function edit(Patient $patient, BehaviorContingencyPlan $plan)
    {
        $this->authorize('update', $plan);

        $patientProfile = $this->getPatientProfile($patient);
        $plan->load('items');

        return view('doctor.patients.behavior-contingency.edit', compact('patient', 'patientProfile', 'plan'));
    }

    public function update(Request $request, Patient $patient, BehaviorContingencyPlan $plan)
    {
        $this->authorize('update', $plan);

        $request->validate([
            'title' => 'required|string|max:255',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|in:active,archived',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:behavior_contingency_plan_items,id',
            'items.*.target_behavior' => 'required|string|max:1000',
            'items.*.condition_stimulus' => 'required|string|max:1000',
            'items.*.reward_if_followed' => 'nullable|string|max:1000',
            'items.*.punishment_if_not_followed' => 'nullable|string|max:1000',
        ]);

        $plan->update([
            'title' => $request->title,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'status' => $request->status,
        ]);

        $existingIds = [];
        foreach ($request->items as $i => $itemData) {
            if (!empty($itemData['id'])) {
                $item = BehaviorContingencyPlanItem::where('plan_id', $plan->id)->find($itemData['id']);
                if ($item) {
                    $item->update([
                        'sort_order' => $i,
                        'target_behavior' => $itemData['target_behavior'],
                        'condition_stimulus' => $itemData['condition_stimulus'],
                        'reward_if_followed' => $itemData['reward_if_followed'] ?? null,
                        'punishment_if_not_followed' => $itemData['punishment_if_not_followed'] ?? null,
                    ]);
                    $existingIds[] = $item->id;
                    continue;
                }
            }

            $newItem = BehaviorContingencyPlanItem::create([
                'plan_id' => $plan->id,
                'sort_order' => $i,
                'target_behavior' => $itemData['target_behavior'],
                'condition_stimulus' => $itemData['condition_stimulus'],
                'reward_if_followed' => $itemData['reward_if_followed'] ?? null,
                'punishment_if_not_followed' => $itemData['punishment_if_not_followed'] ?? null,
                'is_active' => true,
            ]);
            $existingIds[] = $newItem->id;
        }

        BehaviorContingencyPlanItem::where('plan_id', $plan->id)->whereNotIn('id', $existingIds)->delete();

        return redirect()->route('patients.behavior-contingency-plans.index', $patient)
            ->with('success', 'Behavior contingency plan updated successfully.');
    }

    public function destroy(Patient $patient, BehaviorContingencyPlan $plan)
    {
        $this->authorize('delete', $plan);

        $plan->delete();

        return redirect()->route('patients.behavior-contingency-plans.index', $patient)
            ->with('success', 'Behavior contingency plan deleted successfully.');
    }

    public function checkinReviewsIndex()
    {
        $plans = BehaviorContingencyPlan::with('patient', 'patientProfile', 'createdBy')
            ->when(auth()->user()->role !== 'admin', fn ($q) => $q->where('created_by', auth()->id()))
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.behavior-contingency.checkin-reviews.index', compact('plans'));
    }

    public function checkinReviewsShow(BehaviorContingencyPlan $plan)
    {
        $this->authorize('view', $plan);

        $from = request('from', now()->subDays(7)->format('Y-m-d'));
        $to = request('to', now()->format('Y-m-d'));

        $checkins = $plan->checkins()
            ->with(['planItem', 'reviewer'])
            ->whereBetween('date', [$from, $to])
            ->orderBy('date', 'desc')
            ->orderBy('plan_item_id')
            ->get();

        $plan->load('patient', 'patientProfile', 'items');

        return view('doctor.behavior-contingency.checkin-reviews.show', compact('plan', 'checkins', 'from', 'to'));
    }

    public function checkinReviewUpdate(Request $request, BehaviorContingencyCheckin $checkin)
    {
        $plan = $checkin->plan;
        $this->authorize('update', $plan);

        $request->validate([
            'reviewer_note' => 'nullable|string|max:2000',
            'applied_reward' => 'nullable|string|max:1000',
            'applied_punishment' => 'nullable|string|max:1000',
        ]);

        $checkin->update([
            'reviewer_id' => auth()->id(),
            'reviewer_note' => $request->reviewer_note,
            'applied_reward' => $request->applied_reward,
            'applied_punishment' => $request->applied_punishment,
        ]);

        return redirect()->route('behavior-contingency.checkin-reviews.show', $plan)
            ->with('success', 'Check-in review saved.');
    }
}
