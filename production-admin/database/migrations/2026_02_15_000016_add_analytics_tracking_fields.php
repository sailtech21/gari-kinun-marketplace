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
        // Add traffic source to listings
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'clicks')) {
                $table->integer('clicks')->default(0)->after('views');
            }
            if (!Schema::hasColumn('listings', 'phone_reveals')) {
                $table->integer('phone_reveals')->default(0)->after('clicks');
            }
            if (!Schema::hasColumn('listings', 'conversions')) {
                $table->integer('conversions')->default(0)->after('phone_reveals');
            }
        });

        // Add analytics tracking table for sessions/traffic
        if (!Schema::hasTable('analytics_sessions')) {
            Schema::create('analytics_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('session_id')->index();
                $table->string('traffic_source')->nullable(); // google, facebook, direct, referral
                $table->string('referrer_url')->nullable();
                $table->string('landing_page')->nullable();
                $table->string('device_type')->nullable(); // mobile, desktop, tablet
                $table->string('browser')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('country')->nullable();
                $table->string('city')->nullable();
                $table->integer('page_views')->default(1);
                $table->timestamp('first_visit');
                $table->timestamp('last_activity');
                $table->timestamps();
            });
        }

        // Add listing views tracking for detailed analytics
        if (!Schema::hasTable('listing_views')) {
            Schema::create('listing_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('listing_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('session_id')->index();
                $table->string('ip_address')->nullable();
                $table->string('traffic_source')->nullable();
                $table->timestamp('viewed_at');
                $table->timestamps();
                
                $table->index(['listing_id', 'viewed_at']);
            });
        }

        // Add user activity tracking
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'total_listings')) {
                $table->integer('total_listings')->default(0)->after('role');
            }
            if (!Schema::hasColumn('users', 'total_views')) {
                $table->integer('total_views')->default(0)->after('total_listings');
            }
            if (!Schema::hasColumn('users', 'total_revenue')) {
                $table->decimal('total_revenue', 10, 2)->default(0)->after('total_views');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['clicks', 'phone_reveals', 'conversions']);
        });

        Schema::dropIfExists('listing_views');
        Schema::dropIfExists('analytics_sessions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_listings', 'total_views', 'total_revenue']);
        });
    }
};
