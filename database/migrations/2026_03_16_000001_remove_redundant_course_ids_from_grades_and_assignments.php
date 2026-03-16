<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');

            $this->rebuildGradesWithoutCourseId();
            $this->rebuildAssignmentsWithoutCourseId();

            DB::statement('PRAGMA foreign_keys=ON');

            return;
        }

        if (Schema::hasColumn('grades', 'course_id')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            });
        }

        if (Schema::hasColumn('assignments', 'course_id')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');

            $this->rebuildGradesWithCourseId();
            $this->rebuildAssignmentsWithCourseId();

            DB::statement('PRAGMA foreign_keys=ON');

            return;
        }

        if (! Schema::hasColumn('grades', 'course_id')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->foreignId('course_id')->nullable()->after('id')->constrained()->nullOnDelete();
            });

            DB::statement('
                UPDATE grades
                SET course_id = (
                    SELECT subjects.course_id
                    FROM subjects
                    WHERE subjects.id = grades.subject_id
                )
                WHERE subject_id IS NOT NULL
            ');
        }

        if (! Schema::hasColumn('assignments', 'course_id')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->foreignId('course_id')->nullable()->after('subject_id')->constrained()->nullOnDelete();
            });

            DB::statement('
                UPDATE assignments
                SET course_id = (
                    SELECT subjects.course_id
                    FROM subjects
                    WHERE subjects.id = assignments.subject_id
                )
                WHERE subject_id IS NOT NULL
            ');
        }
    }

    private function rebuildGradesWithoutCourseId(): void
    {
        if (! Schema::hasColumn('grades', 'course_id')) {
            return;
        }

        Schema::create('grades_normalized_tmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();
            $table->string('status', 20)->default('draft');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->unique(['subject_id', 'student_id']);
            $table->index(['status', 'subject_id', 'student_id']);
        });

        DB::statement('
            INSERT INTO grades_normalized_tmp (
                id, subject_id, student_id, graded_by, score, created_at, updated_at, status, reviewed_by, reviewed_at, rejection_reason
            )
            SELECT
                id, subject_id, student_id, graded_by, score, created_at, updated_at, status, reviewed_by, reviewed_at, rejection_reason
            FROM grades
        ');

        Schema::drop('grades');
        Schema::rename('grades_normalized_tmp', 'grades');
    }

    private function rebuildAssignmentsWithoutCourseId(): void
    {
        if (! Schema::hasColumn('assignments', 'course_id')) {
            return;
        }

        Schema::create('assignments_normalized_tmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->time('due_time')->nullable();
            $table->integer('max_score')->default(100);
            $table->string('status', 20)->default('draft');
            $table->json('allowed_file_types')->nullable();
            $table->integer('max_file_size')->nullable();
            $table->timestamps();
            $table->index(['subject_id', 'status']);
            $table->index('due_date');
        });

        DB::statement('
            INSERT INTO assignments_normalized_tmp (
                id, subject_id, created_by, title, description, due_date, due_time, max_score, status, allowed_file_types, max_file_size, created_at, updated_at
            )
            SELECT
                id, subject_id, created_by, title, description, due_date, due_time, max_score, status, allowed_file_types, max_file_size, created_at, updated_at
            FROM assignments
        ');

        Schema::drop('assignments');
        Schema::rename('assignments_normalized_tmp', 'assignments');
    }

    private function rebuildGradesWithCourseId(): void
    {
        if (Schema::hasColumn('grades', 'course_id')) {
            return;
        }

        Schema::create('grades_legacy_tmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();
            $table->string('status', 20)->default('draft');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->unique(['subject_id', 'student_id']);
            $table->index(['status', 'subject_id', 'student_id']);
        });

        DB::statement('
            INSERT INTO grades_legacy_tmp (
                id, course_id, subject_id, student_id, graded_by, score, created_at, updated_at, status, reviewed_by, reviewed_at, rejection_reason
            )
            SELECT
                grades.id,
                (
                    SELECT subjects.course_id
                    FROM subjects
                    WHERE subjects.id = grades.subject_id
                ) AS course_id,
                grades.subject_id,
                grades.student_id,
                grades.graded_by,
                grades.score,
                grades.created_at,
                grades.updated_at,
                grades.status,
                grades.reviewed_by,
                grades.reviewed_at,
                grades.rejection_reason
            FROM grades
        ');

        Schema::drop('grades');
        Schema::rename('grades_legacy_tmp', 'grades');
    }

    private function rebuildAssignmentsWithCourseId(): void
    {
        if (Schema::hasColumn('assignments', 'course_id')) {
            return;
        }

        Schema::create('assignments_legacy_tmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->time('due_time')->nullable();
            $table->integer('max_score')->default(100);
            $table->string('status', 20)->default('draft');
            $table->json('allowed_file_types')->nullable();
            $table->integer('max_file_size')->nullable();
            $table->timestamps();
            $table->index(['subject_id', 'status']);
            $table->index('due_date');
        });

        DB::statement('
            INSERT INTO assignments_legacy_tmp (
                id, subject_id, course_id, created_by, title, description, due_date, due_time, max_score, status, allowed_file_types, max_file_size, created_at, updated_at
            )
            SELECT
                assignments.id,
                assignments.subject_id,
                (
                    SELECT subjects.course_id
                    FROM subjects
                    WHERE subjects.id = assignments.subject_id
                ) AS course_id,
                assignments.created_by,
                assignments.title,
                assignments.description,
                assignments.due_date,
                assignments.due_time,
                assignments.max_score,
                assignments.status,
                assignments.allowed_file_types,
                assignments.max_file_size,
                assignments.created_at,
                assignments.updated_at
            FROM assignments
        ');

        Schema::drop('assignments');
        Schema::rename('assignments_legacy_tmp', 'assignments');
    }
};
