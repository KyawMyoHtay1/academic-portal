<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Attendance → Subject → Course is sufficient; course_id is redundant.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'course_id')) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            }
        });

        // Index for common queries: filter by date (e.g. recent records, reports)
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });

        // Add column nullable first so backfill can run; then enforce NOT NULL
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Backfill before making NOT NULL (rows with subject_id get course_id from subject)
        DB::statement('UPDATE attendances a INNER JOIN subjects s ON a.subject_id = s.id SET a.course_id = s.course_id');

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable(false)->change();
        });
    }
};
