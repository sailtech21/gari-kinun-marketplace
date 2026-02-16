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
        Schema::table('categories', function (Blueprint $table) {
            // Add posting_fee column if not exists
            if (!Schema::hasColumn('categories', 'posting_fee')) {
                $table->decimal('posting_fee', 10, 2)->default(0)->after('custom_fields');
            }
            
            // Add required_fields column if not exists
            if (!Schema::hasColumn('categories', 'required_fields')) {
                $table->json('required_fields')->nullable()->after('posting_fee');
            }
            
            // Add order column if not exists
            if (!Schema::hasColumn('categories', 'order')) {
                $table->integer('order')->default(0)->after('required_fields');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['posting_fee', 'required_fields', 'order']);
        });
    }
};
