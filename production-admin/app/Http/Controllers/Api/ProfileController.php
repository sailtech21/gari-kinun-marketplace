<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function show()
    {
        $user = Auth::user();

        // Get listings count
        $listingsCount = $user->listings()->count();

        // Calculate rating (default to 0 for now, can be expanded with reviews system)
        $rating = 0; // You can implement a real rating calculation here

        // Get dealer information if user is a dealer
        $dealer = $user->dealer()->first();
        $isVerifiedDealer = $dealer && $dealer->status === 'active' && $dealer->is_verified;

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'address' => $user->address ?? '',
                'avatar' => $user->avatar ?? null,
                'phone_verified' => $user->phone_verified_at !== null,
                'listings_count' => $listingsCount,
                'rating' => $rating,
                'created_at' => $user->created_at->toISOString(),
                'is_verified_dealer' => $isVerifiedDealer,
                'dealer' => $dealer ? [
                    'id' => $dealer->id,
                    'business_name' => $dealer->business_name,
                    'business_phone' => $dealer->business_phone,
                    'business_address' => $dealer->business_address,
                    'status' => $dealer->status,
                    'is_verified' => $dealer->is_verified,
                    'applied_at' => $dealer->applied_at,
                    'approved_at' => $dealer->approved_at,
                ] : null,
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only(['name', 'email', 'phone', 'address']));

        // Get updated data
        $listingsCount = $user->listings()->count();
        $rating = 0;
        
        // Get dealer information if user is a dealer
        $dealer = $user->dealer()->first();
        $isVerifiedDealer = $dealer && $dealer->status === 'active' && $dealer->is_verified;

        return response()->json([
            'success' => true,
            'message' => 'প্রোফাইল আপডেট হয়েছে',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'address' => $user->address ?? '',
                'avatar' => $user->avatar ?? null,
                'phone_verified' => $user->phone_verified_at !== null,
                'listings_count' => $listingsCount,
                'rating' => $rating,
                'created_at' => $user->created_at->toISOString(),
                'is_verified_dealer' => $isVerifiedDealer,
                'dealer' => $dealer ? [
                    'id' => $dealer->id,
                    'business_name' => $dealer->business_name,
                    'business_phone' => $dealer->business_phone,
                    'business_address' => $dealer->business_address,
                    'status' => $dealer->status,
                    'is_verified' => $dealer->is_verified,
                    'applied_at' => $dealer->applied_at,
                    'approved_at' => $dealer->approved_at,
                ] : null,
            ]
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => ['current_password' => 'বর্তমান পাসওয়ার্ড সঠিক নয়']
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'পাসওয়ার্ড পরিবর্তন হয়েছে'
        ]);
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'ছবি আপলোড হয়েছে',
            'data' => [
                'avatar' => $path,
                'avatar_url' => asset('storage/' . $path)
            ]
        ]);
    }
}
