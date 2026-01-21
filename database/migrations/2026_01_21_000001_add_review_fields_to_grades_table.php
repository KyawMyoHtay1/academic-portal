<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('approved')
                ->after('score');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->after('graded_by')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->string('rejection_reason', 255)->nullable()->after('reviewed_at');
        });

        // Existing grades should remain visible after adding the workflow.
        DB::table('grades')->whereNull('status')->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            try {
                $table->dropForeign(['reviewed_by']);
            } catch (\Exception $e) {
                // ignore
            }

            $table->dropColumn(['status', 'reviewed_by', 'reviewed_at', 'rejection_reason']);
        });
    }
};

