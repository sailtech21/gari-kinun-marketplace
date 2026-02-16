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
            // Drop unique constraint on email first
            $table->dropUnique(['email']);
        });
        
        // For SQLite, we need to recreate the columns
        Schema::table('dealers', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['name', 'email', 'phone', 'address', 'applied_at']);
        });
        
        Schema::table('dealers', function (Blueprint $table) {
            // Recreate them as nullable
            $table->string('name')->nullable()->after('id');
            $table->string('email')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->dateTime('applied_at')->nullable()->after('business_license');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            // Drop nullable columns
            $table->dropColumn(['name', 'email', 'phone', 'address', 'applied_at']);
        });
        
        Schema::table('dealers', function (Blueprint $table) {
            // Recreate them as required
            $table->string('name')->after('id');
            $table->string('email')->unique()->after('name');
            $table->string('phone')->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->dateTime('applied_at')->after('business_license');
        });
    }
};
