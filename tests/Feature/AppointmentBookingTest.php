<?php

namespace Tests\Feature;

use App\Models\AppointmentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AppointmentBookingTest extends TestCase
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
            'doctor_number' => 'DR001',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function public_can_view_booking_form(): void
    {
        $response = $this->get(route('appointment.book'));

        $response->assertOk();
    }

    /** @test */
    public function public_can_book_by_doctor_number(): void
    {
        $doctor = $this->createDoctor();

        $response = $this->post(route('appointment-request.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@test.com',
            'phone' => '1234567890',
            'preferred_date' => now()->addDays(3)->format('Y-m-d'),
            'doctor_number' => 'DR001',
        ]);

        $response->assertRedirect();
        $request = AppointmentRequest::where('email', 'john@test.com')->first();
        $this->assertNotNull($request);
        $this->assertEquals($doctor->id, $request->doctor_id);
    }

    /** @test */
    public function booking_form_with_doctor_number_pre_selects_doctor(): void
    {
        $doctor = $this->createDoctor();

        $response = $this->get(route('appointment.book.doctor', 'DR001'));

        $response->assertOk();
        $response->assertSee($doctor->name ?? $doctor->email);
    }
}
