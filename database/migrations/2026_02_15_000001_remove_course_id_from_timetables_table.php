<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Timetable → Subject → Course is sufficient; course_id is redundant.
     */
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add course_id as nullable (safer rollback; rows without subject match stay NULL)
        Schema::table('timetables', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Backfill from subject; do not enforce NOT NULL (consistent with derived/optional course)
        DB::statement('UPDATE timetables t INNER JOIN subjects s ON t.subject_id = s.id SET t.course_id = s.course_id');
    }
};
