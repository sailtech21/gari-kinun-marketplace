<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Store a new contact inquiry
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listing_id' => 'nullable|exists:listings,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $contact = Contact::create([
            'listing_id' => $request->listing_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'message' => $request->message,
            'status' => 'pending',
            'ip_address' => $request->ip()
        ]);

        // Send notification to seller if listing exists
        if ($request->listing_id) {
            $listing = Listing::with(['user', 'dealer'])->find($request->listing_id);
            
            if ($listing) {
                $sellerEmail = null;
                $sellerName = 'বিক্রেতা';
                
                if ($listing->user_id && $listing->user && $listing->user->email) {
                    $sellerEmail = $listing->user->email;
                    $sellerName = $listing->user->name;
                } elseif ($listing->dealer_id && $listing->dealer && $listing->dealer->email) {
                    $sellerEmail = $listing->dealer->email;
                    $sellerName = $listing->dealer->business_name ?? $listing->dealer->name;
                }
                
                // Send email notification to seller
                if ($sellerEmail) {
                    try {
                        Mail::send([], [], function ($message) use ($sellerEmail, $sellerName, $contact, $listing) {
                            $message->to($sellerEmail)
                                ->subject('নতুন ক্রেতার বার্তা - ' . $listing->title)
                                ->html(
                                    "<h2>প্রিয় {$sellerName},</h2>" .
                                    "<p>আপনার বিজ্ঞাপনে একজন ক্রেতা আগ্রহ দেখিয়েছেন।</p>" .
                                    "<h3>বিজ্ঞাপনঃ {$listing->title}</h3>" .
                                    "<h3>ক্রেতার তথ্যঃ</h3>" .
                                    "<ul>" .
                                    "<li><strong>নামঃ</strong> {$contact->name}</li>" .
                                    "<li><strong>ফোনঃ</strong> {$contact->phone}</li>" .
                                    ($contact->email ? "<li><strong>ইমেইলঃ</strong> {$contact->email}</li>" : "") .
                                    "</ul>" .
                                    "<h3>বার্তাঃ</h3>" .
                                    "<p>{$contact->message}</p>" .
                                    "<br><p>দ্রুত সাড়া দিয়ে ক্রেতার সাথে যোগাযোগ করুন।</p>" .
                                    "<p><strong>GariKinun.com</strong> - বাংলাদেশের সেরা গাড়ি বিক্রয় প্ল্যাটফর্ম</p>"
                                );
                        });
                    } catch (\Exception $e) {
                        // Log error but don't fail the request
                        \Log::error('Failed to send seller notification: ' . $e->getMessage());
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'আপনার মেসেজ সফলভাবে পাঠানো হয়েছে! বিক্রেতা শীঘ্রই আপনার সাথে যোগাযোগ করবে।',
            'data' => $contact
        ], 201);
    }

    /**
     * Get all locations (cities/districts)
     */
    public function getLocations()
    {
        $locations = [
            ['id' => 1, 'name' => 'ঢাকা', 'nameEn' => 'Dhaka', 'count' => 1245, 'division' => 'ঢাকা', 'popular' => true],
            ['id' => 2, 'name' => 'চট্টগ্রাম', 'nameEn' => 'Chattogram', 'count' => 892, 'division' => 'চট্টগ্রাম', 'popular' => true],
            ['id' => 3, 'name' => 'সিলেট', 'nameEn' => 'Sylhet', 'count' => 456, 'division' => 'সিলেট', 'popular' => true],
            ['id' => 4, 'name' => 'রাজশাহী', 'nameEn' => 'Rajshahi', 'count' => 389, 'division' => 'রাজশাহী', 'popular' => true],
            ['id' => 5, 'name' => 'খুলনা', 'nameEn' => 'Khulna', 'count' => 267, 'division' => 'খুলনা', 'popular' => true],
            ['id' => 6, 'name' => 'বরিশাল', 'nameEn' => 'Barisal', 'count' => 198, 'division' => 'বরিশাল', 'popular' => true],
            ['id' => 7, 'name' => 'রংপুর', 'nameEn' => 'Rangpur', 'count' => 156, 'division' => 'রংপুর', 'popular' => false],
            ['id' => 8, 'name' => 'ময়মনসিংহ', 'nameEn' => 'Mymensingh', 'count' => 134, 'division' => 'ময়মনসিংহ', 'popular' => false],
            ['id' => 9, 'name' => 'কুমিল্লা', 'nameEn' => 'Cumilla', 'count' => 123, 'division' => 'চট্টগ্রাম', 'popular' => false],
            ['id' => 10, 'name' => 'নারায়ণগঞ্জ', 'nameEn' => 'Narayanganj', 'count' => 112, 'division' => 'ঢাকা', 'popular' => false],
            ['id' => 11, 'name' => 'গাজীপুর', 'nameEn' => 'Gazipur', 'count' => 98, 'division' => 'ঢাকা', 'popular' => false],
            ['id' => 12, 'name' => 'যশোর', 'nameEn' => 'Jashore', 'count' => 87, 'division' => 'খুলনা', 'popular' => false],
            ['id' => 13, 'name' => 'বগুড়া', 'nameEn' => 'Bogura', 'count' => 76, 'division' => 'রাজশাহী', 'popular' => false],
            ['id' => 14, 'name' => 'দিনাজপুর', 'nameEn' => 'Dinajpur', 'count' => 65, 'division' => 'রংপুর', 'popular' => false],
            ['id' => 15, 'name' => 'কক্সবাজার', 'nameEn' => 'Cox\'s Bazar', 'count' => 54, 'division' => 'চট্টগ্রাম', 'popular' => false],
        ];

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Get public statistics
     */
    public function getStats()
    {
        $stats = [
            'total_listings' => \App\Models\Listing::count(),
            'active_listings' => \App\Models\Listing::where('status', 'active')->count(),
            'total_users' => \App\Models\User::count(),
            'total_categories' => \App\Models\Category::count(),
            'total_dealers' => \App\Models\User::where('role', 'dealer')->count(),
            'total_locations' => 15,
            'featured_listings' => \App\Models\Listing::where('is_featured', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
