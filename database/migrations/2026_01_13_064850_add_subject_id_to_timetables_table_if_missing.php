<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            // Ensure course_id exists (older installs might miss it)
            if (! Schema::hasColumn('timetables', 'course_id')) {
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            }

            // Only add subject_id if it is missing
            if (! Schema::hasColumn('timetables', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->after('course_id')->constrained()->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            if (Schema::hasColumn('timetables', 'subject_id')) {
                $table->dropForeign(['subject_id']);
                $table->dropColumn('subject_id');
            }
        });
    }
};
