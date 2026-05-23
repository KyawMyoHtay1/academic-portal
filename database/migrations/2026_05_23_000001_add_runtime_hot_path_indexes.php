<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'name'], 'users_role_name_idx');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index(['receiver_id', 'read', 'created_at'], 'messages_receiver_read_created_idx');
            $table->index(['sender_id', 'receiver_id', 'created_at'], 'messages_sender_receiver_created_idx');
            $table->index(['receiver_id', 'sender_id', 'created_at'], 'messages_receiver_sender_created_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id', 'created_at'], 'notifications_notifiable_created_idx');
            $table->index(['notifiable_type', 'notifiable_id', 'read_at', 'created_at'], 'notifications_notifiable_read_created_idx');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->index(['publish_at', 'expires_at'], 'announcements_publish_expires_idx');
            $table->index(['pinned', 'created_at'], 'announcements_pinned_created_idx');
        });

        Schema::table('announcement_reads', function (Blueprint $table) {
            $table->index(['user_id', 'read_at', 'announcement_id'], 'announcement_reads_user_read_announcement_idx');
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->index(['is_read', 'replied_at', 'created_at'], 'contact_messages_status_created_idx');
        });

        Schema::table('feedback_messages', function (Blueprint $table) {
            $table->index(['is_read', 'replied_at', 'created_at'], 'feedback_messages_status_created_idx');
            $table->index(['type', 'created_at'], 'feedback_messages_type_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('feedback_messages', function (Blueprint $table) {
            $table->dropIndex('feedback_messages_type_created_idx');
            $table->dropIndex('feedback_messages_status_created_idx');
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropIndex('contact_messages_status_created_idx');
        });

        Schema::table('announcement_reads', function (Blueprint $table) {
            $table->dropIndex('announcement_reads_user_read_announcement_idx');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIndex('announcements_pinned_created_idx');
            $table->dropIndex('announcements_publish_expires_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_notifiable_read_created_idx');
            $table->dropIndex('notifications_notifiable_created_idx');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_receiver_sender_created_idx');
            $table->dropIndex('messages_sender_receiver_created_idx');
            $table->dropIndex('messages_receiver_read_created_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_name_idx');
        });
    }
};
