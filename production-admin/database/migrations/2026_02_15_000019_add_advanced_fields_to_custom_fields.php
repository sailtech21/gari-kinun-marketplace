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
        Schema::table('custom_fields', function (Blueprint $table) {
            if (!Schema::hasColumn('custom_fields', 'show_on_listing_card')) {
                $table->boolean('show_on_listing_card')->default(true)->after('show_in_details');
            }
            if (!Schema::hasColumn('custom_fields', 'allow_multiple_selection')) {
                $table->boolean('allow_multiple_selection')->default(false)->after('is_filterable');
            }
            if (!Schema::hasColumn('custom_fields', 'min_value')) {
                $table->string('min_value')->nullable()->after('default_value');
            }
            if (!Schema::hasColumn('custom_fields', 'max_value')) {
                $table->string('max_value')->nullable()->after('min_value');
            }
            if (!Schema::hasColumn('custom_fields', 'field_group')) {
                $table->string('field_group')->nullable()->after('order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_fields', function (Blueprint $table) {
            if (Schema::hasColumn('custom_fields', 'show_on_listing_card')) {
                $table->dropColumn('show_on_listing_card');
            }
            if (Schema::hasColumn('custom_fields', 'allow_multiple_selection')) {
                $table->dropColumn('allow_multiple_selection');
            }
            if (Schema::hasColumn('custom_fields', 'min_value')) {
                $table->dropColumn('min_value');
            }
            if (Schema::hasColumn('custom_fields', 'max_value')) {
                $table->dropColumn('max_value');
            }
            if (Schema::hasColumn('custom_fields', 'field_group')) {
                $table->dropColumn('field_group');
            }
        });
    }
};
