<?php

namespace Tests\Feature;

use App\Models\HomeworkAssignment;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClientHomeworkAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--force' => true]);
    }

    protected function createUser(string $email, string $role = 'patient'): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => $email,
            'password' => Hash::make('password123'),
            'password_hash' => Hash::make('password123'),
            'role' => $role,
        ]);
    }

    protected function createPatientProfile(User $doctor, User $patientUser): PatientProfile
    {
        return PatientProfile::create([
            'user_id' => $patientUser->id,
            'doctor_id' => $doctor->id,
            'full_name' => 'Test Patient',
            'phone' => '1234567890',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function client_cannot_access_another_patients_homework(): void
    {
        $doctor = $this->createUser('doctor@test.com', 'doctor');
        $patient1User = $this->createUser('patient1@test.com');
        $patient2User = $this->createUser('patient2@test.com');
        $patient1Profile = $this->createPatientProfile($doctor, $patient1User);
        $this->createPatientProfile($doctor, $patient2User);

        $homework = HomeworkAssignment::create([
            'patient_id' => $patient1Profile->id,
            'assigned_by' => $doctor->id,
            'homework_type' => 'psychotherapy',
            'title' => 'Daily Journal',
            'frequency' => 'daily',
            'start_date' => now(),
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($patient2User)->get(route('client.homework.show', $homework->id));

        $response->assertStatus(404);
    }

    /** @test */
    public function client_cannot_complete_another_patients_homework(): void
    {
        $doctor = $this->createUser('doctor@test.com', 'doctor');
        $patient1User = $this->createUser('patient1@test.com');
        $patient2User = $this->createUser('patient2@test.com');
        $patient1Profile = $this->createPatientProfile($doctor, $patient1User);
        $this->createPatientProfile($doctor, $patient2User);

        $homework = HomeworkAssignment::create([
            'patient_id' => $patient1Profile->id,
            'assigned_by' => $doctor->id,
            'homework_type' => 'psychotherapy',
            'title' => 'Daily Journal',
            'frequency' => 'daily',
            'start_date' => now(),
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($patient2User)->post(route('client.homework.complete', $homework->id), [
            'homework_done' => 'yes',
            'completion_percentage' => 100,
        ]);

        $response->assertStatus(404);
    }
}
