<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentPermission;
use App\Models\PatientProfile;

class ParentPermissionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $parentId = $user->id; // assuming parent user
        
        $permissions = ParentPermission::where('parent_id', $parentId)
            ->with('patient.user')
            ->get();
        
        return view('parent.permissions.index', compact('permissions'));
    }
    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'permission_type' => 'required|string',
            'granted' => 'required|boolean',
        ]);
        
        ParentPermission::updateOrCreate(
            [
                'parent_id' => $request->user()->id,
                'patient_id' => $validated['patient_id'],
                'permission_type' => $validated['permission_type'],
            ],
            ['granted' => $validated['granted']]
        );
        
        return redirect()->route('parent.permissions.index')
            ->with('success', 'Permission updated successfully!');
    }
}
