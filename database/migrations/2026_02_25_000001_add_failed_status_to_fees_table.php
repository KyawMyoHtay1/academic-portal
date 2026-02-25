<?php

use Illuminate\Database\Migrations\Migration;
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

        DB::statement("ALTER TABLE fees MODIFY COLUMN status ENUM('pending', 'payment_pending', 'failed', 'paid') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::table('fees')
            ->where('status', 'failed')
            ->update(['status' => 'pending']);

        DB::statement("ALTER TABLE fees MODIFY COLUMN status ENUM('pending', 'payment_pending', 'paid') DEFAULT 'pending'");
    }
};
