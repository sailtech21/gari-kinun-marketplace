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
        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'reported_user_id')) {
                $table->unsignedBigInteger('reported_user_id')->nullable()->after('listing_id');
                $table->foreign('reported_user_id')->references('id')->on('users')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('reports', 'report_type')) {
                $table->enum('report_type', ['listing', 'user'])->default('listing')->after('reported_user_id');
            }
            
            if (!Schema::hasColumn('reports', 'action_taken')) {
                $table->string('action_taken')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('reports', 'action_by')) {
                $table->unsignedBigInteger('action_by')->nullable()->after('action_taken');
                $table->foreign('action_by')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('reports', 'action_date')) {
                $table->timestamp('action_date')->nullable()->after('action_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'action_date')) {
                $table->dropColumn('action_date');
            }
            
            if (Schema::hasColumn('reports', 'action_by')) {
                $table->dropForeign(['action_by']);
                $table->dropColumn('action_by');
            }
            
            if (Schema::hasColumn('reports', 'action_taken')) {
                $table->dropColumn('action_taken');
            }
            
            if (Schema::hasColumn('reports', 'report_type')) {
                $table->dropColumn('report_type');
            }
            
            if (Schema::hasColumn('reports', 'reported_user_id')) {
                $table->dropForeign(['reported_user_id']);
                $table->dropColumn('reported_user_id');
            }
        });
    }
};
