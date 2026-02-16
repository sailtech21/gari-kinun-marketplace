<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create notification types table
        Schema::create('notification_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // system, announcement, message, like, approved, etc.
            $table->string('label'); // Display name
            $table->text('description')->nullable();
            $table->string('icon')->default('fa-bell');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('email_enabled')->default(false); // Send email for this type
            $table->integer('priority')->default(0); // Higher priority shown first
            $table->timestamps();
        });

        // Add sent_email_at to notifications table
        if (!Schema::hasColumn('notifications', 'sent_email_at')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->timestamp('sent_email_at')->nullable()->after('read_at');
            });
        }

        // Add notification_count to users table for tracking
        if (!Schema::hasColumn('users', 'unread_notification_count')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('unread_notification_count')->default(0)->after('email_verified_at');
            });
        }

        // Insert default notification types
        DB::table('notification_types')->insert([
            [
                'name' => 'system',
                'label' => 'System Notification',
                'description' => 'System-wide announcements and updates',
                'icon' => 'fa-cog',
                'is_enabled' => true,
                'email_enabled' => false,
                'priority' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'announcement',
                'label' => 'Announcement',
                'description' => 'Important announcements to users',
                'icon' => 'fa-bullhorn',
                'is_enabled' => true,
                'email_enabled' => true,
                'priority' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'message',
                'label' => 'Message',
                'description' => 'Direct messages from other users',
                'icon' => 'fa-envelope',
                'is_enabled' => true,
                'email_enabled' => true,
                'priority' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'like',
                'label' => 'Like',
                'description' => 'Someone liked your listing',
                'icon' => 'fa-heart',
                'is_enabled' => true,
                'email_enabled' => false,
                'priority' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'approved',
                'label' => 'Approved',
                'description' => 'Listing or account approval notifications',
                'icon' => 'fa-check-circle',
                'is_enabled' => true,
                'email_enabled' => true,
                'priority' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'rejected',
                'label' => 'Rejected',
                'description' => 'Listing or account rejection notifications',
                'icon' => 'fa-times-circle',
                'is_enabled' => true,
                'email_enabled' => true,
                'priority' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_types');
        
        if (Schema::hasColumn('notifications', 'sent_email_at')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('sent_email_at');
            });
        }
        
        if (Schema::hasColumn('users', 'unread_notification_count')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('unread_notification_count');
            });
        }
    }
};
