<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ListingController extends Controller
{
    /**
     * Get all listings with optional filters
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Listing::with(['category', 'user', 'dealer']);

        // Filter by category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // By default, only show active listings
            $query->where('status', 'active');
        }

        // Filter by featured
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->is_featured);
        }

        // Search by title or description
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by location
        if ($request->has('location') && $request->location != '') {
            $query->where('location', 'like', "%{$request->location}%");
        }

        // Filter by year range
        if ($request->has('year_min')) {
            $query->where('year_of_manufacture', '>=', $request->year_min);
        }
        if ($request->has('year_max')) {
            $query->where('year_of_manufacture', '<=', $request->year_max);
        }

        // Filter by fuel type
        if ($request->has('fuel_type') && $request->fuel_type != '') {
            $query->where('fuel_type', $request->fuel_type);
        }

        // Filter by condition
        if ($request->has('condition') && $request->condition != '') {
            $query->where('condition', $request->condition);
        }

        // Filter by brand
        if ($request->has('brand') && $request->brand != '') {
            $query->where('brand', 'like', "%{$request->brand}%");
        }

        // Filter by model
        if ($request->has('model') && $request->model != '') {
            $query->where('model', 'like', "%{$request->model}%");
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $listings = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $listings->items(),
            'pagination' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ]
        ]);
    }

    /**
     * Get a single listing by ID
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $listing = Listing::with(['category', 'user', 'dealer'])->find($id);

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        }

        // Increment views
        $listing->increment('views');

        return response()->json([
            'success' => true,
            'data' => $listing
        ]);
    }

    /**
     * Create a new listing
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:listings,slug|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'video_link' => 'nullable|url|max:500',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Vehicle details
            'condition' => 'nullable|in:Used,New,Reconditioned',
            'model' => 'nullable|string|max:100',
            'year_of_manufacture' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'engine_capacity' => 'nullable|integer|min:0',
            'transmission' => 'nullable|in:Manual,Automatic,Other',
            'registration_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'brand' => 'nullable|string|max:100',
            'trim_edition' => 'nullable|string|max:100',
            'kilometers_run' => 'nullable|integer|min:0',
            'fuel_type' => 'nullable|string|max:50',
            'body_type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('listings', 'public');
                $imagePaths[] = '/storage/' . $path;
            }
        }

        // Generate slug if not provided
        $slug = $request->slug;
        if (!$slug) {
            $slug = \Str::slug($request->title);
            // Ensure uniqueness
            $originalSlug = $slug;
            $counter = 1;
            while (Listing::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $listing = new Listing();
        $listing->title = $request->title;
        $listing->slug = $slug;
        $listing->description = $request->description;
        $listing->price = $request->price;
        $listing->category_id = $request->category_id;
        $listing->location = $request->location;
        $listing->phone = $request->phone;
        $listing->video_link = $request->video_link;
        $listing->images = $imagePaths;
        $listing->status = 'pending'; // Default to pending for review
        $listing->is_featured = false;
        $listing->views = 0;
        
        // Vehicle details
        $listing->condition = $request->condition;
        $listing->model = $request->model;
        $listing->year_of_manufacture = $request->year_of_manufacture;
        $listing->engine_capacity = $request->engine_capacity;
        $listing->transmission = $request->transmission;
        $listing->registration_year = $request->registration_year;
        $listing->brand = $request->brand;
        $listing->trim_edition = $request->trim_edition;
        $listing->kilometers_run = $request->kilometers_run;
        $listing->fuel_type = $request->fuel_type;
        $listing->body_type = $request->body_type;
        
        // If user is authenticated, set user_id
        if ($request->user()) {
            $listing->user_id = $request->user()->id;
        }

        $listing->save();

        return response()->json([
            'success' => true,
            'message' => 'Listing created successfully',
            'data' => $listing->load(['category', 'user'])
        ], 201);
    }

    /**
     * Update an existing listing
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $listing = Listing::find($id);

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        }

        // Check if user owns this listing
        if ($request->user() && $listing->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to edit this listing'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|unique:listings,slug,' . $id . '|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'location' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'video_link' => 'sometimes|nullable|url|max:500',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'sometimes|in:active,pending,sold,rejected',
            
            // Vehicle details
            'condition' => 'sometimes|nullable|in:Used,New,Reconditioned',
            'model' => 'sometimes|nullable|string|max:100',
            'year_of_manufacture' => 'sometimes|nullable|integer|min:1900|max:' . (date('Y') + 1),
            'engine_capacity' => 'sometimes|nullable|integer|min:0',
            'transmission' => 'sometimes|nullable|in:Manual,Automatic,Other',
            'registration_year' => 'sometimes|nullable|integer|min:1900|max:' . date('Y'),
            'brand' => 'sometimes|nullable|string|max:100',
            'trim_edition' => 'sometimes|nullable|string|max:100',
            'kilometers_run' => 'sometimes|nullable|integer|min:0',
            'fuel_type' => 'sometimes|nullable|string|max:50',
            'body_type' => 'sometimes|nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('listings', 'public');
                $imagePaths[] = '/storage/' . $path;
            }
            $listing->images = $imagePaths;
        }

        // Update only provided fields
        if ($request->has('title')) $listing->title = $request->title;
        if ($request->has('slug')) $listing->slug = $request->slug;
        if ($request->has('description')) $listing->description = $request->description;
        if ($request->has('price')) $listing->price = $request->price;
        if ($request->has('category_id')) $listing->category_id = $request->category_id;
        if ($request->has('location')) $listing->location = $request->location;
        if ($request->has('phone')) $listing->phone = $request->phone;
        if ($request->has('video_link')) $listing->video_link = $request->video_link;
        if ($request->has('status')) $listing->status = $request->status;
        
        // Update vehicle details
        if ($request->has('condition')) $listing->condition = $request->condition;
        if ($request->has('model')) $listing->model = $request->model;
        if ($request->has('year_of_manufacture')) $listing->year_of_manufacture = $request->year_of_manufacture;
        if ($request->has('engine_capacity')) $listing->engine_capacity = $request->engine_capacity;
        if ($request->has('transmission')) $listing->transmission = $request->transmission;
        if ($request->has('registration_year')) $listing->registration_year = $request->registration_year;
        if ($request->has('brand')) $listing->brand = $request->brand;
        if ($request->has('trim_edition')) $listing->trim_edition = $request->trim_edition;
        if ($request->has('kilometers_run')) $listing->kilometers_run = $request->kilometers_run;
        if ($request->has('fuel_type')) $listing->fuel_type = $request->fuel_type;
        if ($request->has('body_type')) $listing->body_type = $request->body_type;

        $listing->save();

        return response()->json([
            'success' => true,
            'message' => 'Listing updated successfully',
            'data' => $listing->load(['category', 'user'])
        ]);
    }

    /**
     * Delete a listing
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $listing = Listing::find($id);

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        }

        // Check if user owns this listing
        if ($request->user() && $listing->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this listing'
            ], 403);
        }

        $listing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Listing deleted successfully'
        ]);
    }

    /**
     * Get all categories
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories()
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('listings')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get featured listings
     */
    public function featured(Request $request)
    {
        $query = Listing::with(['category', 'user', 'dealer'])
            ->where('status', 'active')
            ->where('is_featured', true);

        $perPage = $request->get('per_page', 12);
        $listings = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $listings->items(),
            'pagination' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ]
        ]);
    }

    /**
     * Get trending listings (most viewed)
     */
    public function trending(Request $request)
    {
        $query = Listing::with(['category', 'user', 'dealer'])
            ->where('status', 'active');

        $perPage = $request->get('per_page', 6);
        $listings = $query->orderBy('views', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $listings->items(),
            'pagination' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ]
        ]);
    }

    /**
     * Get similar listings based on category, price range, and brand
     */
    public function similar($id, Request $request)
    {
        $listing = Listing::find($id);
        
        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        }

        // Get similar listings based on:
        // 1. Same category
        // 2. Price within ±30%
        // 3. Same brand if available
        $priceMin = $listing->price * 0.7;
        $priceMax = $listing->price * 1.3;

        $query = Listing::with(['category', 'user', 'dealer'])
            ->where('status', 'active')
            ->where('id', '!=', $id); // Exclude current listing

        // Priority 1: Same category
        $query->where('category_id', $listing->category_id);

        // Priority 2: Similar price range
        $query->whereBetween('price', [$priceMin, $priceMax]);

        // Priority 3: Same brand if available
        if ($listing->brand) {
            $query->orWhere(function($q) use ($listing, $id) {
                $q->where('brand', $listing->brand)
                  ->where('status', 'active')
                  ->where('id', '!=', $id);
            });
        }

        // Get up to 6 similar listings
        $perPage = $request->get('per_page', 6);
        $similarListings = $query->orderBy('created_at', 'desc')
            ->take($perPage)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $similarListings
        ]);
    }
}
