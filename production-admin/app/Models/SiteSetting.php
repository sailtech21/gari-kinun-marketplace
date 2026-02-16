<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    // Helper method to get a setting value
    public static function get($key, $default = null)
    {
        try {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            // Decode JSON if type is json
            if ($setting->type === 'json') {
                return json_decode($setting->value, true);
            }

            return $setting->value;
        } catch (\Exception $e) {
            \Log::error('SiteSetting get error: ' . $e->getMessage());
            return $default;
        }
    }

    // Helper method to set a setting value
    public static function set($key, $value, $type = 'text')
    {
        try {
            // Encode to JSON if value is array
            if (is_array($value)) {
                $value = json_encode($value);
                $type = 'json';
            }

            return self::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => $type]
            );
        } catch (\Exception $e) {
            \Log::error('SiteSetting set error: ' . $e->getMessage());
            return null;
        }
    }

    // Get all header links
    public static function getHeaderLinks()
    {
        try {
            return self::get('header_links', []);
        } catch (\Exception $e) {
            return [];
        }
    }

    // Get all footer links
    public static function getFooterLinks()
    {
        try {
            return self::get('footer_links', []);
        } catch (\Exception $e) {
            return [];
        }
    }

    // Get footer content
    public static function getFooterContent()
    {
        try {
            return self::get('footer_content', '');
        } catch (\Exception $e) {
            return '';
        }
    }
}
