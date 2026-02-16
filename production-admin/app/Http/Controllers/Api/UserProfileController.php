<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    /**
     * Get user's favorite listings
     */
    public function index()
    {
        $user = Auth::user();

        $favorites = DB::table('favorites')
            ->where('user_id', $user->id)
            ->join('listings', 'favorites.listing_id', '=', 'listings.id')
            ->select('favorites.id', 'favorites.listing_id', 'favorites.created_at', 'listings.*')
            ->orderBy('favorites.created_at', 'desc')
            ->get();

        // Transform to include image URLs
        $favorites = $favorites->map(function ($favorite) {
            $images = is_string($favorite->images) ? json_decode($favorite->images, true) : (array)$favorite->images;
            
            return [
                'id' => $favorite->id,
                'listing_id' => $favorite->listing_id,
                'created_at' => $favorite->created_at,
                'listing' => [
                    'id' => $favorite->listing_id,
                    'title' => $favorite->title,
                    'description' => $favorite->description,
                    'price' => $favorite->price,
                    'location' => $favorite->location,
                    'year' => $favorite->year,
                    'fuel_type' => $favorite->fuel_type,
                    'image' => !empty($images) ? url('storage/' . $images[0]) : null,
                    'images' => !empty($images) ? array_map(fn($img) => url('storage/' . $img), $images) : [],
                ]
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
    public function store($listingId)
    {
        $user = Auth::user();

        // Check if listing exists
        $listing = Listing::find($listingId);
        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'বিজ্ঞাপন পাওয়া যায়নি'
            ], 404);
        }

        // Check if already favorited
        $exists = DB::table('favorites')
            ->where('user_id', $user->id)
            ->where('listing_id', $listingId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'ইতিমধ্যে প্রিয় তালিকায় যোগ করা আছে'
            ], 400);
        }

        // Add to favorites
        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'listing_id' => $listingId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'প্রিয় তালিকায় যোগ করা হয়েছে'
        ]);
    }

    /**
     * Remove listing from favorites
     */
    public function destroy($listingId)
    {
        $user = Auth::user();

        $deleted = DB::table('favorites')
            ->where('user_id', $user->id)
            ->where('listing_id', $listingId)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'প্রিয় তালিকায় পাওয়া যায়নি'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'প্রিয় তালিকা থেকে সরানো হয়েছে'
        ]);
    }

    /**
     * Check if listing is favorited
     */
    public function check($listingId)
    {
        $user = Auth::user();

        $isFavorited = DB::table('favorites')
            ->where('user_id', $user->id)
            ->where('listing_id', $listingId)
            ->exists();

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited
        ]);
    }
}
