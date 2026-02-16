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
            $table->timestamp('featured_until')->nullable()->after('is_featured');
            $table->decimal('featured_price', 10, 2)->nullable()->after('featured_until')->comment('Price paid for featuring');
            $table->timestamp('featured_at')->nullable()->after('featured_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['featured_until', 'featured_price', 'featured_at']);
        });
    }
};
