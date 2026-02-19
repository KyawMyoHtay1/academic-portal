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
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('attendance_threshold', 5, 2)->nullable()->after('semester')
                ->comment('Minimum attendance percentage threshold for this course. Null uses global threshold.');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->decimal('attendance_threshold', 5, 2)->nullable()->after('photo')
                ->comment('Minimum attendance percentage threshold for this subject. Null uses course or global threshold.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('attendance_threshold');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('attendance_threshold');
        });
    }
};
