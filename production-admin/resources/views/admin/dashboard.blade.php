@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('styles')
<style>
    .stat-card {
        position: relative;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        color: white;
        border: none;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3) !important;
    }
    .stat-card i {
        font-size: 3rem;
        opacity: 0.2;
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
    }
    .stat-card h2 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .stat-card p {
        margin-bottom: 0;
        font-size: 1rem;
        opacity: 0.9;
    }
    
    .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .bg-gradient-purple { background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%); }
    .bg-gradient-pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
    .bg-gradient-indigo { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
    .bg-gradient-teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }
    .bg-gradient-orange { background: linear-gradient(135deg, #fb923c 0%, #f97316 100%); }
    .bg-gradient-cyan { background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%); }
    .bg-gradient-lime { background: linear-gradient(135deg, #84cc16 0%, #65a30d 100%); }

    .quick-action-btn {
        padding: 20px;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        background: white;
        transition: all 0.3s;
        text-align: center;
        cursor: pointer;
        text-decoration: none;
        display: block;
    }
    .quick-action-btn:hover {
        border-color: #667eea;
        background: #f9fafb;
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }
    .quick-action-btn i {
        font-size: 2rem;
        color: #667eea;
        display: block;
        margin-bottom: 10px;
    }
    .quick-action-btn span {
        display: block;
        color: #374151;
        font-weight: 600;
    }

    .chart-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .chart-container {
        position: relative;
        height: 300px;
        padding: 20px;
    }
    
    .category-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    .category-item:last-child {
        border-bottom: none;
    }
    .category-name {
        font-weight: 600;
        color: #374151;
    }
    .category-count {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 3px solid #667eea;
        display: inline-block;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="mb-4">
    <h2 class="mb-0"><i class="fas fa-chart-line me-2"></i>📊 Dashboard Overview</h2>
</div>

<!-- Overview Widgets -->
<div class="row">
    <!-- Total Users -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card stat-card bg-gradient-primary">
            <i class="fas fa-users"></i>
            <h2>{{ number_format($stats['total_users']) }}</h2>
            <p>Total Users</p>
        </div>
    </div>

    <!-- Total Dealers -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card stat-card bg-gradient-success">
            <i class="fas fa-store"></i>
            <h2>{{ number_format($stats['total_dealers']) }}</h2>
            <p>Total Dealers</p>
        </div>
    </div>

    <!-- Total Ads -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card stat-card bg-gradient-info">
            <i class="fas fa-list"></i>
            <h2>{{ number_format($stats['total_ads']) }}</h2>
            <p>Total Ads</p>
        </div>
    </div>

    <!-- Active Ads -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card stat-card bg-gradient-success">
            <i class="fas fa-check-circle"></i>
            <h2>{{ number_format($stats['active_ads']) }}</h2>
            <p>Active Ads</p>
        </div>
    </div>

    <!-- Pending Ads -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card stat-card bg-gradient-warning">
            <i class="fas fa-clock"></i>
            <h2>{{ number_format($stats['pending_ads']) }}</h2>
            <p>Pending Ads</p>
        </div>
    </div>

    <!-- Rejected Ads -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card stat-card bg-gradient-danger">
            <i class="fas fa-times-circle"></i>
            <h2>{{ number_format($stats['rejected_ads']) }}</h2>
            <p>Rejected Ads</p>
        </div>
    </div>

    <!-- Reported Ads -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card stat-card bg-gradient-purple">
            <i class="fas fa-flag"></i>
            <h2>{{ number_format($stats['reported_ads']) }}</h2>
            <p>Reported Ads</p>
        </div>
    </div>

    <!-- Boosted Ads -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card stat-card bg-gradient-pink">
            <i class="fas fa-rocket"></i>
            <h2>{{ number_format($stats['boosted_ads']) }}</h2>
            <p>Boosted Ads</p>
        </div>
    </div>

    <!-- Revenue Today -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card stat-card bg-gradient-indigo">
            <i class="fas fa-dollar-sign"></i>
            <h2>৳{{ number_format($stats['revenue_today']) }}</h2>
            <p>Revenue Today</p>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card stat-card bg-gradient-teal">
            <i class="fas fa-chart-bar"></i>
            <h2>৳{{ number_format($stats['monthly_revenue']) }}</h2>
            <p>Monthly Revenue</p>
        </div>
    </div>

    <!-- New Users Today -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card stat-card bg-gradient-orange">
            <i class="fas fa-user-plus"></i>
            <h2>{{ number_format($stats['new_users_today']) }}</h2>
            <p>New Users Today</p>
        </div>
    </div>

    <!-- New Ads Today -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card stat-card bg-gradient-cyan">
            <i class="fas fa-plus-circle"></i>
            <h2>{{ number_format($stats['new_ads_today']) }}</h2>
            <p>New Ads Today</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <h4 class="section-title"><i class="fas fa-bolt me-2"></i>Quick Actions</h4>
    </div>
    
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
        <a href="{{ route('admin.listings.index') }}?status=pending" class="quick-action-btn">
            <i class="fas fa-clipboard-check"></i>
            <span>Approve Pending Ads</span>
        </a>
    </div>

    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
        <a href="{{ route('admin.reports.index') }}" class="quick-action-btn">
            <i class="fas fa-eye"></i>
            <span>View Reports</span>
        </a>
    </div>

    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
        <a href="{{ route('admin.notifications.index') }}" class="quick-action-btn">
            <i class="fas fa-bullhorn"></i>
            <span>Send Announcement</span>
        </a>
    </div>

    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
        <a href="{{ route('admin.categories.index') }}" class="quick-action-btn">
            <i class="fas fa-folder-plus"></i>
            <span>Add Category</span>
        </a>
    </div>

    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
        <a href="{{ route('admin.banners.index') }}" class="quick-action-btn">
            <i class="fas fa-image"></i>
            <span>Add Banner</span>
        </a>
    </div>

    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
        <a href="{{ route('admin.settings.index') }}" class="quick-action-btn">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </div>
</div>

<!-- Charts Section -->
<div class="row mt-4">
    <!-- Ads Growth Chart -->
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Ads Growth (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="adsGrowthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- User Growth Chart -->
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="fas fa-users text-success me-2"></i>User Growth (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card chart-card">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="fas fa-dollar-sign text-info me-2"></i>Revenue Chart (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Categories -->
    <div class="col-lg-4 mb-4">
        <div class="card chart-card">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="fas fa-tags text-warning me-2"></i>Popular Categories</h5>
            </div>
            <div class="card-body">
                @if($popularCategories->count() > 0)
                    @foreach($popularCategories as $category)
                        <div class="category-item">
                            <span class="category-name">
                                @if($category->icon)
                                    <i class="{{ $category->icon }} me-2"></i>
                                @endif
                                {{ $category->name }}
                            </span>
                            <span class="category-count">{{ number_format($category->listings_count) }}</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center py-4">No categories with listings yet</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Listings -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card chart-card">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="fas fa-clock text-danger me-2"></i>Recent Listings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>User</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentListings as $listing)
                                <tr>
                                    <td>
                                        <strong>{{ Str::limit($listing->title, 40) }}</strong>
                                        @if($listing->is_featured)
                                            <span class="badge bg-warning ms-1">Featured</span>
                                        @endif
                                    </td>
                                    <td>{{ $listing->category->name ?? 'N/A' }}</td>
                                    <td>{{ $listing->user->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($listing->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($listing->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($listing->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($listing->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-primary">৳{{ number_format($listing->price) }}</td>
                                    <td>{{ $listing->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.listings.show', $listing->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No recent listings</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Ads Growth Chart
const adsGrowthData = @json($adsGrowth);
const adsGrowthCtx = document.getElementById('adsGrowthChart').getContext('2d');
new Chart(adsGrowthCtx, {
    type: 'line',
    data: {
        labels: adsGrowthData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }),
        datasets: [{
            label: 'New Ads',
            data: adsGrowthData.map(item => item.total),
            borderColor: 'rgb(102, 126, 234)',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { 
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// User Growth Chart
const userGrowthData = @json($userGrowth);
const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
new Chart(userGrowthCtx, {
    type: 'bar',
    data: {
        labels: userGrowthData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }),
        datasets: [{
            label: 'New Users',
            data: userGrowthData.map(item => item.total),
            backgroundColor: 'rgba(16, 185, 129, 0.8)',
            borderColor: 'rgb(16, 185, 129)',
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { 
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// Revenue Chart
const revenueData = @json($revenueChart);
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueData.map(item => {
            const [year, month] = item.month.split('-');
            const date = new Date(year, month - 1);
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        }),
        datasets: [{
            label: 'Revenue (৳)',
            data: revenueData.map(item => item.revenue),
            borderColor: 'rgb(6, 182, 212)',
            backgroundColor: 'rgba(6, 182, 212, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: 'rgb(6, 182, 212)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { 
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '৳' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endsection
