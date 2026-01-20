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
        Schema::table('fees', function (Blueprint $table) {
            $table->string('payment_intent_id')->nullable()->after('paid_date');
            $table->string('payment_method')->nullable()->after('payment_intent_id');
            $table->timestamp('payment_processed_at')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn(['payment_intent_id', 'payment_method', 'payment_processed_at']);
        });
    }
};
