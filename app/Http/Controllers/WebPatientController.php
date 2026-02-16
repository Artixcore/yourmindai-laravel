<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\User;
use App\Models\PatientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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
            'email' => 'required|email|unique:patients,email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = $request->user();
        $doctorId = $user->role === 'admin' && $request->doctor_id 
            ? $request->doctor_id 
            : $user->id;

        // Generate username from name
        $username = $this->generateUsername($validated['name']);
        
        // Ensure username is unique
        while (User::where('username', $username)->exists()) {
            $username = $this->generateUsername($validated['name']);
        }

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

        try {
            DB::beginTransaction();

            // Create User account with PATIENT role
            $userAccount = User::create([
                'email' => $validated['email'],
                'username' => $username,
                'password_hash' => Hash::make($password),
                'role' => 'PATIENT',
                'status' => 'active',
            ]);

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

            // Optionally create PatientProfile for consistency with API flow
            $patientNumber = $this->generatePatientNumber();
            PatientProfile::create([
                'patient_number' => $patientNumber,
                'user_id' => $userAccount->id,
                'doctor_id' => $doctorId,
                'full_name' => $validated['name'],
                'status' => 'ACTIVE',
            ]);

            DB::commit();

            // Flash credentials to session (only once)
            session()->flash('patient_username', $username);
            session()->flash('patient_password', $password);
            session()->flash('patient_created', true);

            return redirect()
                ->route('patients.show', $patient)
                ->with('success', 'Patient created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create patient. Please try again.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $this->authorize('view', $patient);

        $patient->load('doctor');
        $username = session()->get('patient_username');
        $password = session()->get('patient_password');
        $patientCreated = session()->get('patient_created', false);

        // Clear credentials from session after showing
        if ($password) {
            session()->forget('patient_username');
            session()->forget('patient_password');
            session()->forget('patient_created');
        }

        return view('patients.show', [
            'patient' => $patient,
            'username' => $username,
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
    public function destroy(Request $request, Patient $patient)
    {
        $this->authorize('delete', $patient);

        $previousStatus = $patient->status;

        // Delete photo if exists
        if ($patient->photo_path) {
            Storage::disk('public')->delete($patient->photo_path);
        }

        // Soft delete by setting status to inactive
        $patient->update(['status' => 'inactive']);

        AuditLog::log(
            $request->user()->id,
            'patient.deactivated',
            'Patient',
            (int) $patient->id,
            ['previous_status' => $previousStatus]
        );

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

    /**
     * Generate username from full name.
     * Format: firstname + first letter of last name + 3 random digits
     */
    private function generateUsername(string $fullName): string
    {
        $parts = explode(' ', strtolower(trim($fullName)));
        $username = $parts[0];
        if (count($parts) > 1) {
            $username .= substr($parts[1], 0, 1);
        }
        $username .= rand(100, 999);
        return $username;
    }

    /**
     * Generate a sequential patient number (PAT-0001, PAT-0002, etc.)
     */
    private function generatePatientNumber(): string
    {
        $lastPatient = PatientProfile::orderBy('patient_number', 'desc')->first();

        $nextNumber = 1;
        if ($lastPatient && $lastPatient->patient_number) {
            if (preg_match('/PAT-(\d+)/', $lastPatient->patient_number, $matches)) {
                $nextNumber = (int) $matches[1] + 1;
            }
        }

        return 'PAT-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
