<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FirebaseAuthController extends Controller
{
    /**
     * Handle Firebase authentication and sync with Laravel backend
     * 
     * This endpoint receives Firebase authentication results from the frontend
     * and creates or updates users in the Laravel database.
     */
    public function firebaseLogin(Request $request)
    {
        try {
            $request->validate([
                'firebase_uid' => 'required|string',
                'provider' => 'required|in:google,phone',
                'firebase_token' => 'required|string',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'name' => 'nullable|string',
                'avatar' => 'nullable|string',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ], 422);
        }

        // Optional: Verify Firebase ID token for extra security
        // Uncomment after installing firebase-php package
        // try {
        //     $auth = app('firebase.auth');
        //     $verifiedIdToken = $auth->verifyIdToken($request->firebase_token);
        //     $uid = $verifiedIdToken->claims()->get('sub');
        //     
        //     if ($uid !== $request->firebase_uid) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Invalid Firebase token'
        //         ], 401);
        //     }
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Firebase token verification failed'
        //     ], 401);
        // }

        // Find existing user by firebase_uid
        $user = User::where('firebase_uid', $request->firebase_uid)->first();

        $isNewUser = false;

        if (!$user) {
            // Check if user exists with same email or phone (for migration)
            if ($request->provider === 'google' && $request->email) {
                $user = User::where('email', $request->email)->first();
            } elseif ($request->provider === 'phone' && $request->phone) {
                $user = User::where('phone', $request->phone)->first();
            }

            if ($user) {
                // Link existing user to Firebase
                $user->update(['firebase_uid' => $request->firebase_uid]);
            } else {
                // Create new user
                $isNewUser = true;
                
                $userData = [
                    'firebase_uid' => $request->firebase_uid,
                    'password' => Hash::make(Str::random(32)), // Random password (Firebase handles auth)
                    'role' => 'user', // Default role for new users
                ];

                if ($request->provider === 'google') {
                    $userData['email'] = $request->email;
                    $userData['name'] = $request->name ?? 'User';
                    $userData['avatar'] = $request->avatar;
                    $userData['google_id'] = $request->firebase_uid;
                    $userData['email_verified_at'] = now();
                } elseif ($request->provider === 'phone') {
                    $userData['phone'] = $request->phone;
                    $userData['name'] = $request->name ?? 'User';
                    $userData['phone_verified_at'] = now();
                }

                try {
                    $user = User::create($userData);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User creation failed: ' . $e->getMessage(),
                        'data' => $userData
                    ], 500);
                }
            }
        } else {
            // Update existing user information if provided
            $updateData = [];

            if ($request->has('name') && $request->name && $user->name === 'User') {
                $updateData['name'] = $request->name;
            }

            if ($request->has('avatar') && $request->avatar && !$user->avatar) {
                $updateData['avatar'] = $request->avatar;
            }

            if ($request->has('email') && $request->email && !$user->email) {
                $updateData['email'] = $request->email;
                $updateData['email_verified_at'] = now();
            }

            if ($request->has('phone') && $request->phone && !$user->phone) {
                $updateData['phone'] = $request->phone;
                $updateData['phone_verified_at'] = now();
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }
        }

        // Generate Sanctum token for API authentication
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => $isNewUser ? 'Account created successfully' : 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'role' => $user->role,
                ],
                'token' => $token,
                'is_new_user' => $isNewUser
            ]
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
