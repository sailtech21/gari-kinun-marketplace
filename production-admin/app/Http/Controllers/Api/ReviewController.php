<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display approved reviews
     */
    public function index()
    {
        $reviews = Review::approved()
            ->latest()
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'name' => $review->name,
                    'location' => $review->location,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'purchase' => $review->purchase,
                    'is_featured' => $review->is_featured,
                    'date' => $review->created_at->format('Y-m-d'),
                    'avatar' => 'https://i.pravatar.cc/150?u=' . $review->id
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:20',
            'purchase' => 'nullable|string|max:255',
        ], [
            'name.required' => 'নাম প্রয়োজন',
            'location.required' => 'অবস্থান প্রয়োজন',
            'rating.required' => 'রেটিং প্রয়োজন',
            'rating.min' => 'রেটিং কমপক্ষে ১ হতে হবে',
            'rating.max' => 'রেটিং সর্বোচ্চ ৫ হতে পারে',
            'comment.required' => 'মন্তব্য প্রয়োজন',
            'comment.min' => 'মন্তব্য কমপক্ষে ২০ অক্ষরের হতে হবে'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $review = Review::create([
            'name' => $request->name,
            'location' => $request->location,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'purchase' => $request->purchase,
            'is_approved' => false, // Requires admin approval
            'is_featured' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'আপনার রিভিউ সফলভাবে সাবমিট হয়েছে! এটি অনুমোদনের পর প্রদর্শিত হবে।',
            'data' => $review
        ], 201);
    }
}
