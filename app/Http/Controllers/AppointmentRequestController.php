<?php

namespace App\Http\Controllers;

use App\Models\AppointmentRequest;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\User;
use App\Models\Appointment;
use App\Services\AppointmentSlotService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AppointmentRequestController extends Controller
{
    /**
     * Get doctor availability (overloaded days: count >= 5).
     * GET /appointments/doctor/{doctor}/availability?month=2025-01
     */
    public function doctorAvailability(Request $request, $doctorId)
    {
        $doctor = User::where('role', 'doctor')->where('id', $doctorId)->firstOrFail();
        $month = $request->get('month', now()->format('Y-m'));
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $slotService = app(AppointmentSlotService::class);
        $overloaded = [];

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $dateStr = $d->toDateString();
            $count = $slotService->countOnDate((int) $doctor->id, $dateStr);
            if ($count >= AppointmentSlotService::MAX_APPOINTMENTS_PER_DOCTOR_PER_DAY) {
                $overloaded[] = [
                    'date' => $dateStr,
                    'count' => $count,
                    'overloaded' => true,
                ];
            }
        }

        return response()->json($overloaded);
    }

    /**
     * Show booking form (public). Optional doctor_number for direct booking.
     */
    public function showBookForm(?string $doctorNumber = null)
    {
        $doctor = null;
        if ($doctorNumber) {
            $doctor = User::where('role', 'doctor')
                ->where('doctor_number', $doctorNumber)
                ->where('status', 'active')
                ->first();
        }
        $doctors = User::where('role', 'doctor')->where('status', 'active')->get();

        return view('appointment.book', compact('doctor', 'doctors', 'doctorNumber'));
    }

    /**
     * Store a new appointment request (public, no auth required).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'session_mode' => 'nullable|in:in_person,online',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:2000',
            'doctor_id' => 'nullable|exists:users,id',
            'doctor_number' => 'nullable|string|max:50',
        ]);

        $doctorId = !empty($validated['doctor_id']) ? (int) $validated['doctor_id'] : null;
        if (!$doctorId && !empty($validated['doctor_number'])) {
            $doctorUser = User::where('role', 'doctor')->where('doctor_number', $validated['doctor_number'])->first();
            if ($doctorUser) {
                $doctorId = (int) $doctorUser->id;
            }
        }
        if (!$doctorId) {
            $msg = 'Please select a doctor.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'errors' => ['doctor_id' => [$msg]],
                ], 422);
            }
            return back()->withInput()->withErrors(['doctor_id' => $msg]);
        }

        // Daily limit: block if doctor already has 5+ appointments on selected date
        $slotService = app(AppointmentSlotService::class);
        $dateStr = Carbon::parse($validated['preferred_date'])->toDateString();
        if ($slotService->isDayFull($doctorId, $dateStr)) {
            $msg = 'Doctor is fully booked for this day.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'errors' => ['preferred_date' => [$msg]],
                ], 422);
            }
            return back()->withInput()->withErrors(['preferred_date' => $msg]);
        }

        try {
            $appointmentRequest = AppointmentRequest::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'session_mode' => $validated['session_mode'] ?? null,
                'preferred_date' => $validated['preferred_date'],
                'preferred_time' => $validated['preferred_time'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'doctor_id' => $doctorId,
            ]);

            Log::info('Appointment request created', [
                'id' => $appointmentRequest->id,
                'email' => $appointmentRequest->email,
                'name' => $appointmentRequest->first_name . ' ' . $appointmentRequest->last_name,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your appointment request has been submitted successfully! We will contact you soon to confirm your appointment.',
                ]);
            }

            return back()->with('success', 'Your appointment request has been submitted successfully! We will contact you soon to confirm your appointment.');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error creating appointment request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $msg = 'Failed to submit appointment request due to a database error. Please try again or contact support.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg, 'errors' => ['error' => [$msg]]], 422);
            }
            return back()->withInput()->withErrors(['error' => $msg]);
        } catch (\Exception $e) {
            Log::error('Error creating appointment request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $msg = 'Failed to submit appointment request. Please try again.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg, 'errors' => ['error' => [$msg]]], 422);
            }
            return back()->withInput()->withErrors(['error' => $msg]);
        }
    }

    /**
     * Display a listing of appointment requests (admin and doctor).
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', AppointmentRequest::class);

        $user = auth()->user();
        $query = AppointmentRequest::with(['doctor', 'patient', 'patientProfile']);

        // If doctor, only show requests assigned to them
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('preferred_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('preferred_date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $appointmentRequests = $query->orderBy('created_at', 'desc')->paginate(20);
        $doctors = User::where('role', 'doctor')->where('status', 'active')->get();

        // Determine if we're in admin or doctor context
        $isAdmin = $user->role === 'admin';

        return view('admin.appointment-requests.index', compact('appointmentRequests', 'doctors', 'isAdmin'));
    }

    /**
     * Display the specified appointment request (admin and doctor).
     */
    public function show(AppointmentRequest $appointmentRequest)
    {
        $this->authorize('view', $appointmentRequest);

        $appointmentRequest->load(['doctor', 'patient', 'patientProfile']);
        $doctors = User::where('role', 'doctor')->where('status', 'active')->get();

        // Determine if we're in admin or doctor context
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';
        
        // Use admin view for both admin and doctor (they can share the same view)
        return view('admin.appointment-requests.show', compact('appointmentRequest', 'doctors', 'isAdmin'));
    }

    /**
     * Approve an appointment request (admin only).
     */
    public function approve(Request $request, AppointmentRequest $appointmentRequest)
    {
        $this->authorize('update', $appointmentRequest);

        $validated = $request->validate([
            'doctor_id' => 'nullable|exists:users,id',
        ]);

        $appointmentRequest->approve($validated['doctor_id'] ?? null);

        return back()->with('success', 'Appointment request approved successfully.');
    }

    /**
     * Reject an appointment request (admin only).
     */
    public function reject(Request $request, AppointmentRequest $appointmentRequest)
    {
        $this->authorize('update', $appointmentRequest);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $appointmentRequest->reject($validated['reason'] ?? null);

        return back()->with('success', 'Appointment request rejected.');
    }

    /**
     * Show form to create patient from appointment request (admin only).
     */
    public function createPatient(AppointmentRequest $appointmentRequest)
    {
        $this->authorize('update', $appointmentRequest);

        $doctors = User::where('role', 'doctor')->where('status', 'active')->get();

        return view('admin.appointment-requests.create-patient', compact('appointmentRequest', 'doctors'));
    }

    /**
     * Create patient from appointment request (admin only).
     */
    public function storePatient(Request $request, AppointmentRequest $appointmentRequest)
    {
        $this->authorize('update', $appointmentRequest);

        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'create_appointment' => 'nullable|boolean',
        ]);

        // Check if email already exists
        if (User::where('email', $appointmentRequest->email)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'A user with this email already exists. Please use a different email or contact the existing user.']);
        }

        $user = auth()->user();
        $doctorId = $user->role === 'admin' && $validated['doctor_id'] 
            ? $validated['doctor_id'] 
            : $user->id;

        // Generate username from name
        $fullName = $appointmentRequest->first_name . ' ' . $appointmentRequest->last_name;
        $username = $this->generateUsername($fullName);
        
        // Ensure username is unique
        while (User::where('username', $username)->exists()) {
            $username = $this->generateUsername($fullName);
        }

        // Generate strong random password
        $password = $this->generateStrongPassword();

        try {
            DB::beginTransaction();

            // Create User account with patient role
            $userAccount = User::create([
                'email' => $appointmentRequest->email,
                'username' => $username,
                'password_hash' => Hash::make($password),
                'role' => 'patient',
                'status' => 'active',
            ]);

            // Create patient
            $patient = Patient::create([
                'doctor_id' => $doctorId,
                'name' => $fullName,
                'email' => $appointmentRequest->email,
                'phone' => $appointmentRequest->phone,
                'password' => $password,
                'status' => 'active',
            ]);

            // Create PatientProfile
            $patientNumber = $this->generatePatientNumber();
            $patientProfile = PatientProfile::create([
                'patient_number' => $patientNumber,
                'user_id' => $userAccount->id,
                'doctor_id' => $doctorId,
                'full_name' => $fullName,
                'phone' => $appointmentRequest->phone,
                'status' => 'ACTIVE',
            ]);

            // Mark appointment request as converted
            $appointmentRequest->markAsConverted($patient->id, $patientProfile->id);

            // Optionally create appointment (with slot validation: max 5/day, no conflict)
            if ($request->boolean('create_appointment')) {
                $slotService = app(AppointmentSlotService::class);
                $dateStr = $appointmentRequest->preferred_date instanceof \Carbon\Carbon
                    ? $appointmentRequest->preferred_date->toDateString()
                    : Carbon::parse($appointmentRequest->preferred_date)->toDateString();
                $timeSlot = $appointmentRequest->preferred_time ?? '09:00';
                $errors = $slotService->validateSlot($doctorId, $dateStr, $timeSlot);
                if (!empty($errors)) {
                    DB::rollBack();
                    return back()->withInput()->withErrors(['error' => implode(' ', $errors)]);
                }
                $date = Carbon::parse($appointmentRequest->preferred_date);
                if ($appointmentRequest->preferred_time && preg_match('/^(\d{1,2}):(\d{2})/', $appointmentRequest->preferred_time, $m)) {
                    $date->setTime((int) $m[1], (int) $m[2], 0);
                } else {
                    $date->setTime(9, 0, 0);
                }
                $bookingFee = (float) config('app.booking_fee', 0);
                Appointment::create([
                    'doctor_id' => $doctorId,
                    'patient_id' => $patientProfile->id,
                    'date' => $date,
                    'time_slot' => $timeSlot,
                    'status' => 'pending',
                    'appointment_type' => 'initial',
                    'session_mode' => $appointmentRequest->session_mode ?? null,
                    'booking_fee' => $bookingFee,
                    'payment_status' => $bookingFee > 0 ? 'pending' : 'paid',
                    'notes' => 'Created from appointment request',
                    'reminder_enabled' => false,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.appointment-requests.show', $appointmentRequest)
                ->with('success', 'Patient created successfully from appointment request!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Appointment request storePatient failed', [
                'user_id' => auth()->id(),
                'appointment_request_id' => $appointmentRequest->id,
                'route' => 'appointment-requests.store-patient',
                'error' => $e->getMessage(),
            ]);
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create patient: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate a strong random password.
     */
    private function generateStrongPassword(): string
    {
        $length = 8;
        $numbers = rand(1000, 9999);
        $uppercase = chr(rand(65, 90));
        $symbol = chr(rand(33, 47));
        $random = Str::random($length);
        
        return $random . $numbers . $uppercase . $symbol;
    }

    /**
     * Generate username from full name.
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
     * Generate a sequential patient number.
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
