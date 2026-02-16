<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FirebaseAuthController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\NotificationSettingsController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserListingController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DealerController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API Routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

// Phone OTP Authentication
Route::post('/auth/send-otp', [AuthController::class, 'sendOTP']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOTP']);

// Google OAuth Authentication
Route::get('/auth/google', [AuthController::class, 'googleRedirect']);
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);
Route::post('/auth/google/callback', [AuthController::class, 'googleCallback']); // For token-based flow

// Firebase Authentication (Google + Phone OTP)
Route::post('/auth/firebase-login', [FirebaseAuthController::class, 'firebaseLogin']);

// Email Verification
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

// Listings routes
Route::get('/listings', [ListingController::class, 'index']);
Route::get('/listings/featured', [ListingController::class, 'featured']);
Route::get('/listings/trending', [ListingController::class, 'trending']);

// Search with advanced filters (MUST come before {id} route)
Route::get('/listings/search', function(Request $request) {
    $query = \App\Models\Listing::where('status', 'approved');
    
    // Support both 'keyword' and 'q' parameters
    $searchTerm = $request->filled('keyword') ? $request->keyword : $request->q;
    if ($searchTerm) {
        $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%')
              ->orWhere('brand', 'like', '%' . $searchTerm . '%')
              ->orWhere('model', 'like', '%' . $searchTerm . '%');
        });
    }
    
    if ($request->filled('category')) {
        $query->whereHas('category', function($q) use ($request) {
            $q->where('name', $request->category);
        });
    }
    
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }
    
    if ($request->filled('type')) {
        $query->whereHas('category', function($q) use ($request) {
            $q->where('type', $request->type);
        });
    }
    
    // Support both 'make' and 'brand' parameters for compatibility
    $brandParam = $request->filled('brand') ? $request->brand : $request->make;
    if ($brandParam) {
        $query->where('brand', $brandParam);
    }
    
    if ($request->filled('model')) {
        $query->where('model', $request->model);
    }
    
    if ($request->filled('year_min')) {
        $query->where('year_of_manufacture', '>=', $request->year_min);
    }
    
    if ($request->filled('year_max')) {
        $query->where('year_of_manufacture', '<=', $request->year_max);
    }
    
    if ($request->filled('price_min')) {
        $query->where('price', '>=', $request->price_min);
    }
    
    if ($request->filled('price_max')) {
        $query->where('price', '<=', $request->price_max);
    }
    
    if ($request->filled('condition')) {
        $query->where('condition', $request->condition);
    }
    
    if ($request->filled('fuel_type')) {
        $query->where('fuel_type', $request->fuel_type);
    }
    
    if ($request->filled('transmission')) {
        $query->where('transmission', $request->transmission);
    }
    
    $sort = $request->get('sort', 'newest');
    switch ($sort) {
        case 'price_low':
            $query->orderBy('price', 'asc');
            break;
        case 'price_high':
            $query->orderBy('price', 'desc');
            break;
        case 'year_new':
            $query->orderBy('year_of_manufacture', 'desc');
            break;
        case 'year_old':
            $query->orderBy('year_of_manufacture', 'asc');
            break;
        default:
            $query->orderBy('created_at', 'desc');
    }
    
    $listings = $query->with(['category', 'user'])->paginate(20);
    return response()->json(['success' => true, 'data' => $listings]);
});

Route::get('/listings/{id}', [ListingController::class, 'show']);

// Categories & Locations
Route::get('/categories', [ListingController::class, 'categories']);
Route::get('/categories/with-counts', function() {
    $categories = \App\Models\Category::withCount(['listings' => function($query) {
        $query->where('status', 'approved');
    }])->get();
    return response()->json(['success' => true, 'data' => $categories]);
});
Route::get('/locations', [\App\Http\Controllers\Api\ContactController::class, 'getLocations']);

// Stats & Contact
Route::get('/stats', [\App\Http\Controllers\Api\ContactController::class, 'getStats']);
Route::post('/contact', [\App\Http\Controllers\Api\ContactController::class, 'store']);

// Reviews
Route::get('/reviews', [ReviewController::class, 'index']);
Route::post('/reviews', [ReviewController::class, 'store']);

// Banners & Advertisements
Route::get('/banners', function(Request $request) {
    $banners = \App\Models\Banner::where('is_active', true)
        ->orderBy('order')
        ->get();
    return response()->json(['success' => true, 'data' => $banners]);
});

Route::get('/advertisements', function(Request $request) {
    $query = \App\Models\Advertisement::active()->orderBy('order');
    
    if ($request->has('position')) {
        $query->where('position', $request->position);
    }
    
    if ($request->has('type')) {
        $query->where('type', $request->type);
    }
    
    $ads = $query->get();
    return response()->json(['success' => true, 'data' => $ads]);
});

// Ads alias for advertisements
Route::get('/ads', function(Request $request) {
    $query = \App\Models\Advertisement::active()->orderBy('order');
    
    if ($request->has('position')) {
        $query->where('position', $request->position);
    }
    
    if ($request->has('type')) {
        $query->where('type', $request->type);
    }
    
    $ads = $query->get();
    return response()->json(['success' => true, 'data' => $ads]);
});

// Settings
Route::get('/settings', function() {
    $settings = \App\Models\Setting::pluck('value', 'key');
    return response()->json(['success' => true, 'data' => $settings]);
});

// Hero Section (public CMS endpoint)
Route::get('/cms/hero-section', function() {
    $hero = \App\Models\SiteSetting::get('homepage_hero', null);
    
    if (!$hero) {
        $hero = [
            'main_heading' => 'Find Your Perfect Vehicle',
            'sub_heading' => 'Browse thousands of verified listings',
            'cta_text' => 'Start Searching',
            'cta_link' => '/listings',
            'background' => '',
            'enabled' => true
        ];
    } else {
        $hero = json_decode($hero, true);
    }
    
    return response()->json([
        'success' => true,
        'hero' => $hero
    ]);
});

// Dealers (Verified only)
Route::get('/dealers', function(Request $request) {
    $dealers = \App\Models\Dealer::where('status', 'active')
        ->with(['user' => function($query) {
            $query->select('id', 'name', 'email', 'phone');
        }])
        ->orderBy('created_at', 'desc')
        ->get();
    return response()->json(['success' => true, 'data' => $dealers]);
});

// Verified dealers endpoint
Route::get('/dealers/verified', function(Request $request) {
    $dealers = \App\Models\Dealer::where('status', 'active')
        ->where('is_verified', true)
        ->with(['user' => function($query) {
            $query->select('id', 'name', 'email', 'phone');
        }])
        ->orderBy('created_at', 'desc')
        ->get();
    return response()->json(['success' => true, 'data' => $dealers]);
});

// Dealer Profile & Listings
Route::get('/dealers/{id}', function($id) {
    $dealer = \App\Models\Dealer::where('status', 'active')
        ->with(['user' => function($query) {
            $query->select('id', 'name', 'email', 'phone');
        }])
        ->findOrFail($id);
    
    $listings = \App\Models\Listing::where('user_id', $dealer->user_id)
        ->where('status', 'approved')
        ->with(['category'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    return response()->json([
        'success' => true, 
        'data' => [
            'dealer' => $dealer,
            'listings' => $listings
        ]
    ]);
});

// Get available makes and models for filters
Route::get('/filters/makes', function(Request $request) {
    $makes = \App\Models\Listing::distinct()
        ->whereNotNull('brand')
        ->where('status', 'approved')
        ->pluck('brand')
        ->sort()
        ->values();
    return response()->json(['success' => true, 'data' => $makes]);
});

Route::get('/filters/models', function(Request $request) {
    $query = \App\Models\Listing::distinct()->whereNotNull('model')->where('status', 'approved');
    
    // Support both 'make' and 'brand' parameters for compatibility
    $brandParam = $request->filled('brand') ? $request->brand : $request->make;
    if ($brandParam) {
        $query->where('brand', $brandParam);
    }
    
    $models = $query->pluck('model')->sort()->values();
    return response()->json(['success' => true, 'data' => $models]);
});

// Combined filters endpoint
Route::get('/filters', function(Request $request) {
    $brands = \App\Models\Listing::distinct()
        ->whereNotNull('brand')
        ->where('status', 'approved')
        ->pluck('brand')
        ->sort()
        ->values();
    
    $years = \App\Models\Listing::distinct()
        ->whereNotNull('year_of_manufacture')
        ->where('status', 'approved')
        ->pluck('year_of_manufacture')
        ->sort()
        ->values();
    
    $fuelTypes = \App\Models\Listing::distinct()
        ->whereNotNull('fuel_type')
        ->where('status', 'approved')
        ->pluck('fuel_type')
        ->values();
    
    $transmissions = \App\Models\Listing::distinct()
        ->whereNotNull('transmission')
        ->where('status', 'approved')
        ->pluck('transmission')
        ->values();
    
    $conditions = ['New', 'Used', 'Reconditioned'];
    
    return response()->json([
        'success' => true,
        'data' => [
            'brands' => $brands,
            'years' => $years,
            'fuel_types' => $fuelTypes,
            'transmissions' => $transmissions,
            'conditions' => $conditions
        ]
    ]);
});

// Brands endpoint (alias to filters/makes)
Route::get('/brands', function(Request $request) {
    $brands = \App\Models\Listing::distinct()
        ->whereNotNull('brand')
        ->where('status', 'approved')
        ->pluck('brand')
        ->sort()
        ->values();
    return response()->json(['success' => true, 'data' => $brands]);
});

// Models endpoint with brand filter
Route::get('/models', function(Request $request) {
    $query = \App\Models\Listing::distinct()->whereNotNull('model')->where('status', 'approved');
    
    // Support both 'brand_id' by name lookup and direct 'brand' parameter
    if ($request->filled('brand_id')) {
        // Assuming brand_id is actually the brand name for filtering
        $query->where('brand', $request->brand_id);
    }
    
    if ($request->filled('brand')) {
        $query->where('brand', $request->brand);
    }
    
    $models = $query->pluck('model')->sort()->values();
    return response()->json(['success' => true, 'data' => $models]);
});

// Similar/Related listings
Route::get('/listings/{id}/similar', [ListingController::class, 'similar']);

// Pricing Information (Public)
Route::get('/pricing', [PaymentController::class, 'getPricing']);

// Protected API Routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Email Verification (protected)
    Route::post('/email/resend', [AuthController::class, 'sendVerificationEmail']);
    Route::get('/email/status', [AuthController::class, 'checkEmailVerification']);
    
    // User Profile routes
    Route::get('/users/profile', [ProfileController::class, 'show']);
    Route::put('/users/profile', [ProfileController::class, 'update']);
    Route::put('/users/password', [ProfileController::class, 'updatePassword']);
    Route::post('/users/avatar', [ProfileController::class, 'uploadAvatar']);
    
    // User's Listings Management
    Route::get('/users/listings', [UserListingController::class, 'index']);
    Route::put('/listings/{id}', [UserListingController::class, 'update']);
    Route::delete('/listings/{id}', [UserListingController::class, 'destroy']);
    
    // Create Listing (public create endpoint)
    Route::post('/listings', [ListingController::class, 'store']);
    
    // Favorites routes
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/listings/{listingId}/favorite', [FavoriteController::class, 'store']);
    Route::delete('/listings/{listingId}/unfavorite', [FavoriteController::class, 'destroy']);
    Route::get('/listings/{listingId}/favorite/check', [FavoriteController::class, 'check']);
    
    // Upload routes
    Route::post('/upload/image', [UploadController::class, 'uploadImage']);
    Route::post('/upload/images', [UploadController::class, 'uploadImages']);
    Route::delete('/upload/image', [UploadController::class, 'deleteImage']);
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    
    // Notification Settings routes
    Route::get('/notification-settings', [NotificationSettingsController::class, 'index']);
    Route::post('/notification-settings', [NotificationSettingsController::class, 'update']);
    
    // Dealer Application Routes
    Route::get('/dealer/status', [DealerController::class, 'status']);
    Route::post('/dealer/apply', [DealerController::class, 'apply']);
    Route::post('/dealer/send-verification', [DealerController::class, 'sendVerificationCode']);
    Route::post('/dealer/verify-code', [DealerController::class, 'verifyCode']);
    
    // Admin: Dealer Management Routes
    Route::get('/admin/dealers', [DealerController::class, 'index']);
    Route::post('/admin/dealers/{id}/approve', [DealerController::class, 'approve']);
    Route::post('/admin/dealers/{id}/reject', [DealerController::class, 'reject']);
    
    // Payment & Monetization Routes (Protected)
    Route::post('/payment/featured-listing', [PaymentController::class, 'purchaseFeaturedListing']);
    Route::post('/payment/subscription', [PaymentController::class, 'purchaseSubscription']);
    Route::get('/payment/status/{transactionId}', [PaymentController::class, 'getPaymentStatus']);
    Route::post('/payment/banner-ad', [PaymentController::class, 'requestBannerAd']);
    
    // Report Listing
    Route::post('/listings/{id}/report', function(Request $request, $id) {
        $validated = $request->validate([
            'reason' => 'required|string|in:spam,inappropriate,fraud,duplicate,other',
            'description' => 'required|string|max:500'
        ]);
        
        $listing = \App\Models\Listing::findOrFail($id);
        
        $report = \App\Models\Report::create([
            'listing_id' => $listing->id,
            'user_id' => $request->user()->id,
            'reason' => $validated['reason'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Report submitted successfully. We will review it soon.',
            'data' => $report
        ]);
    });
    
    // Increment listing views
    Route::post('/listings/{id}/view', function($id) {
        $listing = \App\Models\Listing::findOrFail($id);
        $listing->increment('views');
        return response()->json(['success' => true, 'views' => $listing->views]);
    });
});
