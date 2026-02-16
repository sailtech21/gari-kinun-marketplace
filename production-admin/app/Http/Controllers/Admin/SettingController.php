<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function getAll()
    {
        $settings = Setting::all()->groupBy('group');
        
        $formatted = [];
        foreach ($settings as $group => $items) {
            $formatted[$group] = $items->pluck('value', 'key')->toArray();
        }
        
        return response()->json([
            'success' => true,
            'settings' => $formatted
        ]);
    }

    public function getGroup($group)
    {
        $settings = Setting::where('group', $group)->get()->pluck('value', 'key');
        
        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->settings as $key => $value) {
            // Determine type based on key
            $type = 'text';
            if (strpos($key, '_enabled') !== false || strpos($key, 'enable_') !== false || 
                strpos($key, 'auto_') !== false || strpos($key, 'require_') !== false ||
                $key === 'maintenance_mode' || $key === 'seo_sitemap_enabled') {
                $type = 'boolean';
                $value = $value ? '1' : '0';
            } elseif (strpos($key, 'price') !== false || strpos($key, 'max_') !== false || 
                     strpos($key, '_days') !== false || strpos($key, '_months') !== false) {
                $type = 'number';
            } elseif (strpos($key, 'email') !== false) {
                $type = 'email';
            } elseif (strpos($key, 'social_') !== false || strpos($key, 'url') !== false) {
                $type = 'url';
            } elseif (strpos($key, 'description') !== false || strpos($key, 'message') !== false || 
                     strpos($key, 'code') !== false || strpos($key, 'keywords') !== false) {
                $type = 'textarea';
            } elseif (strpos($key, 'password') !== false) {
                $type = 'password';
            }

            // Determine group
            $group = 'general';
            if (strpos($key, 'seo_') !== false || strpos($key, 'google_') !== false || 
                strpos($key, 'facebook_pixel') !== false) {
                $group = 'seo';
            } elseif (strpos($key, 'maintenance_') !== false || strpos($key, 'auto_') !== false || 
                     strpos($key, 'max_') !== false || strpos($key, 'enable_') !== false || 
                     strpos($key, 'require_') !== false || strpos($key, '_expire') !== false) {
                $group = 'system';
            } elseif (strpos($key, 'price') !== false || strpos($key, 'currency') !== false || 
                     strpos($key, 'premium_') !== false || strpos($key, 'featured_') !== false || 
                     strpos($key, 'boost_') !== false) {
                $group = 'pricing';
            } elseif (strpos($key, 'smtp_') !== false || strpos($key, 'mail_') !== false) {
                $group = 'email';
            } elseif (strpos($key, 'social_') !== false) {
                $group = 'general';
            }

            Setting::set($key, $value, $type, $group);
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $path = $logo->storeAs('settings', $filename, 'public');

            // Delete old logo
            $oldLogo = Setting::get('site_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            Setting::set('site_logo', $path, 'image', 'general');

            return response()->json([
                'success' => true,
                'message' => 'Logo uploaded successfully',
                'path' => $path,
                'url' => Storage::url($path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No logo file found'
        ], 400);
    }

    public function uploadFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|image|mimes:png,ico,jpg|max:1024'
        ]);

        if ($request->hasFile('favicon')) {
            $favicon = $request->file('favicon');
            $filename = 'favicon_' . time() . '.' . $favicon->getClientOriginalExtension();
            $path = $favicon->storeAs('settings', $filename, 'public');

            // Delete old favicon
            $oldFavicon = Setting::get('site_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }

            Setting::set('site_favicon', $path, 'image', 'general');

            return response()->json([
                'success' => true,
                'message' => 'Favicon uploaded successfully',
                'path' => $path,
                'url' => Storage::url($path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No favicon file found'
        ], 400);
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'to_email' => 'required|email'
        ]);

        try {
            \Mail::raw('This is a test email from GariKinun.', function ($message) use ($request) {
                $message->to($request->to_email)
                       ->subject('Test Email from GariKinun');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $request->to_email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearCache()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');

            return response()->json([
                'success' => true,
                'message' => 'All caches cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stats()
    {
        $stats = [
            'total_settings' => Setting::count(),
            'by_group' => Setting::select('group', \DB::raw('count(*) as count'))
                ->groupBy('group')
                ->get()
                ->pluck('count', 'group'),
        ];

        return response()->json($stats);
    }
}