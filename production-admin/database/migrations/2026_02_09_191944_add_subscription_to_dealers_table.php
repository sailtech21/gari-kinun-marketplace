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
        Schema::table('dealers', function (Blueprint $table) {
            $table->enum('subscription_tier', ['free', 'premium', 'pro'])->default('free')->after('is_verified');
            $table->timestamp('subscription_starts_at')->nullable()->after('subscription_tier');
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_starts_at');
            $table->decimal('subscription_price', 10, 2)->nullable()->after('subscription_ends_at');
            $table->integer('listing_limit')->default(5)->after('subscription_price')->comment('Max listings allowed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn(['subscription_tier', 'subscription_starts_at', 'subscription_ends_at', 'subscription_price', 'listing_limit']);
        });
    }
};
