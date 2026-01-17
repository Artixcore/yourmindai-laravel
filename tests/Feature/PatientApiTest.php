<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Session;
use App\Models\SessionDay;
use App\Models\PatientResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PatientApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate', ['--force' => true]);
    }

    /** @test */
    public function patient_can_login_with_valid_credentials()
    {
        $doctor = User::create([
            'name' => 'Test Doctor',
            'email' => 'doctor@test.com',
            'password' => Hash::make('password123'),
            'role' => 'DOCTOR',
        ]);
        $patient = Patient::create([
            'doctor_id' => $doctor->id,
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/patient/login', [
            'email' => 'patient@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'patient' => ['id', 'name', 'email', 'phone', 'photo_url', 'status'],
                    'token',
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);

        $this->assertNotEmpty($response->json('data.token'));
    }

    /** @test */
    public function patient_cannot_login_with_invalid_credentials()
    {
        $doctor = User::factory()->create(['role' => 'DOCTOR']);
        $patient = Patient::create([
            'doctor_id' => $doctor->id,
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/patient/login', [
            'email' => 'patient@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'error' => 'Invalid credentials.',
            ]);
    }

    /** @test */
    public function inactive_patient_cannot_login()
    {
        $doctor = User::create([
            'name' => 'Test Doctor',
            'email' => 'doctor@test.com',
            'password' => Hash::make('password123'),
            'role' => 'DOCTOR',
        ]);
        $patient = Patient::create([
            'doctor_id' => $doctor->id,
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => Hash::make('password123'),
            'status' => 'inactive',
        ]);

        $response = $this->postJson('/api/patient/login', [
            'email' => 'patient@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => 'Your account is inactive.',
            ]);
    }

    /** @test */
    public function patient_can_get_own_profile()
    {
        $doctor = User::create([
            'name' => 'Test Doctor',
            'email' => 'doctor@test.com',
            'password' => Hash::make('password123'),
            'role' => 'DOCTOR',
        ]);
        $patient = Patient::create([
            'doctor_id' => $doctor->id,
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $token = $patient->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/patient/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'email', 'phone', 'photo_url', 'status', 'doctor_id'],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $patient->id,
                    'email' => 'patient@test.com',
                ],
            ]);
    }

    /** @test */
    public function patient_can_logout()
    {
        $doctor = User::create([
            'name' => 'Test Doctor',
            'email' => 'doctor@test.com',
            'password' => Hash::make('password123'),
            'role' => 'DOCTOR',
        ]);
        $patient = Patient::create([
            'doctor_id' => $doctor->id,
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $token = $patient->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/patient/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logged out successfully.',
            ]);

        // Verify token is deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $patient->id,
            'tokenable_type' => Patient::class,
        ]);
    }

    /** @test */
    public function patient_can_list_own_sessions()
    {
        $doctor = User::create([
            'name' => 'Test Doctor',
            'email' => 'doctor@test.com',
            'password' => Hash::make('password123'),
            'role' => 'DOCTOR',
        ]);
        $patient = Patient::create([
            'doctor_id' => $doctor->id,
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $session1 = Session::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'title' => 'Session 1',
            'status' => 'active',
        ]);

        $session2 = Session::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'title' => 'Session 2',
            'status' => 'active',
        ]);

        // Create another patient's session (should not appear)
        $otherPatient = Patient::create([
            'doctor_id' => $doctor->id,
            'name' => 'Other Patient',
            'email' => 'other@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Session::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $otherPatient->id,
            'title' => 'Other Session',
            'status' => 'active',
        ]);

        $token = $patient->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/patient/sessions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'notes', 'status', 'doctor', 'days_count'],
                ],
            ])
            ->assertJsonCount(2, 'data')
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function patient_cannot_access_other_patient_sessions()
    {
        $doctor = User::create([
            'name' => 'Test Doctor',
            'email' => 'doctor@test.com',
            'password' => Hash::make('password123'),
            'role' => 'DOCTOR',
        ]);
        $patient1 = Patient::create([
            'doctor_id' => $doctor->id,
            'name' => 'Patient 1',
            'email' => 'patient1@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $patient2 = Patient::create([
            'doctor_id' => $doctor->id,
            'name' => 'Patient 2',
            'email' => 'patient2@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $session2 = Session::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient2->id,
            'title' => 'Patient 2 Session',
            'status' => 'active',
        ]);

        $token = $patient1->createToken('test-token')->plainTextToken;

        // Patient 1 tries to access Patient 2's session
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/patient/sessions/{$session2->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function patient_can_get_own_resources()
    {
        $doctor = User::create([
            'name' => 'Test Doctor',
            'email' => 'doctor@test.com',
            'password' => Hash::make('password123'),
            'role' => 'DOCTOR',
        ]);
        $patient = Patient::create([
            'doctor_id' => $doctor->id,
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $resource = PatientResource::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'type' => 'youtube',
            'title' => 'Test Resource',
            'youtube_url' => 'https://www.youtube.com/watch?v=test123',
        ]);

        $token = $patient->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/patient/resources');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'type', 'title', 'session', 'session_day'],
                ],
            ])
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function unauthenticated_request_returns_401()
    {
        $response = $this->getJson('/api/patient/me');

        $response->assertStatus(401);
    }
}
