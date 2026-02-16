<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Setting;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $banners = Banner::select('banners.*')->orderBy('order');
            
            if ($request->filled('position')) {
                $banners->where('position', $request->position);
            }
            
            if ($request->filled('status')) {
                $banners->where('is_active', $request->status == 'active' ? 1 : 0);
            }
            
            return DataTables::of($banners)
                ->addColumn('image_preview', function($banner) {
                    return '<img src="'.asset('storage/'.$banner->image).'" width="100" class="img-thumbnail">';
                })
                ->addColumn('status_badge', function($banner) {
                    $color = $banner->is_active ? 'success' : 'danger';
                    $text = $banner->is_active ? 'Active' : 'Inactive';
                    return '<span class="badge bg-'.$color.'">'.$text.'</span>';
                })
                ->addColumn('action', function($banner) {
                    $btn = '<button class="btn btn-sm btn-info view-btn" data-id="'.$banner->id.'">
                                <i class="fas fa-eye"></i> View
                            </button> ';
                    $btn .= '<button class="btn btn-sm btn-warning edit-btn" data-id="'.$banner->id.'">
                                <i class="fas fa-edit"></i> Edit
                            </button> ';
                    $btn .= '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$banner->id.'">
                                <i class="fas fa-trash"></i> Delete
                            </button>';
                    return $btn;
                })
                ->rawColumns(['image_preview', 'status_badge', 'action'])
                ->make(true);
        }

        $stats = [
            'total' => Banner::count(),
            'active' => Banner::where('is_active', true)->count(),
            'inactive' => Banner::where('is_active', false)->count(),
        ];

        return view('admin.banners.index', compact('stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'position' => 'required|in:home,listing,category',
            'order' => 'nullable|integer|min:0'
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully');
    }

    public function show($id)
    {
        $banner = Banner::findOrFail($id);
        return response()->json($banner);
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'position' => 'required|in:home,listing,category',
            'order' => 'nullable|integer|min:0'
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Delete image
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();

        return response()->json([
            'success' => true,
            'message' => 'Banner status updated successfully'
        ]);
    }

    public function all()
    {
        $banners = Banner::orderBy('priority', 'desc')
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['banners' => $banners]);
    }

    public function setPriority(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|integer|min:0|max:100',
        ]);

        $banner = Banner::findOrFail($id);
        $banner->priority = $request->priority;
        $banner->save();

        return response()->json([
            'success' => true,
            'message' => 'Banner priority updated successfully'
        ]);
    }

    public function schedule(Request $request, $id)
    {
        $request->validate([
            'scheduled_start' => 'required|date',
            'scheduled_end' => 'required|date|after:scheduled_start',
        ]);

        $banner = Banner::findOrFail($id);
        $banner->scheduled_start = $request->scheduled_start;
        $banner->scheduled_end = $request->scheduled_end;
        $banner->save();

        return response()->json([
            'success' => true,
            'message' => 'Banner scheduled successfully'
        ]);
    }

    public function updatePricing(Request $request)
    {
        $request->validate([
            'featured_price' => 'required|numeric|min:0',
            'boost_price' => 'required|numeric|min:0',
            'boost_duration' => 'required|integer|min:1|max:90',
        ]);

        Setting::set('featured_price', $request->featured_price, 'number', 'pricing');
        Setting::set('boost_price', $request->boost_price, 'number', 'pricing');
        Setting::set('boost_duration', $request->boost_duration, 'number', 'pricing');

        return response()->json([
            'success' => true,
            'message' => 'Pricing updated successfully'
        ]);
    }

    public function getFeaturedAds()
    {
        $featuredAds = Listing::where('is_featured', true)
            ->with(['user', 'category'])
            ->orderBy('featured_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'featured_ads' => $featuredAds
        ]);
    }

    public function approveFeaturedAd(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        $listing->is_featured = true;
        $listing->featured_at = now();
        $listing->featured_until = now()->addDays(30);
        $listing->save();

        return response()->json([
            'success' => true,
            'message' => 'Ad featured successfully'
        ]);
    }

    public function manuallyFeatureAd(Request $request, $id)
    {
        $request->validate([
            'duration' => 'required|integer|min:1|max:365',
        ]);

        $listing = Listing::findOrFail($id);
        $listing->is_featured = true;
        $listing->featured_at = now();
        $listing->featured_until = now()->addDays($request->duration);
        $listing->save();

        return response()->json([
            'success' => true,
            'message' => 'Ad manually featured for ' . $request->duration . ' days'
        ]);
    }

    public function getBoostedAds()
    {
        $boostedAds = Listing::where('is_boosted', true)
            ->with(['user', 'category'])
            ->orderBy('boosted_until', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'boosted_ads' => $boostedAds
        ]);
    }

    public function approveBoost(Request $request, $id)
    {
        $duration = Setting::get('boost_duration', 7);
        
        $listing = Listing::findOrFail($id);
        $listing->is_boosted = true;
        $listing->boosted_until = now()->addDays($duration);
        $listing->save();

        return response()->json([
            'success' => true,
            'message' => 'Boost approved for ' . $duration . ' days'
        ]);
    }

    public function setBoostDuration(Request $request, $id)
    {
        $request->validate([
            'duration' => 'required|integer|min:1|max:90',
        ]);

        $listing = Listing::findOrFail($id);
        $listing->is_boosted = true;
        $listing->boosted_until = now()->addDays($request->duration);
        $listing->save();

        return response()->json([
            'success' => true,
            'message' => 'Boost duration set to ' . $request->duration . ' days'
        ]);
    }

    public function stats()
    {
        return response()->json([
            'total_banners' => Banner::count(),
            'active_banners' => Banner::where('is_active', true)->count(),
            'scheduled_banners' => Banner::whereNotNull('scheduled_start')->count(),
            'featured_ads' => Listing::where('is_featured', true)->count(),
            'boosted_ads' => Listing::where('is_boosted', true)->count(),
            'featured_price' => Setting::get('featured_price', 0),
            'boost_price' => Setting::get('boost_price', 0),
            'boost_duration' => Setting::get('boost_duration', 7),
        ]);
    }
}
