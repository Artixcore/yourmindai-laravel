<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistantDoctorAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class AssistantAssignmentController extends Controller
{
    public function index()
    {
        $assignments = AssistantDoctorAssignment::with(['assistant', 'doctor'])->latest()->get();
        $assistants = User::where('role', 'assistant')->where('status', 'active')->get();
        $doctors = User::where('role', 'doctor')->where('status', 'active')->get();
        
        return view('admin.staff.assignments', compact('assignments', 'assistants', 'doctors'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'assistant_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:users,id',
        ]);
        
        $assistant = User::findOrFail($request->assistant_id);
        $doctor = User::findOrFail($request->doctor_id);
        
        if ($assistant->role !== 'assistant') {
            return back()->with('error', 'Selected user is not an assistant.');
        }
        
        if ($doctor->role !== 'doctor') {
            return back()->with('error', 'Selected user is not a doctor.');
        }
        
        AssistantDoctorAssignment::firstOrCreate([
            'assistant_id' => $assistant->id,
            'doctor_id' => $doctor->id,
        ]);
        
        return back()->with('success', 'Assignment created successfully.');
    }
    
    public function destroy(AssistantDoctorAssignment $assignment)
    {
        $assignment->delete();
        
        return back()->with('success', 'Assignment removed successfully.');
    }
}
