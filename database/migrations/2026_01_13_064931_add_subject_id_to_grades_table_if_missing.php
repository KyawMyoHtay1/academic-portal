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
        Schema::table('grades', function (Blueprint $table) {
            // Drop foreign key constraint first
            try {
                $table->dropForeign(['course_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            
            // Drop the old unique constraint if it exists
            try {
                $table->dropUnique(['course_id', 'student_id']);
            } catch (\Exception $e) {
                // Unique constraint might not exist, continue
            }
            
            // Make course_id nullable
            $table->foreignId('course_id')->nullable()->change();
            
            // Re-add foreign key
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            
            // Add subject_id column
            $table->foreignId('subject_id')->nullable()->after('course_id')->constrained()->onDelete('cascade');
            
            // Add new unique constraint with subject_id
            $table->unique(['subject_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique(['subject_id', 'student_id']);
            
            // Drop subject_id foreign key
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
            
            // Restore course_id to not nullable
            $table->dropForeign(['course_id']);
            $table->foreignId('course_id')->nullable(false)->change();
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            
            // Restore old unique constraint
            $table->unique(['course_id', 'student_id']);
        });
    }
};
