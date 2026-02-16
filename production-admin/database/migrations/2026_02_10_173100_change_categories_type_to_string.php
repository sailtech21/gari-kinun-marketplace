<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support MODIFY, so we need to recreate the table
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we'll skip this as it requires complex table recreation
            // The type column will work as-is for SQLite
            return;
        }
        
        // Change type column from enum to string for more flexibility
        DB::statement("ALTER TABLE `categories` MODIFY `type` VARCHAR(50) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        
        // Revert back to enum
        DB::statement("ALTER TABLE `categories` MODIFY `type` ENUM('Car', 'Bike') NOT NULL");
    }
};
