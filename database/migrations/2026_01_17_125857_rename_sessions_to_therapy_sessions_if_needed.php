<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if sessions table exists
        if (Schema::hasTable('sessions')) {
            // Get column names to determine table type
            $columns = Schema::getColumnListing('sessions');
            
            // Check if it's Laravel's session table (has user_id, ip_address, payload, last_activity)
            $isLaravelSessionTable = in_array('user_id', $columns) 
                && in_array('ip_address', $columns) 
                && in_array('payload', $columns) 
                && in_array('last_activity', $columns);
            
            // Check if it's therapy sessions table (has doctor_id, patient_id, title)
            $isTherapySessionTable = in_array('doctor_id', $columns) 
                && in_array('patient_id', $columns) 
                && in_array('title', $columns);
            
            if ($isTherapySessionTable) {
                // It's the therapy sessions table, rename it
                Schema::rename('sessions', 'therapy_sessions');
                
                // Update foreign keys in dependent tables if they exist
                $this->updateForeignKeys();
            } elseif ($isLaravelSessionTable) {
                // It's Laravel's session table, keep it and create therapy_sessions
                // The therapy_sessions table should be created by the original migration
                // But if it doesn't exist, create it
                if (!Schema::hasTable('therapy_sessions')) {
                    Schema::create('therapy_sessions', function (Blueprint $table) {
                        $table->id();
                        $table->unsignedBigInteger('doctor_id');
                        $table->unsignedBigInteger('patient_id');
                        $table->string('title');
                        $table->longText('notes')->nullable();
                        $table->enum('status', ['active', 'closed'])->default('active');
                        $table->timestamps();

                        // Foreign key constraints with cascade delete
                        $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
                        $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');

                        // Indexes for performance
                        $table->index('doctor_id');
                        $table->index('patient_id');
                        $table->index(['doctor_id', 'patient_id']);
                    });
                    
                    // Update foreign keys in dependent tables if they exist
                    $this->updateForeignKeys();
                }
            }
        } else {
            // Sessions table doesn't exist, ensure therapy_sessions exists
            if (!Schema::hasTable('therapy_sessions')) {
                Schema::create('therapy_sessions', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('doctor_id');
                    $table->unsignedBigInteger('patient_id');
                    $table->string('title');
                    $table->longText('notes')->nullable();
                    $table->enum('status', ['active', 'closed'])->default('active');
                    $table->timestamps();

                    // Foreign key constraints with cascade delete
                    $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');

                    // Indexes for performance
                    $table->index('doctor_id');
                    $table->index('patient_id');
                    $table->index(['doctor_id', 'patient_id']);
                });
            }
        }
    }

    /**
     * Update foreign key references in dependent tables.
     */
    private function updateForeignKeys(): void
    {
        // Update session_days table foreign key if it exists
        if (Schema::hasTable('session_days')) {
            // Find foreign keys that reference 'sessions' table
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'session_days' 
                AND COLUMN_NAME = 'session_id' 
                AND REFERENCED_TABLE_NAME = 'sessions'
            ");
            
            // Drop foreign keys that reference 'sessions'
            foreach ($foreignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE session_days DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                } catch (\Exception $e) {
                    // Ignore if foreign key doesn't exist
                }
            }
            
            // Check if foreign key already references 'therapy_sessions'
            $existingFk = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'session_days' 
                AND COLUMN_NAME = 'session_id' 
                AND REFERENCED_TABLE_NAME = 'therapy_sessions'
            ");
            
            // Only add foreign key if it doesn't already exist
            if (empty($existingFk)) {
                try {
                    DB::statement('ALTER TABLE session_days ADD CONSTRAINT session_days_session_id_foreign FOREIGN KEY (session_id) REFERENCES therapy_sessions(id) ON DELETE CASCADE');
                } catch (\Exception $e) {
                    // Foreign key might already exist, ignore
                }
            }
        }
        
        // Update patient_resources table foreign key if it exists
        if (Schema::hasTable('patient_resources')) {
            // Find foreign keys that reference 'sessions' table
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'patient_resources' 
                AND COLUMN_NAME = 'session_id' 
                AND REFERENCED_TABLE_NAME = 'sessions'
            ");
            
            // Drop foreign keys that reference 'sessions'
            foreach ($foreignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE patient_resources DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                } catch (\Exception $e) {
                    // Ignore if foreign key doesn't exist
                }
            }
            
            // Check if foreign key already references 'therapy_sessions'
            $existingFk = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'patient_resources' 
                AND COLUMN_NAME = 'session_id' 
                AND REFERENCED_TABLE_NAME = 'therapy_sessions'
            ");
            
            // Only add foreign key if it doesn't already exist
            if (empty($existingFk)) {
                try {
                    DB::statement('ALTER TABLE patient_resources ADD CONSTRAINT patient_resources_session_id_foreign FOREIGN KEY (session_id) REFERENCES therapy_sessions(id) ON DELETE CASCADE');
                } catch (\Exception $e) {
                    // Foreign key might already exist, ignore
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is designed to fix the table structure
        // Rolling back would require knowing the original state
        // For safety, we'll leave the therapy_sessions table as is
    }
};
