<?php

namespace Tests\Feature;

use App\Models\HomeworkAssignment;
use App\Models\HomeworkCompletion;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HomeworkContingencyScoringTest extends TestCase
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
    public function client_can_complete_homework_with_scoring_choice(): void
    {
        $doctor = $this->createDoctor();
        $patientUser = $this->createPatientUser();
        $patientProfile = $this->createPatientProfile($doctor, $patientUser);

        $homework = HomeworkAssignment::create([
            'patient_id' => $patientProfile->id,
            'assigned_by' => $doctor->id,
            'homework_type' => 'psychotherapy',
            'title' => 'Daily Journal',
            'frequency' => 'daily',
            'start_date' => now(),
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($patientUser)->post(route('client.homework.complete', $homework->id), [
            'homework_done' => 'yes',
            'completion_percentage' => 100,
            'patient_notes' => 'Done well',
            'scoring_choice' => 'self_action',
        ]);

        $response->assertRedirect();
        $completion = HomeworkCompletion::where('homework_assignment_id', $homework->id)->first();
        $this->assertNotNull($completion);
        $this->assertEquals('self_action', $completion->scoring_choice);
        $this->assertEquals(10, $completion->score_value);
    }

    /** @test */
    public function doctor_can_review_homework_completion(): void
    {
        $doctor = $this->createDoctor();
        $patientUser = $this->createPatientUser();
        $patientProfile = $this->createPatientProfile($doctor, $patientUser);

        $homework = HomeworkAssignment::create([
            'patient_id' => $patientProfile->id,
            'assigned_by' => $doctor->id,
            'homework_type' => 'psychotherapy',
            'title' => 'Daily Journal',
            'frequency' => 'daily',
            'start_date' => now(),
            'status' => 'in_progress',
        ]);

        $completion = HomeworkCompletion::create([
            'homework_assignment_id' => $homework->id,
            'patient_id' => $patientProfile->id,
            'completion_date' => today(),
            'is_completed' => true,
            'completion_percentage' => 100,
            'scoring_choice' => 'others_help',
            'score_value' => 5,
        ]);

        $response = $this->actingAs($doctor)->put(route('patients.homework.completions.review', [
            $patientProfile->id,
            $homework->id,
            $completion->id,
        ]), [
            'score_value' => 10,
        ]);

        $response->assertRedirect();
        $completion->refresh();
        $this->assertEquals($doctor->id, $completion->reviewed_by);
        $this->assertNotNull($completion->reviewed_at);
        $this->assertEquals(10, $completion->score_value);
    }
}
