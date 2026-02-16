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
        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
                $table->foreign('parent_id')->references('id')->on('locations')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('locations', 'type')) {
                $table->enum('type', ['division', 'district'])->default('district')->after('parent_id');
            }
            
            if (!Schema::hasColumn('locations', 'order')) {
                $table->integer('order')->default(0)->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (Schema::hasColumn('locations', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            
            if (Schema::hasColumn('locations', 'type')) {
                $table->dropColumn('type');
            }
            
            if (Schema::hasColumn('locations', 'order')) {
                $table->dropColumn('order');
            }
        });
    }
};
