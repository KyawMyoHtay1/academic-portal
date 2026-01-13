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
        // Modify the enum to include payment_pending
        DB::statement("ALTER TABLE fees MODIFY COLUMN status ENUM('pending', 'payment_pending', 'paid') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        // First, update any payment_pending to pending
        DB::table('fees')
            ->where('status', 'payment_pending')
            ->update(['status' => 'pending']);
        
        // Then modify enum back
        DB::statement("ALTER TABLE fees MODIFY COLUMN status ENUM('pending', 'paid') DEFAULT 'pending'");
    }
};
