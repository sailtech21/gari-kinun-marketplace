<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     */
    public function index()
    {
        $reviews = Review::latest()->paginate(20);
        $stats = [
            'total' => Review::count(),
            'approved' => Review::where('is_approved', true)->count(),
            'pending' => Review::where('is_approved', false)->count(),
            'featured' => Review::where('is_featured', true)->count(),
        ];
        
        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    /**
     * Approve a review
     */
    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Review approved successfully',
            'review' => $review->fresh()
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_featured' => !$review->is_featured]);
        
        return response()->json([
            'success' => true,
            'message' => 'Featured status updated successfully',
            'review' => $review->fresh()
        ]);
    }

    /**
     * Remove the specified review.
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }

    /**
     * Get all reviews
     */
    public function all()
    {
        $reviews = Review::with([])  // No relations needed
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json(['reviews' => $reviews]);
    }

    /**
     * Get a single review
     */
    public function show($id)
    {
        $review = Review::findOrFail($id);
        return response()->json(['review' => $review]);
    }
}
