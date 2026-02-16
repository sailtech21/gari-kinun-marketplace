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
            if (!Schema::hasColumn('banners', 'priority')) {
                $table->integer('priority')->default(0)->after('order');
            }
            
            if (!Schema::hasColumn('banners', 'scheduled_start')) {
                $table->timestamp('scheduled_start')->nullable()->after('starts_at');
            }
            
            if (!Schema::hasColumn('banners', 'scheduled_end')) {
                $table->timestamp('scheduled_end')->nullable()->after('scheduled_start');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'priority')) {
                $table->dropColumn('priority');
            }
            
            if (Schema::hasColumn('banners', 'scheduled_start')) {
                $table->dropColumn('scheduled_start');
            }
            
            if (Schema::hasColumn('banners', 'scheduled_end')) {
                $table->dropColumn('scheduled_end');
            }
        });
    }
};
