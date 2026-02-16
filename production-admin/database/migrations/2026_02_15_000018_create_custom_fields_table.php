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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Field name (e.g., "Brand", "Model")
            $table->string('slug')->unique(); // URL-friendly name (e.g., "brand", "model")
            $table->string('type'); // text, textarea, number, select, radio, checkbox, date
            $table->json('options')->nullable(); // For select, radio, checkbox fields
            $table->string('placeholder')->nullable();
            $table->string('default_value')->nullable();
            $table->text('help_text')->nullable();
            $table->json('validation_rules')->nullable(); // min, max, regex, etc.
            
            // Visibility controls
            $table->boolean('show_in_add_form')->default(true); // Show when adding/editing ad
            $table->boolean('show_in_search')->default(true); // Show in search filters
            $table->boolean('show_in_details')->default(true); // Show in ad details page
            
            // Other controls
            $table->boolean('is_required')->default(false);
            $table->boolean('is_searchable')->default(true); // Can be used in search queries
            $table->boolean('is_filterable')->default(true); // Can be used as filter
            $table->integer('order')->default(0); // Display order
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });

        // Pivot table for category-field relationship
        Schema::create('category_custom_field', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('custom_field_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(false); // Can override field's default
            $table->timestamps();
            
            $table->unique(['category_id', 'custom_field_id']);
        });

        // Add custom_fields column to listings table if not exists
        if (!Schema::hasColumn('listings', 'custom_field_values')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->json('custom_field_values')->nullable()->after('images');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('listings', 'custom_field_values')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->dropColumn('custom_field_values');
            });
        }

        Schema::dropIfExists('category_custom_field');
        Schema::dropIfExists('custom_fields');
    }
};
