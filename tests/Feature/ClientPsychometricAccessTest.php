<?php

namespace Tests\Feature;

use App\Models\PatientProfile;
use App\Models\PsychometricAssessment;
use App\Models\PsychometricScale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClientPsychometricAccessTest extends TestCase
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
    public function client_cannot_access_another_patients_assessment(): void
    {
        $doctor = $this->createUser('doctor@test.com', 'doctor');
        $patient1User = $this->createUser('patient1@test.com');
        $patient2User = $this->createUser('patient2@test.com');
        $patient1Profile = $this->createPatientProfile($doctor, $patient1User);
        $this->createPatientProfile($doctor, $patient2User);

        $scale = PsychometricScale::create([
            'name' => 'Test Scale',
            'description' => 'Test',
            'min_score' => 0,
            'max_score' => 10,
            'interpretation_rules' => [],
        ]);

        $assessment = PsychometricAssessment::create([
            'patient_profile_id' => $patient1Profile->id,
            'scale_id' => $scale->id,
            'assigned_by_doctor_id' => $doctor->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($patient2User)->get(route('client.assessments.show', $assessment));

        $response->assertRedirect(route('client.assessments.index'));
        $response->assertSessionHas('error', 'Unauthorized access.');
    }

    /** @test */
    public function client_cannot_generate_report_for_another_patients_assessment(): void
    {
        $doctor = $this->createUser('doctor@test.com', 'doctor');
        $patient1User = $this->createUser('patient1@test.com');
        $patient2User = $this->createUser('patient2@test.com');
        $patient1Profile = $this->createPatientProfile($doctor, $patient1User);
        $this->createPatientProfile($doctor, $patient2User);

        $scale = PsychometricScale::create([
            'name' => 'Test Scale',
            'description' => 'Test',
            'min_score' => 0,
            'max_score' => 10,
            'interpretation_rules' => [],
        ]);

        $assessment = PsychometricAssessment::create([
            'patient_profile_id' => $patient1Profile->id,
            'scale_id' => $scale->id,
            'assigned_by_doctor_id' => $doctor->id,
            'status' => 'completed',
            'total_score' => 5,
        ]);

        $response = $this->actingAs($patient2User)->post(route('client.assessments.report.generate', $assessment));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Unauthorized access.');
    }
}
