<?php

namespace Tests\Feature;

use App\Models\BehaviorContingencyCheckin;
use App\Models\BehaviorContingencyPlan;
use App\Models\BehaviorContingencyPlanItem;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BehaviorContingencyPlanTest extends TestCase
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

    protected function createPatient(User $doctor): Patient
    {
        return Patient::create([
            'doctor_id' => $doctor->id,
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'phone' => '1234567890',
            'password' => Hash::make('password123'),
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
            'status' => 'active',
        ]);
    }

    /** @test */
    public function doctor_can_create_behavior_contingency_plan(): void
    {
        $doctor = $this->createDoctor();
        $patient = $this->createPatient($doctor);
        $patientUser = User::create([
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
        ]);
        $patientProfile = $this->createPatientProfile($doctor, $patientUser);

        $response = $this->actingAs($doctor)->post(route('patients.behavior-contingency-plans.store', $patient), [
            'title' => 'Daily Behavior Plan',
            'starts_at' => now()->format('Y-m-d'),
            'ends_at' => null,
            'status' => 'active',
            'items' => [
                [
                    'target_behavior' => 'lying',
                    'condition_stimulus' => 'tell truth daily',
                    'reward_if_followed' => 'extra screen time',
                    'punishment_if_not_followed' => 'educational discussion',
                ],
            ],
        ]);

        $response->assertRedirect(route('patients.behavior-contingency-plans.index', $patient));
        $this->assertDatabaseHas('behavior_contingency_plans', [
            'title' => 'Daily Behavior Plan',
            'patient_id' => $patient->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('behavior_contingency_plan_items', [
            'target_behavior' => 'lying',
            'condition_stimulus' => 'tell truth daily',
        ]);
    }

    /** @test */
    public function doctor_can_view_behavior_contingency_plans_index(): void
    {
        $doctor = $this->createDoctor();
        $patient = $this->createPatient($doctor);
        $patientProfile = PatientProfile::create([
            'doctor_id' => $doctor->id,
            'full_name' => $patient->name,
            'phone' => $patient->phone,
            'status' => 'active',
        ]);

        $plan = BehaviorContingencyPlan::create([
            'patient_id' => $patient->id,
            'patient_profile_id' => $patientProfile->id,
            'created_by' => $doctor->id,
            'title' => 'Test Plan',
            'starts_at' => now(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($doctor)->get(route('patients.behavior-contingency-plans.index', $patient));

        $response->assertStatus(200);
        $response->assertSee('Test Plan');
    }

    /** @test */
    public function client_can_view_own_contingency_plans(): void
    {
        $doctor = $this->createDoctor();
        $patient = $this->createPatient($doctor);
        $patientUser = User::create([
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
        ]);
        $patientProfile = PatientProfile::create([
            'user_id' => $patientUser->id,
            'doctor_id' => $doctor->id,
            'full_name' => 'Test Patient',
            'phone' => '1234567890',
            'status' => 'active',
        ]);

        $plan = BehaviorContingencyPlan::create([
            'patient_id' => $patient->id,
            'patient_profile_id' => $patientProfile->id,
            'created_by' => $doctor->id,
            'title' => 'My Plan',
            'starts_at' => now(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($patientUser)->get(route('client.contingency-plans.index'));

        $response->assertStatus(200);
        $response->assertSee('My Plan');
    }

    /** @test */
    public function client_can_submit_checkin(): void
    {
        $doctor = $this->createDoctor();
        $patient = $this->createPatient($doctor);
        $patientUser = User::create([
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
        ]);
        $patientProfile = PatientProfile::create([
            'user_id' => $patientUser->id,
            'doctor_id' => $doctor->id,
            'full_name' => 'Test Patient',
            'phone' => '1234567890',
            'status' => 'active',
        ]);

        $plan = BehaviorContingencyPlan::create([
            'patient_id' => $patient->id,
            'patient_profile_id' => $patientProfile->id,
            'created_by' => $doctor->id,
            'title' => 'My Plan',
            'starts_at' => now(),
            'status' => 'active',
        ]);

        $item = BehaviorContingencyPlanItem::create([
            'plan_id' => $plan->id,
            'sort_order' => 0,
            'target_behavior' => 'lying',
            'condition_stimulus' => 'tell truth',
            'is_active' => true,
        ]);

        $response = $this->actingAs($patientUser)->post(route('client.contingency-plans.checkins.store', $plan), [
            'checkins' => [
                [
                    'plan_item_id' => $item->id,
                    'followed' => true,
                    'client_note' => 'Did well today',
                ],
            ],
        ]);

        $response->assertRedirect(route('client.contingency-plans.show', $plan));
        $this->assertDatabaseHas('behavior_contingency_checkins', [
            'plan_id' => $plan->id,
            'plan_item_id' => $item->id,
            'followed' => true,
            'client_note' => 'Did well today',
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_client_contingency_plans(): void
    {
        $response = $this->get(route('client.contingency-plans.index'));
        $response->assertRedirect(route('login'));
    }
}
