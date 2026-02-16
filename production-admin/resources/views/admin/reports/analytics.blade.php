@extends('admin.layouts.app')

@section('title', 'Analytics Dashboard')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    
    .stat-card.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-card.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .stat-card.bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-card.bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-card.bg-gradient-danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .stat-card.bg-gradient-dark { background: linear-gradient(135deg, #3a7bd5 0%, #3a6073 100%); }
    
    .stat-card .icon {
        font-size: 2rem;
        opacity: 0.5;
        margin-bottom: 10px;
    }
    
    .stat-card .number {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 10px 0 5px;
    }
    
    .stat-card .label {
        font-size: 0.95rem;
        opacity: 0.9;
    }
    
    .analytics-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .analytics-card h5 {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .table-analytics {
        font-size: 0.9rem;
    }
    
    .table-analytics th {
        background: #f8f9fa;
        font-weight: 600;
    }
    
    .badge-views {
        background: #667eea;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
    }
    
    .progress-custom {
        height: 25px;
        border-radius: 10px;
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        padding: 12px 24px;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #667eea;
        background: transparent;
        border-bottom: 3px solid #667eea;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">📈 Analytics Dashboard</h1>
        <div>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
            <button class="btn btn-success" onclick="window.location.reload()">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card bg-gradient-primary">
            <div class="icon"><i class="fas fa-bullhorn"></i></div>
            <div class="number">{{ number_format($listingsCount) }}</div>
            <div class="label">Total Listings</div>
        </div>
        <div class="stat-card bg-gradient-success">
            <div class="icon"><i class="fas fa-users"></i></div>
            <div class="number">{{ number_format($usersCount) }}</div>
            <div class="label">Total Users</div>
        </div>
        <div class="stat-card bg-gradient-warning">
            <div class="icon"><i class="fas fa-user-tie"></i></div>
            <div class="number">{{ number_format($dealersCount) }}</div>
            <div class="label">Dealers</div>
        </div>
        <div class="stat-card bg-gradient-info">
            <div class="icon"><i class="fas fa-eye"></i></div>
            <div class="number">{{ number_format($mostViewedAds->sum('views')) }}</div>
            <div class="label">Total Views</div>
        </div>
        <div class="stat-card bg-gradient-danger">
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="number">{{ number_format($totalRevenue) }}</div>
            <div class="label">Total Revenue (BDT)</div>
        </div>
        <div class="stat-card bg-gradient-dark">
            <div class="icon"><i class="fas fa-percentage"></i></div>
            <div class="number">{{ $conversionRate['conversion_rate'] }}%</div>
            <div class="label">Conversion Rate</div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#viewedAdsTab">
                <i class="fas fa-fire"></i> Most Viewed Ads
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#activeUsersTab">
                <i class="fas fa-user-friends"></i> Most Active Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#locationsTab">
                <i class="fas fa-map-marker-alt"></i> Popular Locations
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#categoriesTab">
                <i class="fas fa-tags"></i> Popular Categories
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#dealerPerformanceTab">
                <i class="fas fa-chart-line"></i> Dealer Performance
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#revenueTab">
                <i class="fas fa-money-bill-wave"></i> Revenue
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#conversionTab">
                <i class="fas fa-exchange-alt"></i> Conversions
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#trafficTab">
                <i class="fas fa-globe"></i> Traffic Source
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Most Viewed Ads Tab -->
        <div class="tab-pane fade show active" id="viewedAdsTab">
            <div class="analytics-card">
                <h5><i class="fas fa-fire text-danger"></i> Most Viewed Ads (Top 10)</h5>
                <div class="table-responsive">
                    <table class="table table-analytics table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Owner</th>
                                <th>Views</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mostViewedAds as $index => $ad)
                            <tr style="cursor: pointer;" onclick="window.open('{{ route('admin.listings.show', $ad->id) }}', '_blank')">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.listings.show', $ad->id) }}" target="_blank">
                                        {{ Str::limit($ad->title, 40) }}
                                    </a>
                                </td>
                                <td>{{ $ad->category->name ?? 'N/A' }}</td>
                                <td>{{ $ad->user->name ?? 'N/A' }}</td>
                                <td><span class="badge-views">{{ number_format($ad->views) }} views</span></td>
                                <td>
                                    <span class="badge bg-{{ $ad->status === 'active' ? 'success' : 'warning' }}">
                                        {{ ucfirst($ad->status) }}
                                    </span>
                                </td>
                                <td>{{ $ad->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Most Active Users Tab -->
        <div class="tab-pane fade" id="activeUsersTab">
            <div class="analytics-card">
                <h5><i class="fas fa-user-friends text-primary"></i> Most Active Users (Top 10)</h5>
                <div class="table-responsive">
                    <table class="table table-analytics table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Total Listings</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mostActiveUsers as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role === 'dealer' ? 'info' : 'secondary' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td><strong>{{ number_format($user->listings_count) }}</strong> listings</td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Popular Locations Tab -->
        <div class="tab-pane fade" id="locationsTab">
            <div class="analytics-card">
                <h5><i class="fas fa-map-marker-alt text-danger"></i> Popular Locations (Top 10)</h5>
                <div class="table-responsive">
                    <table class="table table-analytics table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Location</th>
                                <th>Total Listings</th>
                                <th>Percentage</th>
                                <th>Visual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalLocations = $popularLocations->sum('count'); @endphp
                            @forelse($popularLocations as $index => $location)
                            @php $percentage = $totalLocations > 0 ? ($location->count / $totalLocations) * 100 : 0; @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $location->location }}</strong></td>
                                <td>{{ number_format($location->count) }} listings</td>
                                <td>{{ number_format($percentage, 1) }}%</td>
                                <td>
                                    <div class="progress progress-custom">
                                        <div class="progress-bar bg-info" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Popular Categories Tab -->
        <div class="tab-pane fade" id="categoriesTab">
            <div class="analytics-card">
                <h5><i class="fas fa-tags text-success"></i> Popular Categories (Top 10)</h5>
                <div class="table-responsive">
                    <table class="table table-analytics table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Total Listings</th>
                                <th>Percentage</th>
                                <th>Visual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalCategoryListings = $popularCategories->sum('listings_count'); @endphp
                            @forelse($popularCategories as $index => $category)
                            @php $percentage = $totalCategoryListings > 0 ? ($category->listings_count / $totalCategoryListings) * 100 : 0; @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>{{ number_format($category->listings_count) }} listings</td>
                                <td>{{ number_format($percentage, 1) }}%</td>
                                <td>
                                    <div class="progress progress-custom">
                                        <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Dealer Performance Tab -->
        <div class="tab-pane fade" id="dealerPerformanceTab">
            <div class="analytics-card">
                <h5><i class="fas fa-chart-line text-primary"></i> Dealer Performance (Top 10)</h5>
                <div class="table-responsive">
                    <table class="table table-analytics table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Dealer Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Total Listings</th>
                                <th>Active Listings</th>
                                <th>Total Views</th>
                                <th>Revenue (BDT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dealerPerformance as $index => $dealer)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $dealer['name'] }}</strong></td>
                                <td>{{ $dealer['email'] }}</td>
                                <td>
                                    <span class="badge bg-{{ $dealer['dealer_status'] === 'premium' ? 'warning' : 'info' }}">
                                        {{ ucfirst($dealer['dealer_status']) }}
                                    </span>
                                </td>
                                <td>{{ number_format($dealer['listings_count']) }}</td>
                                <td>{{ number_format($dealer['active_listings']) }}</td>
                                <td>{{ number_format($dealer['total_views']) }}</td>
                                <td>{{ number_format($dealer['total_revenue']) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No dealer data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Revenue Breakdown Tab -->
        <div class="tab-pane fade" id="revenueTab">
            <div class="analytics-card">
                <h5><i class="fas fa-money-bill-wave text-success"></i> Revenue Breakdown</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-analytics">
                                <thead>
                                    <tr>
                                        <th>Revenue Source</th>
                                        <th class="text-end">Amount (BDT)</th>
                                        <th class="text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenueBreakdown as $source => $amount)
                                    @php 
                                        $percentage = $totalRevenue > 0 ? ($amount / $totalRevenue) * 100 : 0;
                                        $label = ucwords(str_replace('_', ' ', $source));
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $label }}</strong></td>
                                        <td class="text-end">{{ number_format($amount) }}</td>
                                        <td class="text-end">{{ number_format($percentage, 1) }}%</td>
                                    </tr>
                                    @endforeach
                                    <tr class="table-primary">
                                        <td><strong>Total Revenue</strong></td>
                                        <td class="text-end"><strong>{{ number_format($totalRevenue) }}</strong></td>
                                        <td class="text-end"><strong>100%</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversion Rate Tab -->
        <div class="tab-pane fade" id="conversionTab">
            <div class="analytics-card">
                <h5><i class="fas fa-exchange-alt text-info"></i> Conversion Rate Analysis</h5>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-primary">{{ number_format($conversionRate['total_listings']) }}</h3>
                            <p class="mb-0">Total Listings</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-info">{{ number_format($conversionRate['listings_with_clicks']) }}</h3>
                            <p class="mb-0">Listings with Clicks</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-warning">{{ number_format($conversionRate['listings_with_phone_reveals']) }}</h3>
                            <p class="mb-0">Phone Reveals</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-success">{{ number_format($conversionRate['listings_with_conversions']) }}</h3>
                            <p class="mb-0">Conversions</p>
                        </div>
                    </div>
                </div>

                <h6 class="mb-3">Conversion Funnel</h6>
                <div class="mb-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Click Rate</span>
                            <strong>{{ $conversionRate['click_rate'] }}%</strong>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar bg-info" style="width: {{ $conversionRate['click_rate'] }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Phone Reveal Rate</span>
                            <strong>{{ $conversionRate['phone_reveal_rate'] }}%</strong>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar bg-warning" style="width: {{ $conversionRate['phone_reveal_rate'] }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Conversion Rate</span>
                            <strong>{{ $conversionRate['conversion_rate'] }}%</strong>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar bg-success" style="width: {{ $conversionRate['conversion_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Traffic Source Tab -->
        <div class="tab-pane fade" id="trafficTab">
            <div class="analytics-card">
                <h5><i class="fas fa-globe text-primary"></i> Traffic Source Distribution</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-analytics table-hover">
                                <thead>
                                    <tr>
                                        <th>Source</th>
                                        <th class="text-end">Sessions</th>
                                        <th class="text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalTraffic = $trafficSources->sum('count'); @endphp
                                    @forelse($trafficSources as $source)
                                    @php $percentage = $totalTraffic > 0 ? ($source->count / $totalTraffic) * 100 : 0; @endphp
                                    <tr>
                                        <td>
                                            <i class="fas fa-circle text-{{ $source->traffic_source === 'google' ? 'danger' : ($source->traffic_source === 'facebook' ? 'primary' : 'secondary') }}"></i>
                                            <strong>{{ ucfirst($source->traffic_source) }}</strong>
                                        </td>
                                        <td class="text-end">{{ number_format($source->count) }}</td>
                                        <td class="text-end">{{ number_format($percentage, 1) }}%</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No traffic data available</td>
                                    </tr>
                                    @endforelse
                                    @if($totalTraffic > 0)
                                    <tr class="table-light">
                                        <td><strong>Total</strong></td>
                                        <td class="text-end"><strong>{{ number_format($totalTraffic) }}</strong></td>
                                        <td class="text-end"><strong>100%</strong></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                            <canvas id="trafficChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart');
if (revenueCtx) {
    new Chart(revenueCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($revenueBreakdown as $source => $amount)
                '{{ ucwords(str_replace("_", " ", $source)) }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($revenueBreakdown as $amount)
                    {{ $amount }},
                    @endforeach
                ],
                backgroundColor: [
                    '#667eea',
                    '#11998e',
                    '#f093fb',
                    '#4facfe'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Traffic Source Chart
const trafficCtx = document.getElementById('trafficChart');
if (trafficCtx) {
    new Chart(trafficCtx, {
        type: 'pie',
        data: {
            labels: [
                @foreach($trafficSources as $source)
                '{{ ucfirst($source->traffic_source) }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($trafficSources as $source)
                    {{ $source->count }},
                    @endforeach
                ],
                backgroundColor: [
                    '#dc3545',
                    '#0d6efd',
                    '#198754',
                    '#ffc107',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
</script>
@endsection
