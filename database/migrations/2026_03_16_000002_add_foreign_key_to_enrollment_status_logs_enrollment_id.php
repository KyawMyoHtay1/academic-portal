<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enrollment_status_logs')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
            $this->rebuildWithEnrollmentForeignKey();
            DB::statement('PRAGMA foreign_keys=ON');

            return;
        }

        DB::statement('
            UPDATE enrollment_status_logs
            SET enrollment_id = NULL
            WHERE enrollment_id IS NOT NULL
              AND enrollment_id NOT IN (SELECT id FROM course_student)
        ');

        Schema::table('enrollment_status_logs', function (Blueprint $table) {
            $table->foreign('enrollment_id')
                ->references('id')
                ->on('course_student')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enrollment_status_logs')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
            $this->rebuildWithoutEnrollmentForeignKey();
            DB::statement('PRAGMA foreign_keys=ON');

            return;
        }

        Schema::table('enrollment_status_logs', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
        });
    }

    private function rebuildWithEnrollmentForeignKey(): void
    {
        Schema::create('enrollment_status_logs_fk_tmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->nullable()->constrained('course_student')->nullOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('from_status', 50)->nullable();
            $table->string('to_status', 50)->nullable();
            $table->string('action', 100);
            $table->text('reason')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'created_at']);
            $table->index(['course_id', 'created_at']);
        });

        DB::statement('
            INSERT INTO enrollment_status_logs_fk_tmp (
                id, enrollment_id, student_id, course_id, from_status, to_status, action, reason, performed_by, meta, created_at, updated_at
            )
            SELECT
                logs.id,
                CASE
                    WHEN logs.enrollment_id IS NOT NULL
                     AND EXISTS(SELECT 1 FROM course_student WHERE course_student.id = logs.enrollment_id)
                    THEN logs.enrollment_id
                    ELSE NULL
                END AS enrollment_id,
                logs.student_id,
                logs.course_id,
                logs.from_status,
                logs.to_status,
                logs.action,
                logs.reason,
                logs.performed_by,
                logs.meta,
                logs.created_at,
                logs.updated_at
            FROM enrollment_status_logs logs
        ');

        Schema::drop('enrollment_status_logs');
        Schema::rename('enrollment_status_logs_fk_tmp', 'enrollment_status_logs');
    }

    private function rebuildWithoutEnrollmentForeignKey(): void
    {
        Schema::create('enrollment_status_logs_plain_tmp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id')->nullable()->index();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('from_status', 50)->nullable();
            $table->string('to_status', 50)->nullable();
            $table->string('action', 100);
            $table->text('reason')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'created_at']);
            $table->index(['course_id', 'created_at']);
        });

        DB::statement('
            INSERT INTO enrollment_status_logs_plain_tmp (
                id, enrollment_id, student_id, course_id, from_status, to_status, action, reason, performed_by, meta, created_at, updated_at
            )
            SELECT
                id, enrollment_id, student_id, course_id, from_status, to_status, action, reason, performed_by, meta, created_at, updated_at
            FROM enrollment_status_logs
        ');

        Schema::drop('enrollment_status_logs');
        Schema::rename('enrollment_status_logs_plain_tmp', 'enrollment_status_logs');
    }
};
