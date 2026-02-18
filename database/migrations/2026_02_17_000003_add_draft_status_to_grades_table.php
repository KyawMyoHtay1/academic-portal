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

        // Add "draft" to the enum and make it the default for new records.
        // Note: ALTERing ENUM requires raw SQL on MySQL.
        DB::statement("ALTER TABLE `grades` MODIFY `status` ENUM('draft','pending','approved','rejected') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::table('grades')
                ->where('status', 'draft')
                ->update(['status' => 'pending']);

            return;
        }

        // Revert enum back (draft removed). Any draft rows are converted to pending.
        DB::statement("UPDATE `grades` SET `status` = 'pending' WHERE `status` = 'draft'");
        DB::statement("ALTER TABLE `grades` MODIFY `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved'");
    }
};
