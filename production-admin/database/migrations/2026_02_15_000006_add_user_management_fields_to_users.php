<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'suspended', 'banned'])->default('active')->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'is_premium')) {
                $table->boolean('is_premium')->default(false)->after('status');
            }
            if (!Schema::hasColumn('users', 'can_post')) {
                $table->boolean('can_post')->default(true)->after('is_premium');
            }
            if (!Schema::hasColumn('users', 'listing_limit')) {
                $table->integer('listing_limit')->nullable()->after('can_post');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'is_premium')) {
                $table->dropColumn('is_premium');
            }
            if (Schema::hasColumn('users', 'can_post')) {
                $table->dropColumn('can_post');
            }
            if (Schema::hasColumn('users', 'listing_limit')) {
                $table->dropColumn('listing_limit');
            }
        });
    }
};
