<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PatientTransferService
{
    /**
     * Transfer a patient to a new doctor. Updates patients.doctor_id and
     * any linked patient_profiles.doctor_id. Logs the action to audit_logs.
     *
     * @param  Patient  $patient  The patient to transfer.
     * @param  int  $newDoctorId  The user ID of the new owning doctor.
     * @param  User  $actor  The user performing the transfer (admin or current doctor).
     * @param  string|null  $reason  Optional reason/notes for the audit log.
     * @return void
     *
     * @throws InvalidArgumentException If new doctor is not found or not a doctor, or same as current.
     */
    public function transfer(Patient $patient, int $newDoctorId, User $actor, ?string $reason = null): void
    {
        $newDoctor = User::where('id', $newDoctorId)->where('role', 'doctor')->first();
        if (! $newDoctor) {
            throw new InvalidArgumentException('The selected user is not a valid doctor.');
        }

        if ((int) $patient->doctor_id === (int) $newDoctorId) {
            throw new InvalidArgumentException('Patient is already assigned to this doctor.');
        }

        $oldDoctorId = (int) $patient->doctor_id;

        DB::transaction(function () use ($patient, $newDoctorId, $actor, $reason, $oldDoctorId) {
            $patient->update(['doctor_id' => $newDoctorId]);

            $linkedProfiles = $this->getLinkedPatientProfiles($patient);
            foreach ($linkedProfiles as $profile) {
                $profile->update(['doctor_id' => $newDoctorId]);
            }

            AuditLog::log(
                $actor->id,
                'patient.transfer',
                'Patient',
                (int) $patient->id,
                [
                    'from_doctor_id' => $oldDoctorId,
                    'to_doctor_id' => $newDoctorId,
                    'reason' => $reason,
                    'patient_profiles_updated' => $linkedProfiles->count(),
                ]
            );
        });
    }

    /**
     * Get PatientProfile records linked to this patient (same user email -> user_id -> profiles).
     */
    private function getLinkedPatientProfiles(Patient $patient)
    {
        $user = User::where('email', $patient->email)->first();
        if (! $user) {
            return collect();
        }

        return PatientProfile::where('user_id', $user->id)->get();
    }
}
