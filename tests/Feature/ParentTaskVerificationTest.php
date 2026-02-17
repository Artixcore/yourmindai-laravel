<?php

namespace Tests\Feature;

use App\Models\ParentLink;
use App\Models\PatientProfile;
use App\Models\Task;
use App\Models\TaskVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ParentTaskVerificationTest extends TestCase
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
            'status' => 'active',
        ]);
    }

    protected function createPatientProfile(User $doctor, User $patientUser): PatientProfile
    {
        return PatientProfile::create([
            'user_id' => $patientUser->id,
            'doctor_id' => $doctor->id,
            'full_name' => 'Test Patient',
            'phone' => '1234567890',
            'patient_number' => 'PAT-001',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function parent_cannot_verify_tasks_for_unrelated_patient(): void
    {
        $doctor = $this->createUser('doctor@test.com', 'doctor');
        $patientUser = $this->createUser('patient@test.com', 'patient');
        $parentUser = $this->createUser('parent@test.com', 'parent');
        $otherParent = $this->createUser('otherparent@test.com', 'parent');

        $patientProfile = $this->createPatientProfile($doctor, $patientUser);
        ParentLink::create(['parent_id' => $parentUser->id, 'patient_id' => $patientProfile->id]);

        $task = Task::create([
            'patient_id' => $patientProfile->id,
            'title' => 'Test Task',
            'status' => 'pending',
            'visible_to_parent' => true,
        ]);

        $response = $this->actingAs($otherParent)->postJson(route('parent.tasks.verify', $task), [
            '_token' => csrf_token(),
            'verified' => 1,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('task_verifications', [
            'task_id' => $task->id,
            'parent_user_id' => $otherParent->id,
        ]);
    }

    /** @test */
    public function parent_can_verify_tasks_for_linked_child(): void
    {
        $doctor = $this->createUser('doctor@test.com', 'doctor');
        $patientUser = $this->createUser('patient@test.com', 'patient');
        $parentUser = $this->createUser('parent@test.com', 'parent');

        $patientProfile = $this->createPatientProfile($doctor, $patientUser);
        ParentLink::create(['parent_id' => $parentUser->id, 'patient_id' => $patientProfile->id]);

        $task = Task::create([
            'patient_id' => $patientProfile->id,
            'title' => 'Test Task',
            'status' => 'pending',
            'visible_to_parent' => true,
        ]);

        $response = $this->actingAs($parentUser)->postJson(route('parent.tasks.verify', $task), [
            '_token' => csrf_token(),
            'verified' => 1,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true, 'verified' => true]);
        $this->assertDatabaseHas('task_verifications', [
            'task_id' => $task->id,
            'parent_user_id' => $parentUser->id,
        ]);
    }

    /** @test */
    public function verification_sets_correct_fields(): void
    {
        $doctor = $this->createUser('doctor@test.com', 'doctor');
        $patientUser = $this->createUser('patient@test.com', 'patient');
        $parentUser = $this->createUser('parent@test.com', 'parent');

        $patientProfile = $this->createPatientProfile($doctor, $patientUser);
        ParentLink::create(['parent_id' => $parentUser->id, 'patient_id' => $patientProfile->id]);

        $task = Task::create([
            'patient_id' => $patientProfile->id,
            'title' => 'Test Task',
            'status' => 'pending',
            'visible_to_parent' => true,
        ]);

        $this->actingAs($parentUser)->post(route('parent.tasks.verify', $task), [
            '_token' => csrf_token(),
            'verified' => 1,
        ]);

        $verification = TaskVerification::where('task_id', $task->id)
            ->where('parent_user_id', $parentUser->id)
            ->first();

        $this->assertNotNull($verification);
        $this->assertNotNull($verification->verified_at);
    }

    /** @test */
    public function parent_routes_require_auth_and_role(): void
    {
        $response = $this->get(route('parent.dashboard'));
        $response->assertRedirect(route('login'));

        $doctor = $this->createUser('doctor@test.com', 'doctor');
        $response = $this->actingAs($doctor)->get(route('parent.dashboard'));
        $response->assertForbidden();
    }

    /** @test */
    public function parent_can_remove_verification(): void
    {
        $doctor = $this->createUser('doctor@test.com', 'doctor');
        $patientUser = $this->createUser('patient@test.com', 'patient');
        $parentUser = $this->createUser('parent@test.com', 'parent');

        $patientProfile = $this->createPatientProfile($doctor, $patientUser);
        ParentLink::create(['parent_id' => $parentUser->id, 'patient_id' => $patientProfile->id]);

        $task = Task::create([
            'patient_id' => $patientProfile->id,
            'title' => 'Test Task',
            'status' => 'pending',
            'visible_to_parent' => true,
        ]);

        TaskVerification::create([
            'task_id' => $task->id,
            'parent_user_id' => $parentUser->id,
            'verified_at' => now(),
        ]);

        $response = $this->actingAs($parentUser)->postJson(route('parent.tasks.verify', $task), [
            '_token' => csrf_token(),
            'verified' => 0,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true, 'verified' => false]);
        $this->assertDatabaseMissing('task_verifications', [
            'task_id' => $task->id,
            'parent_user_id' => $parentUser->id,
        ]);
    }
}
