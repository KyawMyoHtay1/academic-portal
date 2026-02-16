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
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Modify the enum to include withdrawal_pending
        DB::statement("ALTER TABLE course_student MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'withdrawal_pending') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Revert to original enum values
        // First, update any withdrawal_pending to approved
        DB::table('course_student')
            ->where('status', 'withdrawal_pending')
            ->update(['status' => 'approved']);
        
        // Then modify enum back
        DB::statement("ALTER TABLE course_student MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
