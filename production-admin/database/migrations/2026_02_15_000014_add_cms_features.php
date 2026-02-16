<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add CMS fields to pages table
        if (!Schema::hasColumn('pages', 'images')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->json('images')->nullable()->after('content');
                $table->text('excerpt')->nullable()->after('content');
                $table->string('featured_image')->nullable()->after('content');
            });
        }
        
        // Create site_settings table for header, footer, etc.
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, json, image, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('site_settings')->insert([
            [
                'key' => 'header_links',
                'value' => json_encode([
                    ['label' => 'Home', 'url' => '/', 'order' => 1],
                    ['label' => 'About', 'url' => '/about', 'order' => 2],
                    ['label' => 'Contact', 'url' => '/contact', 'order' => 3],
                ]),
                'type' => 'json',
                'description' => 'Header navigation links',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'footer_content',
                'value' => '<p>&copy; 2026 GariKinun. All rights reserved.</p>',
                'type' => 'html',
                'description' => 'Footer content',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'footer_links',
                'value' => json_encode([
                    ['label' => 'Privacy Policy', 'url' => '/privacy', 'order' => 1],
                    ['label' => 'Terms & Conditions', 'url' => '/terms', 'order' => 2],
                    ['label' => 'FAQ', 'url' => '/faq', 'order' => 3],
                ]),
                'type' => 'json',
                'description' => 'Footer links',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'image',
                'description' => 'Site logo image',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@garikinun.com',
                'type' => 'text',
                'description' => 'Contact email',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_phone',
                'value' => '+880 1234567890',
                'type' => 'text',
                'description' => 'Contact phone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/garikinun',
                'type' => 'text',
                'description' => 'Facebook URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/garikinun',
                'type' => 'text',
                'description' => 'Twitter URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert default pages if they don't exist
        $defaultPages = [
            [
                'title' => 'Homepage',
                'slug' => 'homepage',
                'content' => '<h1>Welcome to GariKinun</h1><p>Bangladesh\'s leading automobile marketplace.</p>',
                'type' => 'page',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'About Us',
                'slug' => 'about',
                'content' => '<h1>About GariKinun</h1><p>Learn more about our mission and vision.</p>',
                'type' => 'page',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms',
                'content' => '<h1>Terms & Conditions</h1><p>Please read these terms carefully.</p>',
                'type' => 'policy',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy',
                'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us.</p>',
                'type' => 'policy',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'title' => 'FAQ',
                'slug' => 'faq',
                'content' => '<h1>Frequently Asked Questions</h1><p>Find answers to common questions.</p>',
                'type' => 'help',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'content' => '<h1>Contact Us</h1><p>Get in touch with our team.</p>',
                'type' => 'page',
                'is_active' => true,
                'order' => 6,
            ],
        ];

        foreach ($defaultPages as $pageData) {
            $exists = DB::table('pages')->where('slug', $pageData['slug'])->exists();
            if (!$exists) {
                DB::table('pages')->insert(array_merge($pageData, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        
        if (Schema::hasColumn('pages', 'images')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn(['images', 'excerpt', 'featured_image']);
            });
        }
    }
};
