<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BehaviorContingencyPlan;
use App\Models\BehaviorContingencyCheckin;
use Illuminate\Http\Request;

class BehaviorContingencyController extends Controller
{
    public function index()
    {
        $plans = BehaviorContingencyPlan::with('patient', 'patientProfile', 'createdBy', 'items')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.behavior-contingency.index', compact('plans'));
    }

    public function checkinReviewsIndex()
    {
        $plans = BehaviorContingencyPlan::with('patient', 'patientProfile', 'createdBy')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.behavior-contingency.checkin-reviews', compact('plans'));
    }

    public function checkinReviewsShow(BehaviorContingencyPlan $plan)
    {
        $from = request('from', now()->subDays(7)->format('Y-m-d'));
        $to = request('to', now()->format('Y-m-d'));

        $checkins = $plan->checkins()
            ->with(['planItem', 'reviewer'])
            ->whereBetween('date', [$from, $to])
            ->orderBy('date', 'desc')
            ->orderBy('plan_item_id')
            ->get();

        $plan->load('patient', 'patientProfile', 'items');

        return view('admin.behavior-contingency.checkin-reviews-show', compact('plan', 'checkins', 'from', 'to'));
    }

    public function checkinReviewUpdate(Request $request, BehaviorContingencyCheckin $checkin)
    {
        $plan = $checkin->plan;

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

        return redirect()->route('admin.behavior-contingency.checkin-reviews.show', $plan)
            ->with('success', 'Check-in review saved.');
    }
}
