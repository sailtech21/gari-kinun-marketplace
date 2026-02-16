@extends('admin.layouts.app')

@section('title', 'Promotions Management')

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
    
    .banner-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    
    .banner-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .banner-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .priority-badge {
        background: #10b981;
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .pricing-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
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
        <h1 class="h3 mb-0 text-gray-800">📢 Promotions Management</h1>
        <button class="btn btn-primary" onclick="showAddBannerModal()">
            <i class="fas fa-plus"></i> Add Banner
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card bg-gradient-primary">
            <div class="icon"><i class="fas fa-rectangle-ad"></i></div>
            <div class="number" id="statTotalBanners">0</div>
            <div class="label">Total Banners</div>
        </div>
        <div class="stat-card bg-gradient-success">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <div class="number" id="statActiveBanners">0</div>
            <div class="label">Active Banners</div>
        </div>
        <div class="stat-card bg-gradient-warning">
            <div class="icon"><i class="fas fa-star"></i></div>
            <div class="number" id="statFeaturedAds">0</div>
            <div class="label">Featured Ads</div>
        </div>
        <div class="stat-card bg-gradient-info">
            <div class="icon"><i class="fas fa-rocket"></i></div>
            <div class="number" id="statBoostedAds">0</div>
            <div class="label">Boosted Ads</div>
        </div>
        <div class="stat-card bg-gradient-danger">
            <div class="icon"><i class="fas fa-calendar"></i></div>
            <div class="number" id="statScheduled">0</div>
            <div class="label">Scheduled</div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#bannersTab">
                <i class="fas fa-rectangle-ad"></i> Banners
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#featuredTab">
                <i class="fas fa-star"></i> Featured Ads
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#boostedTab">
                <i class="fas fa-rocket"></i> Boosted Ads
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#pricingTab">
                <i class="fas fa-dollar-sign"></i> Pricing
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Banners Tab -->
        <div class="tab-pane fade show active" id="bannersTab">
            <div class="row" id="bannersContainer">
                <!-- Banners will be loaded here -->
            </div>
        </div>

        <!-- Featured Ads Tab -->
        <div class="tab-pane fade" id="featuredTab">
            <div class="table-responsive">
                <table class="table table-hover" id="featuredTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>User</th>
                            <th>Category</th>
                            <th>Featured Until</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="featuredAdsContainer">
                        <!-- Featured ads will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Boosted Ads Tab -->
        <div class="tab-pane fade" id="boostedTab">
            <div class="table-responsive">
                <table class="table table-hover" id="boostedTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>User</th>
                            <th>Category</th>
                            <th>Boosted Until</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="boostedAdsContainer">
                        <!-- Boosted ads will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pricing Tab -->
        <div class="tab-pane fade" id="pricingTab">
            <div class="row">
                <div class="col-md-6">
                    <div class="pricing-card">
                        <h5><i class="fas fa-star text-warning"></i> Featured Ad Pricing</h5>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Featured Price ($)*</label>
                            <input type="number" class="form-control" id="featuredPrice" step="0.01" min="0">
                            <small class="text-muted">Price per 30 days</small>
                        </div>
                        <button class="btn btn-primary" onclick="updatePricing()">Save Pricing</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="pricing-card">
                        <h5><i class="fas fa-rocket text-info"></i> Boost Ad Pricing</h5>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Boost Price ($)*</label>
                            <input type="number" class="form-control" id="boostPrice" step="0.01" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Default Boost Duration (Days)*</label>
                            <input type="number" class="form-control" id="boostDuration" min="1" max="90">
                        </div>
                        <button class="btn btn-primary" onclick="updatePricing()">Save Pricing</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Banner Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bannerForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title*</label>
                        <input type="text" class="form-control" id="bannerTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image*</label>
                        <input type="file" class="form-control" id="bannerImage" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link URL</label>
                        <input type="url" class="form-control" id="bannerLink">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Position*</label>
                            <select class="form-control" id="bannerPosition" required>
                                <option value="home">Homepage</option>
                                <option value="listing">Listing Page</option>
                                <option value="category">Category Page</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Priority</label>
                            <input type="number" class="form-control" id="bannerPriority" value="0" min="0" max="100">
                        </div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="bannerActive" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Set Priority Modal -->
<div class="modal fade" id="priorityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Banner Priority</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="priorityForm">
                <input type="hidden" id="priorityBannerId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Priority (0-100)*</label>
                        <input type="number" class="form-control" id="priorityValue" min="0" max="100" required>
                        <small class="text-muted">Higher priority banners appear first</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Set Priority</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Schedule Banner Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="scheduleForm">
                <input type="hidden" id="scheduleBannerId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Start Date*</label>
                        <input type="datetime-local" class="form-control" id="scheduleStart" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date*</label>
                        <input type="datetime-local" class="form-control" id="scheduleEnd" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Feature Ad Modal -->
<div class="modal fade" id="featureAdModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manually Feature Ad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="featureAdForm">
                <input type="hidden" id="featureAdId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Duration (Days)*</label>
                        <input type="number" class="form-control" id="featureDuration" value="30" min="1" max="365" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Feature Ad</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Boost Ad Modal -->
<div class="modal fade" id="boostAdModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Boost Duration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="boostAdForm">
                <input type="hidden" id="boostAdId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Duration (Days)*</label>
                        <input type="number" class="form-control" id="boostDurationValue" value="7" min="1" max="90" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Set Boost</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Check if response is authenticated
function checkAuth(response) {
    if (response.redirected && response.url.includes('/login')) {
        window.location.href = '/admin/login';
        return false;
    }
    if (!response.ok) {
        throw new Error('Request failed with status ' + response.status);
    }
    return true;
}

// Load stats
function loadStats() {
    fetch('/admin/banners/stats', {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(res => {
            if (!checkAuth(res)) return;
            return res.json();
        })
        .then(data => {
            if (!data) return;
            document.getElementById('statTotalBanners').textContent = data.total_banners;
            document.getElementById('statActiveBanners').textContent = data.active_banners;
            document.getElementById('statFeaturedAds').textContent = data.featured_ads;
            document.getElementById('statBoostedAds').textContent = data.boosted_ads;
            document.getElementById('statScheduled').textContent = data.scheduled_banners;
            
            document.getElementById('featuredPrice').value = data.featured_price;
            document.getElementById('boostPrice').value = data.boost_price;
            document.getElementById('boostDuration').value = data.boost_duration;
        })
        .catch(err => {
            console.error('Error loading stats:', err);
        });
}

// Load banners
function loadBanners() {
    fetch('/admin/banners/all', {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(res => {
            if (!checkAuth(res)) return;
            return res.json();
        })
        .then(data => {
            if (!data) return;
            const container = document.getElementById('bannersContainer');
            container.innerHTML = '';
            
            data.banners.forEach(banner => {
                const statusBadge = banner.is_active 
                    ? '<span class="badge bg-success">Active</span>' 
                    : '<span class="badge bg-secondary">Inactive</span>';
                
                const scheduledInfo = banner.scheduled_start 
                    ? `<small class="text-info"><i class="fas fa-calendar"></i> Scheduled</small>` 
                    : '';
                
                container.innerHTML += `
                    <div class="col-md-4 col-lg-3">
                        <div class="banner-card">
                            <img src="/storage/${banner.image}" class="banner-image" alt="${banner.title}">
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">${banner.title}</h6>
                                    ${statusBadge}
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="priority-badge">Priority: ${banner.priority}</span>
                                    ${scheduledInfo}
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog"></i> Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="setPriority(${banner.id}, ${banner.priority})">
                                            <i class="fas fa-sort"></i> Set Priority
                                        </a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="scheduleBanner(${banner.id})">
                                            <i class="fas fa-calendar"></i> Schedule
                                        </a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="toggleBanner(${banner.id})">
                                            <i class="fas fa-toggle-on"></i> ${banner.is_active ? 'Deactivate' : 'Activate'}
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteBanner(${banner.id})">
                                            <i class="fas fa-trash"></i> Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        })
        .catch(err => {
            console.error('Error loading banners:', err);
        });
}

// Load featured ads
function loadFeaturedAds() {
    fetch('/admin/banners/featured/list', {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(res => {
            if (!checkAuth(res)) return;
            return res.json();
        })
        .then(data => {
            const container = document.getElementById('featuredAdsContainer');
            container.innerHTML = '';
            
            data.featured_ads.forEach(ad => {
                const images = Array.isArray(ad.images) && ad.images.length > 0 
                    ? `<img src="/storage/${ad.images[0]}" width="80" class="rounded">` 
                    : '<div class="bg-light p-3">No image</div>';
                
                container.innerHTML += `
                    <tr>
                        <td>${images}</td>
                        <td>${ad.title}</td>
                        <td>${ad.user ? ad.user.name : 'N/A'}</td>
                        <td>${ad.category ? ad.category.name : 'N/A'}</td>
                        <td>${ad.featured_until ? new Date(ad.featured_until).toLocaleDateString() : 'N/A'}</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewAd(${ad.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="manuallyFeature(${ad.id})">
                                <i class="fas fa-clock"></i> Extend
                            </button>
                        </td>
                    </tr>
                `;
            });
        })
        .catch(err => {
            console.error('Error loading featured ads:', err);
        });
}

// Load boosted ads
function loadBoostedAds() {
    fetch('/admin/banners/boosted/list', {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(res => {
            if (!checkAuth(res)) return;
            return res.json();
        })
        .then(data => {
            if (!data) return;
            const container = document.getElementById('boostedAdsContainer');
            container.innerHTML = '';
            
            data.boosted_ads.forEach(ad => {
                const images = Array.isArray(ad.images) && ad.images.length > 0 
                    ? `<img src="/storage/${ad.images[0]}" width="80" class="rounded">` 
                    : '<div class="bg-light p-3">No image</div>';
                
                container.innerHTML += `
                    <tr>
                        <td>${images}</td>
                        <td>${ad.title}</td>
                        <td>${ad.user ? ad.user.name : 'N/A'}</td>
                        <td>${ad.category ? ad.category.name : 'N/A'}</td>
                        <td>${ad.boosted_until ? new Date(ad.boosted_until).toLocaleDateString() : 'N/A'}</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewAd(${ad.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="approveBoost(${ad.id})">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="setBoostDuration(${ad.id})">
                                <i class="fas fa-clock"></i> Duration
                            </button>
                        </td>
                    </tr>
                `;
            });
        })
        .catch(err => {
            console.error('Error loading boosted ads:', err);
        });
}

function showAddBannerModal() {
    document.getElementById('bannerForm').reset();
    new bootstrap.Modal(document.getElementById('bannerModal')).show();
}

document.getElementById('bannerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('title', document.getElementById('bannerTitle').value);
    formData.append('image', document.getElementById('bannerImage').files[0]);
    formData.append('link', document.getElementById('bannerLink').value);
    formData.append('position', document.getElementById('bannerPosition').value);
    formData.append('priority', document.getElementById('bannerPriority').value);
    formData.append('is_active', document.getElementById('bannerActive').checked ? 1 : 0);
    
    fetch('/admin/banners', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: formData
    })
    .then(res => {
        if (!checkAuth(res)) return;
        return res.json();
    })
    .then(data => {
        if (data) {
            alert('Banner added successfully');
            bootstrap.Modal.getInstance(document.getElementById('bannerModal')).hide();
            loadBanners();
            loadStats();
        }
    })
    .catch(err => {
        console.error('Error adding banner:', err);
        alert('Error adding banner. Please try again.');
    });
});

function setPriority(id, currentPriority) {
    document.getElementById('priorityBannerId').value = id;
    document.getElementById('priorityValue').value = currentPriority;
    new bootstrap.Modal(document.getElementById('priorityModal')).show();
}

document.getElementById('priorityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('priorityBannerId').value;
    const priority = document.getElementById('priorityValue').value;
    
    fetch(`/admin/banners/${id}/priority`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({priority})
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        bootstrap.Modal.getInstance(document.getElementById('priorityModal')).hide();
        loadBanners();
    });
});

function scheduleBanner(id) {
    document.getElementById('scheduleBannerId').value = id;
    new bootstrap.Modal(document.getElementById('scheduleModal')).show();
}

document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('scheduleBannerId').value;
    
    fetch(`/admin/banners/${id}/schedule`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            scheduled_start: document.getElementById('scheduleStart').value,
            scheduled_end: document.getElementById('scheduleEnd').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        bootstrap.Modal.getInstance(document.getElementById('scheduleModal')).hide();
        loadBanners();
    });
});

function toggleBanner(id) {
    fetch(`/admin/banners/${id}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        loadBanners();
    });
}

function deleteBanner(id) {
    if (confirm('Delete this banner?')) {
        fetch(`/admin/banners/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadBanners();
            loadStats();
        });
    }
}

function updatePricing() {
    fetch('/admin/banners/pricing', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            featured_price: document.getElementById('featuredPrice').value,
            boost_price: document.getElementById('boostPrice').value,
            boost_duration: document.getElementById('boostDuration').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    });
}

function manuallyFeature(id) {
    document.getElementById('featureAdId').value = id;
    new bootstrap.Modal(document.getElementById('featureAdModal')).show();
}

document.getElementById('featureAdForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('featureAdId').value;
    
    fetch(`/admin/banners/featured/${id}/manual`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            duration: document.getElementById('featureDuration').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        bootstrap.Modal.getInstance(document.getElementById('featureAdModal')).hide();
        loadFeaturedAds();
        loadStats();
    });
});

function approveBoost(id) {
    if (confirm('Approve boost for this ad?')) {
        fetch(`/admin/banners/boosted/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadBoostedAds();
            loadStats();
        });
    }
}

function setBoostDuration(id) {
    document.getElementById('boostAdId').value = id;
    new bootstrap.Modal(document.getElementById('boostAdModal')).show();
}

document.getElementById('boostAdForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('boostAdId').value;
    
    fetch(`/admin/banners/boosted/${id}/duration`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            duration: document.getElementById('boostDurationValue').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        bootstrap.Modal.getInstance(document.getElementById('boostAdModal')).hide();
        loadBoostedAds();
        loadStats();
    });
});

function viewAd(id) {
    window.open(`/admin/listings/${id}`, '_blank');
}

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadBanners();
    loadFeaturedAds();
    loadBoostedAds();
    
    // Reload data when switching tabs
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            if (e.target.getAttribute('href') === '#featuredTab') {
                loadFeaturedAds();
            } else if (e.target.getAttribute('href') === '#boostedTab') {
                loadBoostedAds();
            }
        });
    });
});
</script>
@endsection
