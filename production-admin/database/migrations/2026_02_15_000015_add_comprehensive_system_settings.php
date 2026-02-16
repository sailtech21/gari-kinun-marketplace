<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Insert comprehensive default settings
        $defaultSettings = [
            // General Settings
            ['key' => 'website_name', 'value' => 'GariKinun', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => null, 'type' => 'image', 'group' => 'general'],
            ['key' => 'site_favicon', 'value' => null, 'type' => 'image', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'info@garikinun.com', 'type' => 'email', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => '+880 1234567890', 'type' => 'text', 'group' => 'general'],
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/garikinun', 'type' => 'url', 'group' => 'general'],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com/garikinun', 'type' => 'url', 'group' => 'general'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/garikinun', 'type' => 'url', 'group' => 'general'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@garikinun', 'type' => 'url', 'group' => 'general'],
            ['key' => 'social_linkedin', 'value' => 'https://linkedin.com/company/garikinun', 'type' => 'url', 'group' => 'general'],
            
            // SEO Settings
            ['key' => 'seo_meta_title', 'value' => 'GariKinun - Bangladesh\'s Best Automobile Marketplace', 'type' => 'text', 'group' => 'seo'],
            ['key' => 'seo_meta_description', 'value' => 'Buy and sell new and used cars in Bangladesh. Find your dream vehicle at GariKinun.', 'type' => 'textarea', 'group' => 'seo'],
            ['key' => 'seo_meta_keywords', 'value' => 'cars, vehicles, buy car, sell car, Bangladesh, automobile', 'type' => 'textarea', 'group' => 'seo'],
            ['key' => 'seo_sitemap_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'seo'],
            ['key' => 'google_analytics_code', 'value' => '', 'type' => 'textarea', 'group' => 'seo'],
            ['key' => 'facebook_pixel_code', 'value' => '', 'type' => 'textarea', 'group' => 'seo'],
            ['key' => 'google_tag_manager', 'value' => '', 'type' => 'text', 'group' => 'seo'],
            
            // System Settings
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'system'],
            ['key' => 'maintenance_message', 'value' => 'We are currently under maintenance. Please check back soon.', 'type' => 'textarea', 'group' => 'system'],
            ['key' => 'auto_approve_ads', 'value' => '0', 'type' => 'boolean', 'group' => 'system'],
            ['key' => 'auto_expire_ads', 'value' => '1', 'type' => 'boolean', 'group' => 'system'],
            ['key' => 'ads_expire_days', 'value' => '30', 'type' => 'number', 'group' => 'system'],
            ['key' => 'max_images_per_ad', 'value' => '10', 'type' => 'number', 'group' => 'system'],
            ['key' => 'max_ads_per_user', 'value' => '50', 'type' => 'number', 'group' => 'system'],
            ['key' => 'max_ads_per_day', 'value' => '5', 'type' => 'number', 'group' => 'system'],
            ['key' => 'enable_chat', 'value' => '1', 'type' => 'boolean', 'group' => 'system'],
            ['key' => 'enable_guest_posting', 'value' => '0', 'type' => 'boolean', 'group' => 'system'],
            ['key' => 'require_email_verification', 'value' => '1', 'type' => 'boolean', 'group' => 'system'],
            ['key' => 'require_phone_verification', 'value' => '0', 'type' => 'boolean', 'group' => 'system'],
            
            // Pricing Settings
            ['key' => 'featured_ad_price', 'value' => '500', 'type' => 'number', 'group' => 'pricing'],
            ['key' => 'featured_ad_days', 'value' => '7', 'type' => 'number', 'group' => 'pricing'],
            ['key' => 'boost_ad_price', 'value' => '200', 'type' => 'number', 'group' => 'pricing'],
            ['key' => 'boost_ad_days', 'value' => '3', 'type' => 'number', 'group' => 'pricing'],
            ['key' => 'premium_dealer_price', 'value' => '5000', 'type' => 'number', 'group' => 'pricing'],
            ['key' => 'premium_dealer_months', 'value' => '1', 'type' => 'number', 'group' => 'pricing'],
            ['key' => 'currency_symbol', 'value' => 'BDT', 'type' => 'text', 'group' => 'pricing'],
            
            // Email Settings
            ['key' => 'smtp_host', 'value' => 'smtp.mailtrap.io', 'type' => 'text', 'group' => 'email'],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'text', 'group' => 'email'],
            ['key' => 'smtp_username', 'value' => '', 'type' => 'text', 'group' => 'email'],
            ['key' => 'smtp_password', 'value' => '', 'type' => 'password', 'group' => 'email'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'text', 'group' => 'email'],
            ['key' => 'mail_from_address', 'value' => 'noreply@garikinun.com', 'type' => 'email', 'group' => 'email'],
            ['key' => 'mail_from_name', 'value' => 'GariKinun', 'type' => 'text', 'group' => 'email'],
        ];

        foreach ($defaultSettings as $setting) {
            $exists = DB::table('settings')->where('key', $setting['key'])->exists();
            if (!$exists) {
                DB::table('settings')->insert(array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        // Remove settings added by this migration
        $keys = [
            'website_name', 'site_logo', 'site_favicon', 'contact_email', 'contact_phone',
            'social_facebook', 'social_twitter', 'social_instagram', 'social_youtube', 'social_linkedin',
            'seo_meta_title', 'seo_meta_description', 'seo_meta_keywords', 'seo_sitemap_enabled',
            'google_analytics_code', 'facebook_pixel_code', 'google_tag_manager',
            'maintenance_mode', 'maintenance_message', 'auto_approve_ads', 'auto_expire_ads', 'ads_expire_days',
            'max_images_per_ad', 'max_ads_per_user', 'max_ads_per_day',
            'enable_chat', 'enable_guest_posting', 'require_email_verification', 'require_phone_verification',
            'featured_ad_price', 'featured_ad_days', 'boost_ad_price', 'boost_ad_days',
            'premium_dealer_price', 'premium_dealer_months', 'currency_symbol',
            'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption',
            'mail_from_address', 'mail_from_name',
        ];
        
        DB::table('settings')->whereIn('key', $keys)->delete();
    }
};
