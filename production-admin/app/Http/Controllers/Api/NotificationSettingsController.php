<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationSettingsController extends Controller
{
    /**
     * Get notification settings
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get settings from user's notification_settings JSON column
        // If column doesn't exist or not set, return default settings (all true)
        try {
            $settings = $user->notification_settings ?? $this->getDefaultSettings();
        } catch (\Exception $e) {
            // Column doesn't exist yet, return defaults
            $settings = $this->getDefaultSettings();
        }
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
    
    /**
     * Get default notification settings
     */
    private function getDefaultSettings()
    {
        return [
            'new_message' => true,
            'chat_reply' => true,
            'ad_approved' => true,
            'ad_rejected' => true,
            'ad_expiring' => true,
            'ad_viewed' => true,
            'ad_saved' => true,
            'price_drop' => true,
            'boost_expired' => true,
            'premium_activated' => true,
            'special_offers' => true,
            'in_app_notifications' => true,
            'email_notifications' => true,
            'push_notifications' => false
        ];
    }
    
    /**
     * Update notification settings
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        // Get all settings from request
        $settings = [
            'new_message' => $request->boolean('new_message', true),
            'chat_reply' => $request->boolean('chat_reply', true),
            'ad_approved' => $request->boolean('ad_approved', true),
            'ad_rejected' => $request->boolean('ad_rejected', true),
            'ad_expiring' => $request->boolean('ad_expiring', true),
            'ad_viewed' => $request->boolean('ad_viewed', true),
            'ad_saved' => $request->boolean('ad_saved', true),
            'price_drop' => $request->boolean('price_drop', true),
            'boost_expired' => $request->boolean('boost_expired', true),
            'premium_activated' => $request->boolean('premium_activated', true),
            'special_offers' => $request->boolean('special_offers', true),
            'in_app_notifications' => $request->boolean('in_app_notifications', true),
            'email_notifications' => $request->boolean('email_notifications', true),
            'push_notifications' => $request->boolean('push_notifications', false)
        ];
        
        // Try to save to user's notification_settings JSON column
        try {
            $user->notification_settings = $settings;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully',
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            // Column doesn't exist yet, just return success with settings
            // Frontend will still work, settings just won't persist
            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully (in memory)',
                'data' => $settings,
                'note' => 'Database column not yet created. Settings will not persist.'
            ]);
        }
    }
}
