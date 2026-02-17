<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientProfile;
use App\Models\SupervisorLink;

class SupervisionDashboardController extends Controller
{
    public function index(Request $request)
    {
        $supervisor = $request->user();

        $children = PatientProfile::whereHas('supervisorLinks', function ($query) use ($supervisor) {
            $query->where('supervisor_id', $supervisor->id);
        })->with(['user', 'doctor'])->get();

        return view('supervision.dashboard', compact('children'));
    }
}
