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

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'email')) {
                // Drop unique index if present (name varies by driver/version)
                try {
                    $table->dropUnique(['email']);
                } catch (\Throwable $e) {
                    // ignore
                }

                try {
                    $table->dropUnique('students_email_unique');
                } catch (\Throwable $e) {
                    // ignore
                }
            }

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

    private function rebuildSqliteStudentsWithoutIdentityDupes(): void
    {
        // SQLite can't DROP COLUMN reliably across all versions, so rebuild.
        Schema::create('students_tmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_no')->unique();
            $table->date('dob')->nullable();
            $table->string('phone')->nullable();
            $table->string('programme');
            $table->string('intake_year');
            $table->timestamps();
        });

        // Copy common columns that exist.
        $columns = [
            'id',
            'user_id',
            'student_no',
            'dob',
            'phone',
            'programme',
            'intake_year',
            'created_at',
            'updated_at',
        ];

        $select = implode(', ', array_map(fn ($c) => "s.$c", $columns));
        $insert = implode(', ', $columns);

        DB::statement("
            INSERT INTO students_tmp ($insert)
            SELECT $select
            FROM students s
        ");

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

