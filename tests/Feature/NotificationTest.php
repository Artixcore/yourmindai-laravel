<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\HomeworkAssignment;
use App\Models\HomeworkCompletion;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificationTest extends TestCase
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
            'patient_number' => 'PAT-0001',
            'status' => 'ACTIVE',
        ]);
    }

    protected function createTestNotification(User $user): void
    {
        $doctor = $this->createUser('d@test.com', 'doctor');
        $patientProfile = $this->createPatientProfile($doctor, $user);
        $appointment = Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patientProfile->id,
            'date' => now()->addDay(),
            'time_slot' => '09:00',
            'status' => 'pending',
            'appointment_type' => 'initial',
        ]);
        $user->notify(new \App\Notifications\NewAppointmentNotification(
            $appointment,
            'Test',
            'Test message',
            '/test'
        ));
    }

    /** @test */
    public function unread_count_returns_correct_count(): void
    {
        $user = $this->createUser('user@test.com', 'admin');
        $this->createTestNotification($user);

        $response = $this->actingAs($user)->getJson(route('notifications.unread-count'));

        $response->assertOk();
        $response->assertJson(['success' => true, 'count' => 1]);
    }

    /** @test */
    public function unread_list_returns_notifications(): void
    {
        $user = $this->createUser('user@test.com', 'admin');
        $doctor = $this->createUser('d@test.com', 'doctor');
        $patientProfile = $this->createPatientProfile($doctor, $user);
        $appointment = Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patientProfile->id,
            'date' => now()->addDay(),
            'time_slot' => '09:00',
            'status' => 'pending',
            'appointment_type' => 'initial',
        ]);
        $user->notify(new \App\Notifications\NewAppointmentNotification(
            $appointment,
            'Test Title',
            'Test message',
            '/test'
        ));

        $response = $this->actingAs($user)->getJson(route('notifications.unread'));

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonPath('notifications.0.title', 'Test Title');
    }

    /** @test */
    public function mark_as_read_works(): void
    {
        $user = $this->createUser('user@test.com', 'admin');
        $this->createTestNotification($user);
        $notification = $user->unreadNotifications->first();

        $response = $this->actingAs($user)->postJson(route('notifications.read', $notification->id), [
            '_token' => csrf_token(),
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    /** @test */
    public function mark_all_read_works(): void
    {
        $user = $this->createUser('user@test.com', 'admin');
        $this->createTestNotification($user);

        $response = $this->actingAs($user)->post(route('notifications.read-all'), [
            '_token' => csrf_token(),
        ]);

        $response->assertOk();
        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    /** @test */
    public function user_cannot_read_another_users_notification(): void
    {
        $user1 = $this->createUser('user1@test.com', 'admin');
        $user2 = $this->createUser('user2@test.com', 'admin');
        $this->createTestNotification($user1);
        $notification = $user1->unreadNotifications->first();

        $response = $this->actingAs($user2)->postJson(route('notifications.read', $notification->id), [
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function task_completion_triggers_notification(): void
    {
        $doctor = $this->createUser('doctor@test.com', 'doctor');
        $patientUser = $this->createUser('patient@test.com');
        $patientProfile = $this->createPatientProfile($doctor, $patientUser);
        $task = Task::create([
            'patient_id' => $patientProfile->id,
            'title' => 'Test Task',
            'status' => 'pending',
            'due_date' => now()->addDay(),
            'assigned_by_doctor_id' => $doctor->id,
            'visible_to_patient' => true,
        ]);

        $this->actingAs($patientUser)->post(route('client.tasks.complete', $task), [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(1, $doctor->fresh()->unreadNotifications()->count());
    }

    /** @test */
    public function homework_assignment_triggers_notification(): void
    {
        $doctor = $this->createUser('doctor@test.com', 'doctor');
        $patientUser = $this->createUser('patient@test.com');
        $patientProfile = $this->createPatientProfile($doctor, $patientUser);
        $patient = Patient::create([
            'doctor_id' => $doctor->id,
            'name' => 'Test Patient',
            'email' => $patientUser->email,
            'status' => 'active',
        ]);

        $this->actingAs($doctor)->post(route('patients.homework.store', $patient), [
            '_token' => csrf_token(),
            'homework_type' => 'psychotherapy',
            'title' => 'Daily Journal',
            'frequency' => 'daily',
            'start_date' => now()->format('Y-m-d'),
        ]);

        $this->assertEquals(1, $patientUser->fresh()->unreadNotifications()->count());
    }

    /** @test */
    public function notifications_require_authentication(): void
    {
        $response = $this->getJson(route('notifications.unread-count'));
        $response->assertRedirect();
    }
}
