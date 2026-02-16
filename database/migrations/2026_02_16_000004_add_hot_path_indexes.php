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
        Schema::table('course_student', function (Blueprint $table) {
            $table->index(['status', 'student_id', 'course_id'], 'course_student_status_student_course_idx');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['student_id', 'subject_id', 'date', 'status'], 'attendances_student_subject_date_status_idx');
        });

        Schema::table('fees', function (Blueprint $table) {
            $table->index(['status', 'paid_date', 'student_id'], 'fees_status_paid_date_student_idx');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->index(['status', 'subject_id', 'student_id'], 'grades_status_subject_student_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_student', function (Blueprint $table) {
            $table->dropIndex('course_student_status_student_course_idx');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('attendances_student_subject_date_status_idx');
        });

        Schema::table('fees', function (Blueprint $table) {
            $table->dropIndex('fees_status_paid_date_student_idx');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->dropIndex('grades_status_subject_student_idx');
        });
    }
};
