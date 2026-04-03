<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }

        // Before dropping columns, align existing data so the UI can still show identity
        // through the linked users table.
        if (DB::getDriverName() !== 'sqlite') {
            // Best-effort: if a student row has email/full_name set, backfill users.
            // This avoids losing identity when migrating an existing DB.
            if (Schema::hasColumns('students', ['email', 'full_name'])) {
                DB::statement("
                    UPDATE users u
                    INNER JOIN students s ON s.user_id = u.id
                    SET
                      u.email = COALESCE(NULLIF(u.email, ''), s.email),
                      u.name  = COALESCE(NULLIF(u.name, ''), s.full_name)
                    WHERE (u.email IS NULL OR u.email = '' OR u.name IS NULL OR u.name = '')
                ");
            }
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
            $this->rebuildSqliteStudentsWithoutIdentityDupes();
            DB::statement('PRAGMA foreign_keys=ON');
            return;
        }

        $studentsEmailUniqueIndex = $this->getUniqueIndexNameForColumn('students', 'email');

        if (Schema::hasColumn('students', 'email') && $studentsEmailUniqueIndex) {
            DB::statement(sprintf(
                'ALTER TABLE `%s` DROP INDEX `%s`',
                'students',
                $studentsEmailUniqueIndex
            ));
        }

        Schema::table('students', function (Blueprint $table) {
            // Remove duplicate identity fields; users is the source of truth.
            if (Schema::hasColumn('students', 'full_name')) {
                $table->dropColumn('full_name');
            }
            if (Schema::hasColumn('students', 'email')) {
                $table->dropColumn('email');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
            $this->rebuildSqliteStudentsWithIdentityDupes();
            DB::statement('PRAGMA foreign_keys=ON');
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'full_name')) {
                $table->string('full_name')->after('student_no');
            }
            if (! Schema::hasColumn('students', 'email')) {
                $table->string('email')->after('dob');
            }
        });

        // Backfill from users.
        DB::statement("
            UPDATE students s
            INNER JOIN users u ON u.id = s.user_id
            SET
              s.full_name = COALESCE(s.full_name, u.name),
              s.email     = COALESCE(s.email, u.email)
        ");

        // Restore uniqueness on students.email (legacy behavior).
        Schema::table('students', function (Blueprint $table) {
            try {
                $table->unique('email', 'students_email_unique');
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }

    private function getUniqueIndexNameForColumn(string $table, string $column): ?string
    {
        if (! Schema::hasTable($table)) {
            return null;
        }

        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return null;
        }

        $rows = DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Column_name = ? AND Non_unique = 0",
            [$column]
        );

        return $rows[0]->Key_name ?? null;
    }

    private function rebuildSqliteStudentsWithoutIdentityDupes(): void
    {
        // SQLite can't DROP COLUMN reliably across all versions, so rebuild.
        Schema::create('students_tmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_no')->unique();
            $table->date('dob')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 50)->nullable();
            $table->string('programme');
            $table->string('intake_year');
            $table->string('previous_institution')->nullable();
            $table->string('previous_qualification')->nullable();
            $table->string('status', 30)->default('active');
            $table->text('notes')->nullable();
            $table->date('enrollment_date')->nullable();
            $table->string('photo')->nullable();
            $table->string('id_card')->nullable();
            $table->string('transcript')->nullable();
            $table->timestamps();
        });

        DB::statement('
            INSERT INTO students_tmp (
                id, user_id, student_no, dob, gender, nationality, phone, address,
                emergency_contact_name, emergency_contact_phone,
                programme, intake_year, previous_institution, previous_qualification,
                status, notes, enrollment_date, photo, id_card, transcript,
                created_at, updated_at
            )
            SELECT
                id, user_id, student_no, dob, gender, nationality, phone, address,
                emergency_contact_name, emergency_contact_phone,
                programme, intake_year, previous_institution, previous_qualification,
                status, notes, enrollment_date, photo, id_card, transcript,
                created_at, updated_at
            FROM students
        ');

        Schema::drop('students');
        Schema::rename('students_tmp', 'students');
    }

    private function rebuildSqliteStudentsWithIdentityDupes(): void
    {
        Schema::create('students_tmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_no')->unique();
            $table->string('full_name');
            $table->date('dob')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('programme');
            $table->string('intake_year');
            $table->timestamps();
        });

        DB::statement("
            INSERT INTO students_tmp (
                id, user_id, student_no, full_name, dob, email, phone, programme, intake_year, created_at, updated_at
            )
            SELECT
                s.id,
                s.user_id,
                s.student_no,
                COALESCE(u.name, ''),
                s.dob,
                COALESCE(u.email, ''),
                s.phone,
                s.programme,
                s.intake_year,
                s.created_at,
                s.updated_at
            FROM students s
            LEFT JOIN users u ON u.id = s.user_id
        ");

        Schema::drop('students');
        Schema::rename('students_tmp', 'students');
    }
};
