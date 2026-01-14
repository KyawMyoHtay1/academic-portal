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
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('priority')->default('info')->after('body'); // info|important|urgent
            $table->boolean('pinned')->default(false)->after('priority');
            $table->boolean('require_ack')->default(false)->after('pinned');

            // Delivery / targeting (start simple: role-based targeting via JSON)
            // Example: {"roles":["all","student","teacher","staff"]}
            $table->json('audience')->nullable()->after('require_ack');

            // Scheduling / lifecycle
            $table->timestamp('publish_at')->nullable()->after('audience');
            $table->timestamp('expires_at')->nullable()->after('publish_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn([
                'priority',
                'pinned',
                'require_ack',
                'audience',
                'publish_at',
                'expires_at',
            ]);
        });
    }
};
