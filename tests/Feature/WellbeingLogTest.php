<?php

namespace Tests\Feature;

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WellbeingLogTest extends TestCase
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
    public function client_can_store_wellbeing_log(): void
    {
        $doctor = $this->createDoctor();
        $patientUser = $this->createPatientUser();
        $patientProfile = $this->createPatientProfile($doctor, $patientUser);

        $response = $this->actingAs($patientUser)->post(route('client.wellbeing.store'), [
            'log_date' => now()->toDateString(),
            'screentime_minutes' => 120,
        ]);

        $response->assertRedirect(route('client.wellbeing.index'));
        $this->assertDatabaseHas('wellbeing_logs', [
            'patient_profile_id' => $patientProfile->id,
            'screentime_minutes' => 120,
        ]);
    }

    /** @test */
    public function client_can_view_wellbeing_index(): void
    {
        $doctor = $this->createDoctor();
        $patientUser = $this->createPatientUser();
        $this->createPatientProfile($doctor, $patientUser);

        $response = $this->actingAs($patientUser)->get(route('client.wellbeing.index'));

        $response->assertOk();
    }
}
