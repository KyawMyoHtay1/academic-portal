<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Timetables should not contain duplicate rows for the same subject and time slot.
     */
    public function up(): void
    {
        if (! Schema::hasTable('timetables')) {
            return;
        }

        Schema::table('timetables', function (Blueprint $table) {
            // Ensure we can key on subject + slot; older installs might be missing subject_id.
            if (! Schema::hasColumn('timetables', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->constrained()->cascadeOnDelete();
            }
        });

        Schema::table('timetables', function (Blueprint $table) {
            // Add a uniqueness rule that prevents exact duplicates.
            // (We still rely on application-level conflict checks for overlaps.)
            if (! $this->hasIndex('timetables', 'timetables_subject_slot_unique')) {
                $table->unique(['subject_id', 'day_of_week', 'start_time', 'end_time'], 'timetables_subject_slot_unique');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('timetables')) {
            return;
        }

        Schema::table('timetables', function (Blueprint $table) {
            if ($this->hasIndex('timetables', 'timetables_subject_slot_unique')) {
                $table->dropUnique('timetables_subject_slot_unique');
            }
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $rows = DB::select("PRAGMA index_list('$table')");
            foreach ($rows as $row) {
                if (($row->name ?? null) === $indexName) {
                    return true;
                }
            }

            return false;
        }

        // MySQL/MariaDB
        $database = DB::getDatabaseName();
        $rows = DB::select(
            'SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1',
            [$database, $table, $indexName]
        );

        return count($rows) > 0;
    }
};

