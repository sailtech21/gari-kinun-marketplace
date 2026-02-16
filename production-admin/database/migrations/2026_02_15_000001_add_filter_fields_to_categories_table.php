<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add category-specific filter control fields
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('show_fuel_type')->default(true)->after('is_active');
            $table->boolean('show_transmission')->default(true)->after('show_fuel_type');
            $table->boolean('show_body_type')->default(true)->after('show_transmission');
            $table->boolean('show_year')->default(true)->after('show_body_type');
            $table->boolean('show_mileage')->default(true)->after('show_year');
            $table->boolean('show_engine_capacity')->default(true)->after('show_mileage');
            $table->boolean('show_condition')->default(true)->after('show_engine_capacity');
            $table->text('description')->nullable()->after('icon');
            $table->string('slug')->nullable()->unique()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'show_fuel_type',
                'show_transmission',
                'show_body_type',
                'show_year',
                'show_mileage',
                'show_engine_capacity',
                'show_condition',
                'description',
                'slug'
            ]);
        });
    }
};
