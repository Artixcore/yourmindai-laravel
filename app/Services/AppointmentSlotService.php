<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentSlotService
{
    public const MAX_APPOINTMENTS_PER_DOCTOR_PER_DAY = 5;

    /**
     * Check if a doctor has reached max appointments on a given date (excluding cancelled).
     */
    public function isDayFull(int $doctorId, string $date): bool
    {
        $count = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->whereNull('cancelled_at')
            ->whereNotIn('status', ['cancelled'])
            ->count();

        return $count >= self::MAX_APPOINTMENTS_PER_DOCTOR_PER_DAY;
    }

    /**
     * Get number of appointments for doctor on date (for display).
     */
    public function countOnDate(int $doctorId, string $date): int
    {
        return Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->whereNull('cancelled_at')
            ->whereNotIn('status', ['cancelled'])
            ->count();
    }

    /**
     * Check if a specific slot (doctor + date + time_slot) is already taken.
     */
    public function isSlotTaken(int $doctorId, string $date, ?string $timeSlot, ?int $excludeAppointmentId = null): bool
    {
        $query = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->whereNull('cancelled_at')
            ->whereNotIn('status', ['cancelled']);

        if ($timeSlot !== null && $timeSlot !== '') {
            $query->where('time_slot', $timeSlot);
        }

        if ($excludeAppointmentId !== null) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        return $query->exists();
    }

    /**
     * Validate that creating this appointment is allowed (max per day + no conflict).
     */
    public function validateSlot(int $doctorId, string $date, ?string $timeSlot, ?int $excludeAppointmentId = null): array
    {
        $errors = [];

        if ($this->isDayFull($doctorId, $date)) {
            $errors[] = 'This doctor already has the maximum number of appointments (' . self::MAX_APPOINTMENTS_PER_DOCTOR_PER_DAY . ') on this date.';
        }

        if ($timeSlot !== null && $timeSlot !== '' && $this->isSlotTaken($doctorId, $date, $timeSlot, $excludeAppointmentId)) {
            $errors[] = 'This time slot is already booked for this doctor.';
        }

        return $errors;
    }

    /**
     * Whether cancellation is allowed and refundable (more than 48h before appointment).
     */
    public function cancellationRefundable(Appointment $appointment): bool
    {
        $appointmentDateTime = $this->getAppointmentDateTime($appointment);
        // Hours from now until appointment; refundable when >= 48 (appointment is 48+ hours in the future)
        return now()->diffInHours($appointmentDateTime, false) >= 48;
    }

    /**
     * Get appointment start as Carbon (date + time_slot).
     */
    public function getAppointmentDateTime(Appointment $appointment): Carbon
    {
        $date = Carbon::parse($appointment->date);
        if ($appointment->time_slot) {
            if (preg_match('/^(\d{1,2}):(\d{2})/', $appointment->time_slot, $m)) {
                $date->setTime((int) $m[1], (int) $m[2], 0);
            }
        }
        return $date;
    }
}
