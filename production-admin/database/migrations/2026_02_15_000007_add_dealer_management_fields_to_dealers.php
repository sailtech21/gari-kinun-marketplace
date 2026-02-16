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
            // Add badge column if not exists
            if (!Schema::hasColumn('dealers', 'badge')) {
                $table->enum('badge', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze')->after('subscription_tier');
            }
            
            // Add is_featured column if not exists
            if (!Schema::hasColumn('dealers', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_verified');
            }
            
            // Add is_suspended column if not exists
            if (!Schema::hasColumn('dealers', 'is_suspended')) {
                $table->boolean('is_suspended')->default(false)->after('is_featured');
            }
            
            // Add total_revenue column if not exists
            if (!Schema::hasColumn('dealers', 'total_revenue')) {
                $table->decimal('total_revenue', 10, 2)->default(0)->after('subscription_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn(['badge', 'is_featured', 'is_suspended', 'total_revenue']);
        });
    }
};
