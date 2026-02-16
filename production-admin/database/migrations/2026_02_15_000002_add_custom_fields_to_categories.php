<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add custom fields JSON column to categories
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->json('custom_fields')->nullable()->after('show_condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
    }
};
