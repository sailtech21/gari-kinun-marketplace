<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    /**
     * Get user's favorite listings
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get favorite listing IDs from user's favorites JSON column
        $favoriteIds = $user->favorites ?? [];
        
        if (empty($favoriteIds)) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        // Get full listing data with relationships
        $listings = Listing::with(['category', 'user', 'dealer'])
            ->whereIn('id', $favoriteIds)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        // Format response to match frontend expectations
        $favorites = $listings->map(function($listing) {
            return [
                'id' => $listing->id,
                'listing_id' => $listing->id,
                'listing' => $listing,
                'created_at' => $listing->created_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $favorites
        ]);
    }

    /**
     * Add listing to favorites
     */
    public function store(Request $request, $listingId)
    {
        $user = $request->user();
        
        // Verify listing exists
        $listing = Listing::find($listingId);
        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        }
        
        $favorites = $user->favorites ?? [];
        
        if (!in_array($listingId, $favorites)) {
            $favorites[] = (int)$listingId;
            $user->favorites = $favorites;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Added to favorites'
        ]);
    }

    /**
     * Remove listing from favorites
     */
    public function destroy(Request $request, $listingId)
    {
        $user = $request->user();
        $favorites = $user->favorites ?? [];
        
        $favorites = array_values(array_filter($favorites, function($id) use ($listingId) {
            return $id != $listingId;
        }));
        
        $user->favorites = $favorites;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Removed from favorites'
        ]);
    }

    /**
     * Check if listing is in favorites
     */
    public function check(Request $request, $listingId)
    {
        $user = $request->user();
        $favorites = $user->favorites ?? [];
        
        $isFavorite = in_array($listingId, $favorites);

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite
        ]);
    }
}
