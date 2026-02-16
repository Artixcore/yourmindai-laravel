<?php

namespace Tests\Feature;

use App\Models\PatientProfile;
use App\Models\PsychometricAssessment;
use App\Models\PsychometricReport;
use App\Models\PsychometricScale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PsychometricReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--force' => true]);
    }

    protected function createDoctor(): User
    {
        return User::create([
            'name' => 'Test Doctor',
            'email' => 'doctor@test.com',
            'password' => Hash::make('password123'),
            'password_hash' => Hash::make('password123'),
            'role' => 'doctor',
        ]);
    }

    protected function createPatientUser(): User
    {
        return User::create([
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
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
    public function client_can_generate_report_after_completing_assessment(): void
    {
        $doctor = $this->createDoctor();
        $patientUser = $this->createPatientUser();
        $patientProfile = $this->createPatientProfile($doctor, $patientUser);

        $scale = PsychometricScale::create([
            'name' => 'Test Scale',
            'description' => 'Test',
            'questions' => [['id' => 1, 'text' => 'Q1']],
            'scoring_rules' => ['type' => 'sum'],
            'is_active' => true,
        ]);

        $assessment = PsychometricAssessment::create([
            'patient_profile_id' => $patientProfile->id,
            'scale_id' => $scale->id,
            'assigned_by_doctor_id' => $doctor->id,
            'status' => 'completed',
            'total_score' => 10,
            'interpretation' => 'Normal range',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($patientUser)->post(route('client.assessments.report.generate', $assessment));

        $response->assertRedirect(route('client.assessments.report', $assessment));
        $this->assertDatabaseHas('psychometric_reports', [
            'assessment_id' => $assessment->id,
            'patient_profile_id' => $patientProfile->id,
        ]);
    }

    /** @test */
    public function client_can_view_generated_report(): void
    {
        $doctor = $this->createDoctor();
        $patientUser = $this->createPatientUser();
        $patientProfile = $this->createPatientProfile($doctor, $patientUser);

        $scale = PsychometricScale::create([
            'name' => 'Test Scale',
            'description' => 'Test',
            'questions' => [],
            'is_active' => true,
        ]);

        $assessment = PsychometricAssessment::create([
            'patient_profile_id' => $patientProfile->id,
            'scale_id' => $scale->id,
            'assigned_by_doctor_id' => $doctor->id,
            'status' => 'completed',
            'total_score' => 5,
            'completed_at' => now(),
        ]);

        PsychometricReport::create([
            'assessment_id' => $assessment->id,
            'patient_profile_id' => $patientProfile->id,
            'summary' => 'Test report summary',
            'generated_at' => now(),
        ]);

        $response = $this->actingAs($patientUser)->get(route('client.assessments.report', $assessment));

        $response->assertOk();
        $response->assertSee('Test report summary');
    }
}
