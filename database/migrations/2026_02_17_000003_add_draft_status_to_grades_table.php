<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');

            DB::statement('
                CREATE TABLE grades_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    course_id INTEGER NOT NULL,
                    subject_id INTEGER NULL,
                    student_id INTEGER NOT NULL,
                    graded_by INTEGER NULL,
                    score NUMERIC NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    status TEXT NOT NULL DEFAULT \'draft\' CHECK (status IN (\'draft\', \'pending\', \'approved\', \'rejected\')),
                    reviewed_by INTEGER NULL,
                    reviewed_at DATETIME NULL,
                    rejection_reason VARCHAR NULL,
                    FOREIGN KEY(course_id) REFERENCES courses(id) ON DELETE CASCADE,
                    FOREIGN KEY(subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
                    FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE,
                    FOREIGN KEY(graded_by) REFERENCES users(id) ON DELETE SET NULL,
                    FOREIGN KEY(reviewed_by) REFERENCES users(id) ON DELETE SET NULL
                )
            ');

            DB::statement("
                INSERT INTO grades_new (id, course_id, subject_id, student_id, graded_by, score, created_at, updated_at, status, reviewed_by, reviewed_at, rejection_reason)
                SELECT id, course_id, subject_id, student_id, graded_by, score, created_at, updated_at,
                       CASE WHEN status IN ('pending','approved','rejected') THEN status ELSE 'draft' END,
                       reviewed_by, reviewed_at, rejection_reason
                FROM grades
            ");

            DB::statement('DROP TABLE grades');
            DB::statement('ALTER TABLE grades_new RENAME TO grades');
            DB::statement('CREATE UNIQUE INDEX grades_subject_id_student_id_unique ON grades(subject_id, student_id)');
            DB::statement('CREATE INDEX grades_status_subject_student_idx ON grades(status, subject_id, student_id)');
            DB::statement('PRAGMA foreign_keys=ON');

            return;
        }

        // Add "draft" to the enum and make it the default for new records.
        // Note: ALTERing ENUM requires raw SQL on MySQL.
        DB::statement("ALTER TABLE `grades` MODIFY `status` ENUM('draft','pending','approved','rejected') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("UPDATE grades SET status = 'pending' WHERE status = 'draft'");

            DB::statement('PRAGMA foreign_keys=OFF');
            DB::statement('
                CREATE TABLE grades_old (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    course_id INTEGER NOT NULL,
                    subject_id INTEGER NULL,
                    student_id INTEGER NOT NULL,
                    graded_by INTEGER NULL,
                    score NUMERIC NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    status TEXT NOT NULL DEFAULT \'approved\' CHECK (status IN (\'pending\', \'approved\', \'rejected\')),
                    reviewed_by INTEGER NULL,
                    reviewed_at DATETIME NULL,
                    rejection_reason VARCHAR NULL,
                    FOREIGN KEY(course_id) REFERENCES courses(id) ON DELETE CASCADE,
                    FOREIGN KEY(subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
                    FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE,
                    FOREIGN KEY(graded_by) REFERENCES users(id) ON DELETE SET NULL,
                    FOREIGN KEY(reviewed_by) REFERENCES users(id) ON DELETE SET NULL
                )
            ');

            DB::statement("
                INSERT INTO grades_old (id, course_id, subject_id, student_id, graded_by, score, created_at, updated_at, status, reviewed_by, reviewed_at, rejection_reason)
                SELECT id, course_id, subject_id, student_id, graded_by, score, created_at, updated_at, status, reviewed_by, reviewed_at, rejection_reason
                FROM grades
            ");

            DB::statement('DROP TABLE grades');
            DB::statement('ALTER TABLE grades_old RENAME TO grades');
            DB::statement('CREATE UNIQUE INDEX grades_subject_id_student_id_unique ON grades(subject_id, student_id)');
            DB::statement('CREATE INDEX grades_status_subject_student_idx ON grades(status, subject_id, student_id)');
            DB::statement('PRAGMA foreign_keys=ON');

            return;
        }

        // Revert enum back (draft removed). Any draft rows are converted to pending.
        DB::statement("UPDATE `grades` SET `status` = 'pending' WHERE `status` = 'draft'");
        DB::statement("ALTER TABLE `grades` MODIFY `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved'");
    }
};
