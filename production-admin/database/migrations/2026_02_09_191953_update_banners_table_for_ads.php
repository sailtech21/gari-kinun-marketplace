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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('advertiser_name')->nullable()->after('link');
            $table->string('advertiser_email')->nullable()->after('advertiser_name');
            $table->string('advertiser_phone')->nullable()->after('advertiser_email');
            $table->decimal('monthly_price', 10, 2)->default(7000)->after('advertiser_phone');
            $table->timestamp('starts_at')->nullable()->after('monthly_price');
            $table->timestamp('ends_at')->nullable()->after('starts_at');
            $table->boolean('is_paid')->default(false)->after('ends_at');
            $table->integer('clicks')->default(0)->after('is_paid');
            $table->integer('impressions')->default(0)->after('clicks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['advertiser_name', 'advertiser_email', 'advertiser_phone', 'monthly_price', 'starts_at', 'ends_at', 'is_paid', 'clicks', 'impressions']);
        });
    }
};
