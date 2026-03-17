<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('grades')) {
            return;
        }

        // Remove invalid rows that cannot satisfy the new constraint.
        DB::table('grades')->whereNull('subject_id')->delete();

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
            $this->rebuildSqliteGradesSubjectOnly();
            DB::statement('PRAGMA foreign_keys=ON');
            return;
        }

        // Drop course_id (it is derived via subject -> course).
        if (Schema::hasColumn('grades', 'course_id')) {
            Schema::table('grades', function (Blueprint $table) {
                try {
                    $table->dropForeign(['course_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
            });

            Schema::table('grades', function (Blueprint $table) {
                $table->dropColumn('course_id');
            });
        }

        // Enforce subject_id required.
        if (Schema::hasColumn('grades', 'subject_id')) {
            $driver = DB::getDriverName();
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement('ALTER TABLE `grades` MODIFY `subject_id` BIGINT UNSIGNED NOT NULL');
            } else {
                Schema::table('grades', function (Blueprint $table) {
                    $table->foreignId('subject_id')->nullable(false)->change();
                });
            }
        }

        // Ensure uniqueness is enforced on (subject_id, student_id).
        Schema::table('grades', function (Blueprint $table) {
            try {
                $table->dropUnique(['subject_id', 'student_id']);
            } catch (\Throwable $e) {
                // ignore
            }
            try {
                $table->dropUnique('grades_subject_id_student_id_unique');
            } catch (\Throwable $e) {
                // ignore
            }

            $table->unique(['subject_id', 'student_id'], 'grades_subject_id_student_id_unique');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('grades')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
            $this->rebuildSqliteGradesWithCourseId();
            DB::statement('PRAGMA foreign_keys=ON');
            return;
        }

        // Re-add course_id (legacy) as nullable, backfilled from subject.
        if (! Schema::hasColumn('grades', 'course_id')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->foreignId('course_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            });
            DB::statement('UPDATE grades g INNER JOIN subjects s ON g.subject_id = s.id SET g.course_id = s.course_id');
        }

        // Allow subject_id nullable again (legacy).
        if (Schema::hasColumn('grades', 'subject_id')) {
            $driver = DB::getDriverName();
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement('ALTER TABLE `grades` MODIFY `subject_id` BIGINT UNSIGNED NULL');
            } else {
                Schema::table('grades', function (Blueprint $table) {
                    $table->foreignId('subject_id')->nullable()->change();
                });
            }
        }
    }

    private function rebuildSqliteGradesSubjectOnly(): void
    {
        Schema::create('grades_tmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('reviewed_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['subject_id', 'student_id'], 'grades_subject_id_student_id_unique');
            $table->index(['status', 'subject_id', 'student_id'], 'grades_status_subject_student_idx');
        });

        // Copy over rows with subject_id present.
        DB::statement('
            INSERT INTO grades_tmp (
                id, subject_id, student_id, graded_by, reviewed_by, score, status, reviewed_at, rejection_reason, created_at, updated_at
            )
            SELECT
                id, subject_id, student_id, graded_by, reviewed_by, score, status, reviewed_at, rejection_reason, created_at, updated_at
            FROM grades
            WHERE subject_id IS NOT NULL
        ');

        Schema::drop('grades');
        Schema::rename('grades_tmp', 'grades');
    }

    private function rebuildSqliteGradesWithCourseId(): void
    {
        Schema::create('grades_tmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('reviewed_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['subject_id', 'student_id'], 'grades_subject_id_student_id_unique');
            $table->index(['status', 'subject_id', 'student_id'], 'grades_status_subject_student_idx');
        });

        DB::statement('
            INSERT INTO grades_tmp (
                id, course_id, subject_id, student_id, graded_by, reviewed_by, score, status, reviewed_at, rejection_reason, created_at, updated_at
            )
            SELECT
                id,
                COALESCE((SELECT course_id FROM subjects WHERE subjects.id = grades.subject_id), 1) as course_id,
                subject_id,
                student_id,
                graded_by,
                reviewed_by,
                score,
                status,
                reviewed_at,
                rejection_reason,
                created_at,
                updated_at
            FROM grades
        ');

        Schema::drop('grades');
        Schema::rename('grades_tmp', 'grades');
    }
};

