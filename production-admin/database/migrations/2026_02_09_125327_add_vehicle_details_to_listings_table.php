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
            $table->string('phone')->nullable()->after('location');
            $table->string('video_link')->nullable()->after('phone');
            $table->string('slug')->unique()->nullable()->after('title');
            
            // Vehicle details
            $table->enum('condition', ['Used', 'New', 'Reconditioned'])->nullable()->after('video_link');
            $table->string('model')->nullable()->after('condition');
            $table->year('year_of_manufacture')->nullable()->after('model');
            $table->integer('engine_capacity')->nullable()->after('year_of_manufacture'); // in cc
            $table->enum('transmission', ['Manual', 'Automatic', 'Other'])->nullable()->after('engine_capacity');
            $table->year('registration_year')->nullable()->after('transmission');
            $table->string('brand')->nullable()->after('registration_year');
            $table->string('trim_edition')->nullable()->after('brand');
            $table->integer('kilometers_run')->nullable()->after('trim_edition'); // in km
            $table->string('fuel_type')->nullable()->after('kilometers_run');
            $table->string('body_type')->nullable()->after('fuel_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'video_link',
                'slug',
                'condition',
                'model',
                'year_of_manufacture',
                'engine_capacity',
                'transmission',
                'registration_year',
                'brand',
                'trim_edition',
                'kilometers_run',
                'fuel_type',
                'body_type'
            ]);
        });
    }
};
