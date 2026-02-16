<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Get pricing information
     */
    public function getPricing()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'featured_listing' => [
                    'price' => 500,
                    'currency' => 'BDT',
                    'duration_days' => 30,
                    'features' => [
                        'Top position in search results',
                        'Featured badge',
                        'Homepage featured section',
                        '3x more visibility'
                    ]
                ],
                'subscriptions' => [
                    'free' => [
                        'name' => 'Free',
                        'price' => 0,
                        'listing_limit' => 5,
                        'features' => ['Up to 5 listings', 'Basic support']
                    ],
                    'premium' => [
                        'name' => 'Premium',
                        'price' => 2000,
                        'listing_limit' => 999,
                        'features' => [
                            'Unlimited listings',
                            'Verified dealer badge',
                            'Priority support',
                            'Analytics dashboard'
                        ]
                    ],
                    'pro' => [
                        'name' => 'Pro',
                        'price' => 5000,
                        'listing_limit' => 999,
                        'features' => [
                            'Everything in Premium',
                            'Top placement in dealer list',
                            'Featured listings discount',
                            'Dedicated account manager',
                            'API access'
                        ]
                    ]
                ],
                'banner_ads' => [
                    'price' => 7000,
                    'currency' => 'BDT',
                    'duration_days' => 30,
                    'features' => [
                        'Homepage banner placement',
                        'Mobile & desktop visibility',
                        'Click tracking',
                        'Impression analytics'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Purchase featured listing
     */
    public function purchaseFeaturedListing(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'payment_method' => 'required|in:bkash,nagad,rocket,card',
            'transaction_id' => 'required|string',
        ]);

        $listing = Listing::findOrFail($request->listing_id);

        // Verify ownership
        if ($listing->user_id !== auth()->id() && $listing->dealer_id !== auth()->user()->dealer?->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to feature this listing'
            ], 403);
        }

        // TODO: Integrate with actual payment gateway (bKash, Nagad, etc.)
        // For now, we'll simulate successful payment

        DB::beginTransaction();
        try {
            $listing->update([
                'is_featured' => true,
                'featured_at' => now(),
                'featured_until' => now()->addDays(30),
                'featured_price' => 500
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Listing featured successfully',
                'data' => [
                    'listing' => $listing,
                    'featured_until' => $listing->featured_until->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Purchase dealer subscription
     */
    public function purchaseSubscription(Request $request)
    {
        $request->validate([
            'tier' => 'required|in:premium,pro',
            'payment_method' => 'required|in:bkash,nagad,rocket,card',
            'transaction_id' => 'required|string',
        ]);

        $dealer = auth()->user()->dealer;
        
        if (!$dealer) {
            return response()->json([
                'success' => false,
                'message' => 'You must be a dealer to purchase subscriptions'
            ], 403);
        }

        $prices = [
            'premium' => 2000,
            'pro' => 5000
        ];

        // TODO: Integrate with actual payment gateway

        DB::beginTransaction();
        try {
            $dealer->update([
                'subscription_tier' => $request->tier,
                'subscription_starts_at' => now(),
                'subscription_ends_at' => now()->addMonth(),
                'subscription_price' => $prices[$request->tier],
                'listing_limit' => 999
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subscription activated successfully',
                'data' => [
                    'dealer' => $dealer,
                    'expires_at' => $dealer->subscription_ends_at->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Subscription activation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($transactionId)
    {
        // TODO: Check with payment gateway
        return response()->json([
            'success' => true,
            'data' => [
                'transaction_id' => $transactionId,
                'status' => 'completed',
                'amount' => 500,
                'currency' => 'BDT'
            ]
        ]);
    }

    /**
     * Request banner ad placement
     */
    public function requestBannerAd(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|max:2048',
            'link' => 'required|url',
            'advertiser_name' => 'required|string|max:255',
            'advertiser_email' => 'required|email',
            'advertiser_phone' => 'required|string|max:20',
            'payment_method' => 'required|in:bkash,nagad,rocket,card',
            'transaction_id' => 'required|string',
        ]);

        // TODO: Upload image and integrate payment gateway

        return response()->json([
            'success' => true,
            'message' => 'Banner ad request submitted. Admin will review and activate within 24 hours.',
            'data' => [
                'price' => 7000,
                'duration_days' => 30
            ]
        ]);
    }
}

