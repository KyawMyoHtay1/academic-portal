<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Core identity (optional additions)
            $table->string('gender', 20)->nullable()->after('dob');
            $table->string('nationality', 100)->nullable()->after('gender');

            // Contact
            $table->text('address')->nullable()->after('phone');
            $table->string('emergency_contact_name')->nullable()->after('address');
            $table->string('emergency_contact_phone', 50)->nullable()->after('emergency_contact_name');

            // Academic background (proposal-aligned)
            $table->string('previous_institution')->nullable()->after('intake_year');
            $table->string('previous_qualification')->nullable()->after('previous_institution');

            // Administrative fields
            $table->string('status', 30)->default('active')->after('previous_qualification');
            $table->text('notes')->nullable()->after('status');

            // System-managed enrollment date
            $table->date('enrollment_date')->useCurrent()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'nationality',
                'address',
                'emergency_contact_name',
                'emergency_contact_phone',
                'previous_institution',
                'previous_qualification',
                'status',
                'notes',
                'enrollment_date',
            ]);
        });
    }
};

