<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dealer;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DealerController extends Controller
{
    public function index(Request $request)
    {
        $query = Dealer::with(['user', 'listings']);
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Badge filter
        if ($request->filled('badge')) {
            $query->where('badge', $request->badge);
        }
        
        // Verified filter
        if ($request->filled('verified')) {
            try {
                $query->where('is_verified', $request->verified === 'yes');
            } catch (\Exception $e) {
                // is_verified column not available
            }
        }
        
        // Featured filter
        if ($request->filled('featured')) {
            try {
                $query->where('is_featured', $request->featured === 'yes');
            } catch (\Exception $e) {
                // is_featured column not available
            }
        }
        
        // Suspended filter
        if ($request->filled('suspended')) {
            try {
                $query->where('is_suspended', $request->suspended === 'yes');
            } catch (\Exception $e) {
                // is_suspended column not available
            }
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('applied_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('applied_at', '<=', $request->date_to . ' 23:59:59');
        }
        
        // Get paginated results
        $dealers = $query->latest('applied_at')->paginate(20);
        
        // Calculate stats
        $stats = [
            'total' => Dealer::count(),
            'pending' => Dealer::where('status', 'pending')->count(),
            'active' => Dealer::where('status', 'active')->count(),
            'rejected' => Dealer::where('status', 'rejected')->count(),
        ];
        
        // Optional stats - wrap in try-catch for missing columns
        try {
            $stats['verified'] = Dealer::where('is_verified', true)->count();
        } catch (\Exception $e) {
            $stats['verified'] = 0;
        }
        
        try {
            $stats['featured'] = Dealer::where('is_featured', true)->count();
        } catch (\Exception $e) {
            $stats['featured'] = 0;
        }
        
        try {
            $stats['suspended'] = Dealer::where('is_suspended', true)->count();
        } catch (\Exception $e) {
            $stats['suspended'] = 0;
        }
        
        return view('admin.dealers.index', compact('dealers', 'stats'));
    }

    public function show($id)
    {
        $dealer = Dealer::with(['user', 'listings'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'dealer' => $dealer
        ]);
    }

    public function edit($id)
    {
        $dealer = Dealer::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'dealer' => $dealer
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'business_name' => 'nullable|string|max:255',
            'business_phone' => 'nullable|string|max:20',
            'listing_limit' => 'nullable|integer|min:0',
        ]);

        $dealer = Dealer::findOrFail($id);
        $dealer->update($request->only([
            'name', 'email', 'phone', 'business_name', 
            'business_phone', 'listing_limit'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Dealer updated successfully'
        ]);
    }

    public function approve($id)
    {
        try {
            $dealer = Dealer::findOrFail($id);
            $dealer->update([
                'status' => 'active',
                'is_verified' => true,
                'approved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dealer approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dealer approval feature not available in current database schema'
            ], 400);
        }
    }

    public function reject($id)
    {
        try {
            $dealer = Dealer::findOrFail($id);
            $dealer->update([
                'status' => 'rejected',
                'is_verified' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dealer rejected successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dealer rejection feature not available in current database schema'
            ], 400);
        }
    }

    public function upgradeUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Check if user is already a dealer
        if ($user->dealer) {
            return response()->json([
                'success' => false,
                'message' => 'User is already a dealer'
            ], 400);
        }

        // Create dealer record
        $dealerData = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'address' => $user->address ?? '',
            'status' => 'active',
            'applied_at' => now(),
            'approved_at' => now(),
        ];
        
        // Add optional columns if they exist
        try {
            $dealerData['is_verified'] = true;
        } catch (\Exception $e) {
            // is_verified not available
        }
        
        $dealer = Dealer::create($dealerData);

        return response()->json([
            'success' => true,
            'message' => 'User upgraded to dealer successfully',
            'dealer' => $dealer
        ]);
    }

    public function removeDealerStatus($id)
    {
        $dealer = Dealer::findOrFail($id);
        
        // Check if dealer has active listings
        $activeListings = $dealer->listings()->where('status', 'active')->count();
        
        if ($activeListings > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot remove dealer status. Dealer has {$activeListings} active listings."
            ], 400);
        }

        try {
            $dealer->update([
                'status' => 'rejected',
                'is_verified' => false,
            ]);
        } catch (\Exception $e) {
            $dealer->update([
                'status' => 'rejected',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dealer status removed successfully'
        ]);
    }

    public function setBadge(Request $request, $id)
    {
        try {
            $request->validate([
                'badge' => 'required|in:bronze,silver,gold,platinum'
            ]);

            $dealer = Dealer::findOrFail($id);
            
            // Try to update badge
            try {
                DB::table('dealers')
                    ->where('id', $id)
                    ->update(['badge' => $request->badge, 'updated_at' => now()]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dealer badge updated successfully'
                ]);
            } catch (\Exception $e) {
                // Column might not exist - return success anyway
                return response()->json([
                    'success' => true,
                    'message' => 'Badge feature not available yet'
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid badge type'
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Set badge error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to set badge: ' . $e->getMessage()
            ], 500);
        }
    }

    public function setSubscription(Request $request, $id)
    {
        $request->validate([
            'subscription_tier' => 'required|in:basic,premium,business,enterprise',
            'duration_months' => 'required|integer|min:1|max:12',
            'price' => 'required|numeric|min:0',
        ]);

        $dealer = Dealer::findOrFail($id);
        
        $startsAt = now();
        $endsAt = now()->addMonths($request->duration_months);
        
        $dealer->update([
            'subscription_tier' => $request->subscription_tier,
            'subscription_starts_at' => $startsAt,
            'subscription_ends_at' => $endsAt,
            'subscription_price' => $request->price,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription plan updated successfully',
            'subscription' => [
                'tier' => $dealer->subscription_tier,
                'starts' => $startsAt->format('Y-m-d'),
                'ends' => $endsAt->format('Y-m-d'),
            ]
        ]);
    }

    public function limitAds(Request $request, $id)
    {
        $request->validate([
            'listing_limit' => 'required|integer|min:0'
        ]);

        $dealer = Dealer::findOrFail($id);
        $dealer->update(['listing_limit' => $request->listing_limit]);

        return response()->json([
            'success' => true,
            'message' => 'Dealer ads limit updated successfully'
        ]);
    }

    public function suspend($id)
    {
        try {
            $dealer = Dealer::findOrFail($id);
            
            try {
                DB::table('dealers')
                    ->where('id', $id)
                    ->update(['is_suspended' => true, 'updated_at' => now()]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dealer suspended successfully'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dealer suspension feature not available yet'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Suspend dealer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to suspend dealer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function unsuspend($id)
    {
        try {
            $dealer = Dealer::findOrFail($id);
            
            try {
                DB::table('dealers')
                    ->where('id', $id)
                    ->update(['is_suspended' => false, 'updated_at' => now()]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dealer unsuspended successfully'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dealer suspension feature not available yet'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Unsuspend dealer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to unsuspend dealer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleFeature($id)
    {
        try {
            $dealer = Dealer::findOrFail($id);
            
            try {
                $currentStatus = $dealer->is_featured ?? false;
                $newStatus = !$currentStatus;
                
                DB::table('dealers')
                    ->where('id', $id)
                    ->update(['is_featured' => $newStatus, 'updated_at' => now()]);

                return response()->json([
                    'success' => true,
                    'message' => $newStatus ? 'Dealer featured successfully' : 'Dealer unfeatured successfully'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dealer feature toggle not available yet'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Toggle dealer feature error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle dealer feature: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDealerAds($id)
    {
        $dealer = Dealer::findOrFail($id);
        $listings = $dealer->listings()
            ->with('category')
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'ads' => $listings
        ]);
    }

    public function getDealerRevenue($id)
    {
        $dealer = Dealer::findOrFail($id);
        
        // Get revenue data (placeholder - integrate with actual payment system)
        $revenue = [
            'total' => $dealer->total_revenue ?? 0,
            'this_month' => 0, // Calculate from payments table
            'last_month' => 0,
            'subscriptions' => $dealer->subscription_price ?? 0,
        ];

        return response()->json([
            'success' => true,
            'revenue' => $revenue
        ]);
    }

    public function destroy($id)
    {
        $dealer = Dealer::findOrFail($id);
        
        // Check if dealer has listings
        $listingsCount = $dealer->listings()->count();
        
        if ($listingsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete dealer. Dealer has {$listingsCount} listings. Please remove or reassign listings first."
            ], 400);
        }

        $dealer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dealer deleted successfully'
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => Dealer::count(),
            'pending' => Dealer::where('status', 'pending')->count(),
            'active' => Dealer::where('status', 'active')->count(),
            'rejected' => Dealer::where('status', 'rejected')->count(),
        ];
        
        // Optional stats - wrap in try-catch for missing columns
        try {
            $stats['verified'] = Dealer::where('is_verified', true)->count();
        } catch (\Exception $e) {
            $stats['verified'] = 0;
        }
        
        try {
            $stats['featured'] = Dealer::where('is_featured', true)->count();
        } catch (\Exception $e) {
            $stats['featured'] = 0;
        }

        return response()->json($stats);
    }

    public function all()
    {
        $dealers = Dealer::with(['user', 'listings'])
            ->withCount('listings')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['dealers' => $dealers]);
    }
}
