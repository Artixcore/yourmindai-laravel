<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['doctor', 'assistant']);
        
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }
        
        $staff = $query->latest()->paginate(20);
        
        return view('admin.staff.index', compact('staff'));
    }
    
    public function create()
    {
        return view('admin.staff.create');
    }
    
    public function store(StoreStaffRequest $request)
    {
        $user = User::create([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
            'status' => $request->status ?? 'active',
            'password' => Hash::make($request->password),
        ]);
        
        AuditLog::log(auth()->id(), 'staff.created', 'User', $user->id, [
            'role' => $user->role,
            'email' => $user->email,
        ]);
        
        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member created successfully. Password: ' . $request->password);
    }
    
    public function show(User $staff)
    {
        $staff->load(['papers', 'patients', 'sessions']);
        return view('admin.staff.show', compact('staff'));
    }
    
    public function edit(User $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }
    
    public function update(UpdateStaffRequest $request, User $staff)
    {
        $data = $request->only(['username', 'full_name', 'email', 'phone', 'address', 'role', 'status']);
        
        if ($request->filled('full_name')) {
            $data['name'] = $request->full_name;
        }
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $staff->update($data);
        
        AuditLog::log(auth()->id(), 'staff.updated', 'User', $staff->id);
        
        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully.');
    }
    
    public function destroy(User $staff)
    {
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }
        
        $staff->delete();
        
        AuditLog::log(auth()->id(), 'staff.deleted', 'User', $staff->id);
        
        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }
    
    public function toggleStatus(User $staff)
    {
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate yourself.');
        }
        
        $staff->update([
            'status' => $staff->status === 'active' ? 'inactive' : 'active',
        ]);
        
        AuditLog::log(auth()->id(), 'staff.status_toggled', 'User', $staff->id, [
            'new_status' => $staff->status,
        ]);
        
        return back()->with('success', 'Staff status updated successfully.');
    }
}
