<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    private const ADMIN_EMAIL = 'admin@yourmindaid.com';
    private const ADMIN_PASSWORD = 'Admin123456';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🌱 Starting MongoDB seed...\n\n";

        // Check if admin already exists
        $existingAdmin = User::where('email', self::ADMIN_EMAIL)->first();

        if ($existingAdmin) {
            echo "⚠️  Admin account already exists:\n";
            echo "   Email: " . self::ADMIN_EMAIL . "\n";
            echo "   Skipping creation...\n\n";
            return;
        }

        // Create admin account
        $admin = User::create([
            'email' => self::ADMIN_EMAIL,
            'password_hash' => Hash::make(self::ADMIN_PASSWORD),
            'role' => 'DOCTOR',
        ]);

        echo "✅ Admin account created successfully!\n\n";
        echo "╔════════════════════════════════════════════════════════╗\n";
        echo "║           Admin Login Credentials                     ║\n";
        echo "╠════════════════════════════════════════════════════════╣\n";
        echo "║  Email:    " . str_pad(self::ADMIN_EMAIL, 40) . "║\n";
        echo "║  Password: " . str_pad(self::ADMIN_PASSWORD, 40) . "║\n";
        echo "╚════════════════════════════════════════════════════════╝\n";
        echo "\n   User ID: " . (string) $admin->_id . "\n\n";
        echo "🎉 Seed completed!\n\n";
    }
}
