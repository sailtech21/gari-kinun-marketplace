<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\User;
use App\Models\Report;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        // Comprehensive Stats
        $stats = [
            'total_users' => User::count(),
            'total_dealers' => User::where('role', 'dealer')->count(),
            'total_ads' => Listing::count(),
            'active_ads' => Listing::where('status', 'active')->count(),
            'pending_ads' => Listing::where('status', 'pending')->count(),
            'rejected_ads' => Listing::where('status', 'rejected')->count(),
            'reported_ads' => Report::distinct('listing_id')->count('listing_id'),
            'revenue_today' => 0, // Placeholder - integrate with payment system
            'monthly_revenue' => 0, // Placeholder - integrate with payment system
            'new_users_today' => User::whereDate('created_at', $today)->count(),
            'new_ads_today' => Listing::whereDate('created_at', $today)->count(),
        ];
        
        // Boosted/Featured ads - optional column
        try {
            $stats['boosted_ads'] = Listing::where('is_featured', true)->count();
        } catch (\Exception $e) {
            $stats['boosted_ads'] = 0;
        }

        // Recent Listings
        $recentListings = Listing::with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        // Popular Categories with listing counts
        // Using subquery to avoid MySQL strict mode GROUP BY issues
        $popularCategories = DB::table('categories')
            ->select('categories.*', DB::raw('COALESCE(listing_counts.count, 0) as listings_count'))
            ->leftJoin(
                DB::raw('(SELECT category_id, COUNT(*) as count FROM listings GROUP BY category_id) as listing_counts'),
                'categories.id', '=', 'listing_counts.category_id'
            )
            ->orderBy('listings_count', 'desc')
            ->limit(10)
            ->get();

        // Database-agnostic date formatting
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            $dateFormat = "strftime('%Y-%m-%d', created_at)";
            $monthFormat = "strftime('%Y-%m', created_at)";
        } else {
            $dateFormat = "DATE_FORMAT(created_at, '%Y-%m-%d')";
            $monthFormat = "DATE_FORMAT(created_at, '%Y-%m')";
        }
        
        // Ads Growth (Last 7 days)
        $adsGrowth = Listing::select(
            DB::raw("$dateFormat as date"),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // User Growth (Last 7 days)
        $userGrowth = User::select(
            DB::raw("$dateFormat as date"),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Monthly Revenue (Last 6 months) - Placeholder
        $revenueChart = collect([]);
        for ($i = 5; $i >= 0; $i--) {
            $revenueChart->push([
                'month' => Carbon::now()->subMonths($i)->format('Y-m'),
                'revenue' => rand(1000, 5000) // Placeholder - replace with actual payment data
            ]);
        }

        return view('admin.dashboard', compact(
            'stats', 
            'recentListings', 
            'popularCategories',
            'adsGrowth',
            'userGrowth',
            'revenueChart'
        ));
    }
}