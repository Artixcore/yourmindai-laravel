<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebPatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Patient::class);
        
        $user = $request->user();
        $query = Patient::with('doctor');

        // Filter by doctor if not admin
        if ($user->role !== 'admin') {
            $query->where('doctor_id', $user->id);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('patients.index', [
            'patients' => $patients,
            'search' => $request->search ?? '',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Patient::class);
        return view('patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Patient::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = $request->user();
        $doctorId = $user->role === 'admin' && $request->doctor_id 
            ? $request->doctor_id 
            : $user->id;

        // Generate strong random password
        $password = $this->generateStrongPassword();

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $filename = 'photo_' . time() . '_' . Str::random(10) . '.' . $extension;
            $photoPath = $file->storeAs("patients/{$doctorId}/photos", $filename, 'public');
        }

        // Create patient (password will be hashed by mutator)
        $patient = Patient::create([
            'doctor_id' => $doctorId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $password, // Will be hashed by mutator
            'photo_path' => $photoPath,
            'status' => 'active',
        ]);

        // Flash plain password to session (only once)
        session()->flash('patient_password', $password);
        session()->flash('patient_created', true);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $this->authorize('view', $patient);

        $patient->load('doctor');
        $password = session()->get('patient_password');
        $patientCreated = session()->get('patient_created', false);

        // Clear password from session after showing
        if ($password) {
            session()->forget('patient_password');
            session()->forget('patient_created');
        }

        return view('patients.show', [
            'patient' => $patient,
            'password' => $password,
            'patientCreated' => $patientCreated,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $this->authorize('update', $patient);
        return view('patients.edit', ['patient' => $patient]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email,' . $patient->id,
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Handle photo upload/replacement
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($patient->photo_path) {
                Storage::disk('public')->delete($patient->photo_path);
            }

            // Store new photo
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $filename = 'photo_' . time() . '_' . Str::random(10) . '.' . $extension;
            $photoPath = $file->storeAs("patients/{$patient->doctor_id}/photos", $filename, 'public');
            $validated['photo_path'] = $photoPath;
        }

        $patient->update($validated);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $this->authorize('delete', $patient);

        // Delete photo if exists
        if ($patient->photo_path) {
            Storage::disk('public')->delete($patient->photo_path);
        }

        // Soft delete by setting status to inactive
        $patient->update(['status' => 'inactive']);

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient deactivated successfully!');
    }

    /**
     * Generate a strong random password.
     */
    private function generateStrongPassword(): string
    {
        // Generate 16 character password with complexity
        $length = 8;
        $numbers = rand(1000, 9999);
        $uppercase = chr(rand(65, 90));
        $symbol = chr(rand(33, 47));
        $random = Str::random($length);
        
        return $random . $numbers . $uppercase . $symbol;
    }
}
