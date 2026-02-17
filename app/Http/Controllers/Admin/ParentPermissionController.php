<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParentPermission;
use App\Models\ParentLink;
use App\Models\User;
use App\Models\PatientProfile;

class ParentPermissionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ParentPermission::class);
        
        $query = ParentPermission::with(['parent', 'patient.user']);

        // Apply filters
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('search')) {
            $query->whereHas('parent', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            })->orWhereHas('patient.user', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%');
            });
        }

        $permissions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get parents for filter dropdown
        $parents = User::where('role', 'parent')->get();

        // Get patients for filter dropdown
        $patients = PatientProfile::with('user')->get();

        // Calculate statistics
        $stats = [
            'total' => ParentPermission::count(),
            'permissions_summary' => [
                'can_view_medical' => ParentPermission::where('can_view_medical_records', true)->count(),
                'can_view_sessions' => ParentPermission::where('can_view_session_notes', true)->count(),
                'can_provide_feedback' => ParentPermission::where('can_provide_feedback', true)->count(),
                'can_view_progress' => ParentPermission::where('can_view_progress', true)->count(),
                'can_view_assessments' => ParentPermission::where('can_view_assessments', true)->count(),
                'can_communicate' => ParentPermission::where('can_communicate_with_doctor', true)->count(),
            ],
        ];

        return view('admin.parent-permissions.index', compact('permissions', 'parents', 'patients', 'stats'));
    }

    public function show($id)
    {
        $permission = ParentPermission::with(['parent', 'patient.user'])->findOrFail($id);
        $this->authorize('view', $permission);

        return view('admin.parent-permissions.show', compact('permission'));
    }

    public function edit($id)
    {
        $permission = ParentPermission::with(['parent', 'patient.user'])->findOrFail($id);
        $this->authorize('update', $permission);

        return view('admin.parent-permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $permission = ParentPermission::findOrFail($id);
        $this->authorize('update', $permission);

        $validated = $request->validate([
            'can_view_medical_records' => 'boolean',
            'can_view_session_notes' => 'boolean',
            'can_provide_feedback' => 'boolean',
            'can_view_progress' => 'boolean',
            'can_view_assessments' => 'boolean',
            'can_communicate_with_doctor' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Convert checkbox values
        $validated['can_view_medical_records'] = $request->has('can_view_medical_records');
        $validated['can_view_session_notes'] = $request->has('can_view_session_notes');
        $validated['can_provide_feedback'] = $request->has('can_provide_feedback');
        $validated['can_view_progress'] = $request->has('can_view_progress');
        $validated['can_view_assessments'] = $request->has('can_view_assessments');
        $validated['can_communicate_with_doctor'] = $request->has('can_communicate_with_doctor');

        $permission->update($validated);

        return redirect()->route('admin.parent-permissions.show', $permission->id)
            ->with('success', 'Parent permissions updated successfully.');
    }

    public function destroy($id)
    {
        $permission = ParentPermission::findOrFail($id);
        $this->authorize('delete', $permission);
        
        $permission->delete();

        return redirect()->route('admin.parent-permissions.index')
            ->with('success', 'Parent access revoked successfully.');
    }

    public function create()
    {
        $parents = User::where('role', 'parent')->get();
        $patients = PatientProfile::with('user')->get();

        return view('admin.parent-permissions.create', compact('parents', 'patients'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', ParentPermission::class);
        
        $validated = $request->validate([
            'parent_id' => 'required|exists:users,id',
            'patient_id' => 'required|exists:patient_profiles,id',
            'can_view_medical_records' => 'boolean',
            'can_view_session_notes' => 'boolean',
            'can_provide_feedback' => 'boolean',
            'can_view_progress' => 'boolean',
            'can_view_assessments' => 'boolean',
            'can_communicate_with_doctor' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Convert checkbox values
        $validated['can_view_medical_records'] = $request->has('can_view_medical_records');
        $validated['can_view_session_notes'] = $request->has('can_view_session_notes');
        $validated['can_provide_feedback'] = $request->has('can_provide_feedback');
        $validated['can_view_progress'] = $request->has('can_view_progress');
        $validated['can_view_assessments'] = $request->has('can_view_assessments');
        $validated['can_communicate_with_doctor'] = $request->has('can_communicate_with_doctor');

        // Check if permission already exists
        $existing = ParentPermission::where('parent_id', $validated['parent_id'])
            ->where('patient_id', $validated['patient_id'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withErrors(['error' => 'Permission already exists for this parent-patient relationship.'])
                ->withInput();
        }

        $permission = ParentPermission::create($validated);

        // Sync parent_links so ParentDashboardController and task verification can use it
        ParentLink::firstOrCreate(
            [
                'parent_id' => $validated['parent_id'],
                'patient_id' => $validated['patient_id'],
            ]
        );

        return redirect()->route('admin.parent-permissions.show', $permission->id)
            ->with('success', 'Parent permission created successfully.');
    }
}
