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
        Schema::table('listings', function (Blueprint $table) {
            // Add is_boosted column if not exists
            if (!Schema::hasColumn('listings', 'is_boosted')) {
                $table->boolean('is_boosted')->default(false)->after('is_featured');
            }
            
            // Add boosted_until column if not exists
            if (!Schema::hasColumn('listings', 'boosted_until')) {
                $table->timestamp('boosted_until')->nullable()->after('is_boosted');
            }
            
            // Add is_hidden column if not exists
            if (!Schema::hasColumn('listings', 'is_hidden')) {
                $table->boolean('is_hidden')->default(false)->after('boosted_until');
            }
            
            // Add rejection_reason column if not exists
            if (!Schema::hasColumn('listings', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('is_hidden');
            }
            
            // Add expires_at column if not exists
            if (!Schema::hasColumn('listings', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('rejection_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['is_boosted', 'boosted_until', 'is_hidden', 'rejection_reason', 'expires_at']);
        });
    }
};
