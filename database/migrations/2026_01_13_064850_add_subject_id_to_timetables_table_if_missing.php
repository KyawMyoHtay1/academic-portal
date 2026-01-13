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
            // Drop foreign key constraint first if it exists
            if (Schema::hasColumn('timetables', 'course_id')) {
                $table->dropForeign(['course_id']);
            }
            
            // Make course_id nullable
            $table->foreignId('course_id')->nullable()->change();
            
            // Re-add foreign key
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            
            // Add subject_id column
            $table->foreignId('subject_id')->nullable()->after('course_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            // Drop subject_id foreign key
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
            
            // Restore course_id to not nullable
            $table->dropForeign(['course_id']);
            $table->foreignId('course_id')->nullable(false)->change();
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }
};
