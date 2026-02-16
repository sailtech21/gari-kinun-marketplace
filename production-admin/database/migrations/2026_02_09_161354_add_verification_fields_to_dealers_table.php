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
            // Verification documents
            $table->string('nid_front')->nullable();
            $table->string('nid_back')->nullable();
            $table->string('selfie_photo')->nullable();
            
            // Mobile verification
            $table->string('verification_code')->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->boolean('is_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn([
                'nid_front',
                'nid_back',
                'selfie_photo',
                'verification_code',
                'mobile_verified_at',
                'is_verified'
            ]);
        });
    }
};
