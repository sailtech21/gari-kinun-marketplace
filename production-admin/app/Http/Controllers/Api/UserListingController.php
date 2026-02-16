<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserListingController extends Controller
{
    /**
     * Get authenticated user's listings with stats
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = Listing::where('user_id', $user->id);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $listings = $query->orderBy('created_at', 'desc')->get();

        // Calculate stats
        $stats = [
            'total' => Listing::where('user_id', $user->id)->count(),
            'active' => Listing::where('user_id', $user->id)->where('status', 'active')->count(),
            'pending' => Listing::where('user_id', $user->id)->where('status', 'pending')->count(),
            'sold' => Listing::where('user_id', $user->id)->where('status', 'sold')->count(),
        ];

        // Transform listings to include image URL
        $listings = $listings->map(function ($listing) {
            $images = is_string($listing->images) ? json_decode($listing->images, true) : $listing->images;
            
            return [
                'id' => $listing->id,
                'title' => $listing->title,
                'description' => $listing->description,
                'price' => $listing->price,
                'location' => $listing->location,
                'status' => $listing->status,
                'views' => $listing->views ?? 0,
                'created_at' => $listing->created_at,
                'image' => !empty($images) ? url('storage/' . $images[0]) : null,
                'images' => !empty($images) ? array_map(fn($img) => url('storage/' . $img), $images) : [],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'listings' => $listings,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Update listing
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $listing = Listing::where('id', $id)->where('user_id', $user->id)->first();

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'বিজ্ঞাপন পাওয়া যায়নি'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string',
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $listing->update($request->only([
            'title', 'description', 'price', 'category_id', 'location', 
            'phone', 'brand', 'model', 'year', 'mileage', 'fuel_type', 
            'transmission', 'condition'
        ]));

        // Handle new images if provided
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('listings', 'public');
                $images[] = $path;
            }
            $listing->images = json_encode($images);
            $listing->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'বিজ্ঞাপন আপডেট হয়েছে',
            'data' => $listing
        ]);
    }

    /**
     * Delete listing
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $listing = Listing::where('id', $id)->where('user_id', $user->id)->first();

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'বিজ্ঞাপন পাওয়া যায়নি'
            ], 404);
        }

        // Delete images from storage
        $images = is_string($listing->images) ? json_decode($listing->images, true) : $listing->images;
        if (!empty($images)) {
            foreach ($images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $listing->delete();

        return response()->json([
            'success' => true,
            'message' => 'বিজ্ঞাপন মুছে ফেলা হয়েছে'
        ]);
    }
}
