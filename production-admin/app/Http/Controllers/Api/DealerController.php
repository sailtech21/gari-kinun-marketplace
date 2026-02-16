<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DealerController extends Controller
{
    /**
     * Get dealer status for current user
     */
    public function status(Request $request)
    {
        $dealer = Dealer::where('user_id', $request->user()->id)->first();
        
        return response()->json([
            'success' => true,
            'data' => $dealer
        ]);
    }
    
    /**
     * Submit dealer application with verification documents
     */
    public function apply(Request $request)
    {
        // Check if already has a dealer application
        $existingDealer = Dealer::where('user_id', $request->user()->id)->first();
        if ($existingDealer) {
            return response()->json([
                'success' => false,
                'message' => 'আপনার ডিলার আবেদন ইতিমধ্যে জমা হয়েছে। অনুগ্রহ করে অনুমোদনের জন্য অপেক্ষা করুন।'
            ], 400);
        }
        
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_address' => 'required|string',
            'business_phone' => 'required|string|max:20',
            'business_license' => 'nullable|string|max:255',
            'nid_front' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'nid_back' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'selfie_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Upload NID Front
        $nidFrontPath = null;
        if ($request->hasFile('nid_front')) {
            $nidFrontFile = $request->file('nid_front');
            $nidFrontName = 'nid_front_' . time() . '_' . Str::random(10) . '.' . $nidFrontFile->extension();
            $nidFrontPath = $nidFrontFile->storeAs('dealer_documents', $nidFrontName, 'public');
        }
        
        // Upload NID Back
        $nidBackPath = null;
        if ($request->hasFile('nid_back')) {
            $nidBackFile = $request->file('nid_back');
            $nidBackName = 'nid_back_' . time() . '_' . Str::random(10) . '.' . $nidBackFile->extension();
            $nidBackPath = $nidBackFile->storeAs('dealer_documents', $nidBackName, 'public');
        }
        
        // Upload Selfie
        $selfiePath = null;
        if ($request->hasFile('selfie_photo')) {
            $selfieFile = $request->file('selfie_photo');
            $selfieName = 'selfie_' . time() . '_' . Str::random(10) . '.' . $selfieFile->extension();
            $selfiePath = $selfieFile->storeAs('dealer_documents', $selfieName, 'public');
        }
        
        $userId = $request->user()->id;
        
        // Check if phone was verified
        $cacheKey = "dealer_verification_{$userId}";
        $verificationData = Cache::get($cacheKey);
        $mobileVerifiedAt = null;
        
        if ($verificationData && $verificationData['verified']) {
            $mobileVerifiedAt = $verificationData['verified_at'];
            // Clear cache after use
            Cache::forget($cacheKey);
        }
        
        $dealer = Dealer::create([
            'user_id' => $userId,
            'business_name' => $validated['business_name'],
            'business_address' => $validated['business_address'],
            'business_phone' => $validated['business_phone'],
            'business_license' => $validated['business_license'] ?? null,
            'nid_front' => $nidFrontPath,
            'nid_back' => $nidBackPath,
            'selfie_photo' => $selfiePath,
            'mobile_verified_at' => $mobileVerifiedAt,
            'status' => 'pending',
            'applied_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'ডিলার আবেদন সফলভাবে জমা হয়েছে। আমরা শীঘ্রই যাচাই করে আপনাকে জানাবো।',
            'data' => $dealer
        ]);
    }
    
    /**
     * Send mobile verification code
     */
    public function sendVerificationCode(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
        ]);
        
        $userId = $request->user()->id;
        
        // Generate 6-digit code
        $code = sprintf("%06d", mt_rand(1, 999999));
        
        // Store verification code in cache for 10 minutes
        $cacheKey = "dealer_verification_{$userId}";
        Cache::put($cacheKey, [
            'code' => $code,
            'phone' => $validated['phone'],
            'verified' => false
        ], now()->addMinutes(10));
        
        // TODO: INTEGRATE SMS API HERE
        // Example for Bangladesh SMS services:
        // 
        // Option 1: SSL Wireless (Bangladesh)
        // $url = "https://smsplus.sslwireless.com/api/v3/send-sms";
        // $params = [
        //     'api_token' => env('SMS_API_TOKEN'),
        //     'sid' => env('SMS_SID'),
        //     'msisdn' => $validated['phone'],
        //     'sms' => "Your verification code is: {$code}",
        //     'csms_id' => uniqid()
        // ];
        //
        // Option 2: BulkSMSBD
        // $url = "http://api.greenweb.com.bd/api.php";
        // $params = [
        //     'token' => env('SMS_API_TOKEN'),
        //     'to' => $validated['phone'],
        //     'message' => "Your verification code is: {$code}"
        // ];
        //
        // Option 3: Twilio (International)
        // $twilio = new \Twilio\Rest\Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
        // $twilio->messages->create($validated['phone'], [
        //     'from' => env('TWILIO_FROM'),
        //     'body' => "Your verification code is: {$code}"
        // ]);
        
        // For development: return code in response (REMOVE IN PRODUCTION)
        return response()->json([
            'success' => true,
            'message' => 'যাচাইকরণ কোড পাঠানো হয়েছে।',
            'verification_code' => $code, // REMOVE THIS LINE IN PRODUCTION
        ]);
    }
    
    /**
     * Verify mobile code
     */
    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ]);
        
        $userId = $request->user()->id;
        $cacheKey = "dealer_verification_{$userId}";
        
        // Get verification data from cache
        $verificationData = Cache::get($cacheKey);
        
        if (!$verificationData) {
            return response()->json([
                'success' => false,
                'message' => 'যাচাইকরণ কোডের মেয়াদ শেষ হয়ে গেছে। নতুন কোড পাঠান।'
            ], 400);
        }
        
        if ($verificationData['code'] !== $validated['code']) {
            return response()->json([
                'success' => false,
                'message' => 'ভুল যাচাইকরণ কোড। আবার চেষ্টা করুন।'
            ], 400);
        }
        
        // Mark as verified in cache
        $verificationData['verified'] = true;
        $verificationData['verified_at'] = now()->toDateTimeString();
        Cache::put($cacheKey, $verificationData, now()->addHour());
        
        return response()->json([
            'success' => true,
            'message' => 'মোবাইল নম্বর সফলভাবে যাচাই হয়েছে।',
            'data' => [
                'phone' => $verificationData['phone'],
                'verified' => true
            ]
        ]);
    }
    
    /**
     * Get all dealers (Admin only)
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $dealers = Dealer::with('user')->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $dealers
        ]);
    }
    
    /**
     * Approve dealer application (Admin only)
     */
    public function approve(Request $request, $id)
    {
        // Check if user is admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $dealer = Dealer::findOrFail($id);
        
        $dealer->update([
            'status' => 'active',
            'is_verified' => true,
            'approved_at' => now(),
            'approved_by' => $request->user()->id
        ]);
        
        // TODO: Send notification/email to dealer
        
        return response()->json([
            'success' => true,
            'message' => 'Dealer approved successfully',
            'data' => $dealer
        ]);
    }
    
    /**
     * Reject dealer application (Admin only)
     */
    public function reject(Request $request, $id)
    {
        // Check if user is admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);
        
        $dealer = Dealer::findOrFail($id);
        
        $dealer->update([
            'status' => 'rejected',
            'is_verified' => false,
            'rejection_reason' => $validated['reason'] ?? null,
            'rejected_at' => now(),
            'rejected_by' => $request->user()->id
        ]);
        
        // TODO: Send notification/email to dealer
        
        return response()->json([
            'success' => true,
            'message' => 'Dealer application rejected',
            'data' => $dealer
        ]);
    }
}
