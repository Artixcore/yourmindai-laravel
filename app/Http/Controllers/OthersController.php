<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Referral;

class OthersController extends Controller
{
    public function referrals(Request $request)
    {
        $user = $request->user();
        
        // Get all referrals sent to this expert/specialist
        $referrals = Referral::where('referred_to', $user->id)
            ->with('patient.user', 'referredBy')
            ->orderBy('referred_at', 'desc')
            ->paginate(15);

        // Calculate statistics
        $stats = [
            'total' => Referral::where('referred_to', $user->id)->count(),
            'pending' => Referral::where('referred_to', $user->id)->where('status', 'pending')->count(),
            'active' => Referral::where('referred_to', $user->id)->whereIn('status', ['accepted', 'in_progress'])->count(),
            'completed' => Referral::where('referred_to', $user->id)->where('status', 'completed')->count(),
        ];

        return view('others.referrals.index', compact('referrals', 'stats'));
    }

    public function showReferral(Request $request, Referral $referral)
    {
        $user = $request->user();
        
        // Ensure the referral is for this expert
        if ($referral->referred_to !== $user->id) {
            abort(403, 'Unauthorized access to this referral');
        }

        $referral->load('patient.user', 'referredBy', 'backReferrals');

        return view('others.referrals.show', compact('referral'));
    }

    public function respondToReferral(Request $request, Referral $referral)
    {
        $user = $request->user();
        
        // Ensure the referral is for this expert and is pending
        if ($referral->referred_to !== $user->id) {
            abort(403, 'Unauthorized access to this referral');
        }

        if ($referral->status !== 'pending') {
            return redirect()
                ->route('others.referrals.show', $referral)
                ->with('error', 'This referral has already been responded to.');
        }

        $validated = $request->validate([
            'action' => 'required|in:accept,decline',
            'response_notes' => 'required|string',
        ]);

        if ($validated['action'] === 'accept') {
            $referral->accept($validated['response_notes']);
            $message = 'Referral accepted successfully!';
        } else {
            $referral->decline($validated['response_notes']);
            $message = 'Referral declined.';
        }

        return redirect()
            ->route('others.referrals.show', $referral)
            ->with('success', $message);
    }

    public function updateReferral(Request $request, Referral $referral)
    {
        $user = $request->user();
        
        // Ensure the referral is for this expert
        if ($referral->referred_to !== $user->id) {
            abort(403, 'Unauthorized access to this referral');
        }

        $validated = $request->validate([
            'status' => 'required|in:in_progress,completed',
            'response_notes' => 'required|string',
        ]);

        if ($validated['status'] === 'completed') {
            $referral->complete($validated['response_notes']);
        } else {
            $referral->updateProgress($validated['status'], $validated['response_notes']);
        }

        return redirect()
            ->route('others.referrals.show', $referral)
            ->with('success', 'Referral updated successfully!');
    }
}
