<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BladeAuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@yourmindaid.com'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'password_hash' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+1234567890',
                'address' => '123 Main St, City, State 12345',
            ]
        );

        // Doctor user
        User::updateOrCreate(
            ['email' => 'doctor@yourmindaid.com'],
            [
                'name' => 'Dr. Sarah Johnson',
                'username' => 'doctor',
                'password' => Hash::make('password'),
                'password_hash' => Hash::make('password'),
                'role' => 'doctor',
                'phone' => '+1234567891',
                'address' => '123 Main St, City, State 12345',
            ]
        );

        // Assistant user
        User::updateOrCreate(
            ['email' => 'assistant@yourmindaid.com'],
            [
                'name' => 'Assistant User',
                'username' => 'assistant',
                'password' => Hash::make('password'),
                'password_hash' => Hash::make('password'),
                'role' => 'assistant',
                'phone' => '+1234567892',
                'address' => '123 Main St, City, State 12345',
            ]
        );

        $this->command->info('Blade auth users seeded successfully!');
        $this->command->info('Admin: admin@yourmindaid.com / password');
        $this->command->info('Doctor: doctor@yourmindaid.com / password');
        $this->command->info('Assistant: assistant@yourmindaid.com / password');
    }
}
