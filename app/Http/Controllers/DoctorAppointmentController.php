<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class DoctorAppointmentController extends Controller
{
    /**
     * Display a listing of appointments for the authenticated doctor
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Appointment::where('doctor_id', $user->id)
            ->with(['patient.user', 'doctor'])
            ->orderBy('date', 'asc')
            ->orderBy('time_slot', 'asc');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $appointments = $query->get();

        // Group appointments by status
        $upcoming = $appointments->filter(function ($appointment) {
            return $appointment->date >= now() && $appointment->status !== 'cancelled';
        });

        $past = $appointments->filter(function ($appointment) {
            return $appointment->date < now() || $appointment->status === 'cancelled';
        });

        return view('doctors.appointments.index', [
            'appointments' => $appointments,
            'upcoming' => $upcoming,
            'past' => $past,
            'filterStatus' => $request->status,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
        ]);
    }
}
