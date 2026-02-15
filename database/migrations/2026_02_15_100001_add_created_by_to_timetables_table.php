<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * User 1 – Timetable 0..*: record which staff created the timetable (audit).
     */
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('location')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });
    }
};
