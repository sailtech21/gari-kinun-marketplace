@extends('admin.layouts.app')

@section('title', 'Ads Management')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    
    .stat-card.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-card.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .stat-card.bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-card.bg-gradient-danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .stat-card.bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-card.bg-gradient-secondary { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
    .stat-card.bg-gradient-cyan { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
    .stat-card.bg-gradient-dark { background: linear-gradient(135deg, #434343 0%, #000000 100%); }
    
    .stat-card .icon {
        font-size: 1.8rem;
        opacity: 0.5;
        margin-bottom: 8px;
    }
    
    .stat-card .number {
        font-size: 2rem;
        font-weight: 700;
        margin: 5px 0;
    }
    
    .stat-card .label {
        font-size: 0.85rem;
        opacity: 0.9;
    }
    
    .filters-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .table-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .ad-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
    }
    
    .ad-title {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 3px;
    }
    
    .ad-price {
        font-weight: 700;
        color: #10b981;
        font-size: 1.1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">🚗 Ads Management</h1>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card bg-gradient-primary" onclick="filterByStatus('all')">
            <div class="icon"><i class="fas fa-ad"></i></div>
            <div class="number">{{ $stats['total'] }}</div>
            <div class="label">Total Ads</div>
        </div>
        <div class="stat-card bg-gradient-success" onclick="filterByStatus('active')">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <div class="number">{{ $stats['active'] }}</div>
            <div class="label">Active</div>
        </div>
        <div class="stat-card bg-gradient-warning" onclick="filterByStatus('pending')">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <div class="number">{{ $stats['pending'] }}</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat-card bg-gradient-danger" onclick="filterByStatus('rejected')">
            <div class="icon"><i class="fas fa-times-circle"></i></div>
            <div class="number">{{ $stats['rejected'] }}</div>
            <div class="label">Rejected</div>
        </div>
        <div class="stat-card bg-gradient-info" onclick="filterBy('expired', 'yes')">
            <div class="icon"><i class="fas fa-hourglass-end"></i></div>
            <div class="number">{{ $stats['expired'] }}</div>
            <div class="label">Expired</div>
        </div>
        <div class="stat-card bg-gradient-secondary" onclick="filterBy('featured', 'yes')">
            <div class="icon"><i class="fas fa-star"></i></div>
            <div class="number">{{ $stats['featured'] }}</div>
            <div class="label">Featured</div>
        </div>
        <div class="stat-card bg-gradient-cyan" onclick="filterBy('boosted', 'yes')">
            <div class="icon"><i class="fas fa-rocket"></i></div>
            <div class="number">{{ $stats['boosted'] }}</div>
            <div class="label">Boosted</div>
        </div>
        <div class="stat-card bg-gradient-dark" onclick="filterBy('hidden', 'yes')">
            <div class="icon"><i class="fas fa-eye-slash"></i></div>
            <div class="number">{{ $stats['hidden'] }}</div>
            <div class="label">Hidden</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <form id="filterForm" class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search ads..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="category_id" id="categoryFilter">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="location" placeholder="Location" value="{{ request('location') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>
        </form>
    </div>

    <!-- Ads Table -->
    <div class="table-wrapper">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 80px;">Image</th>
                        <th>Ad Details</th>
                        <th>Category</th>
                        <th>User/Dealer</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Posted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listings as $listing)
                    <tr>
                        <td>
                            @if($listing->images && is_array($listing->images) && count($listing->images) > 0)
                                <img src="{{ asset($listing->images[0]) }}" alt="Ad" class="ad-image">
                            @else
                                <div class="ad-image bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="ad-title">{{ Str::limit($listing->title, 40) }}</div>
                            <div class="ad-price">${{ number_format($listing->price, 2) }}</div>
                            <div class="mt-1">
                                @if($listing->is_featured)
                                    <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> Featured</span>
                                @endif
                                @if($listing->is_boosted)
                                    <span class="badge bg-info"><i class="fas fa-rocket"></i> Boosted</span>
                                @endif
                                @if($listing->is_hidden)
                                    <span class="badge bg-dark"><i class="fas fa-eye-slash"></i> Hidden</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <small>{{ $listing->category->name ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <div>
                                @if($listing->dealer)
                                    <span class="badge bg-primary">Dealer</span>
                                    <small class="d-block">{{ $listing->dealer->name }}</small>
                                @else
                                    <span class="badge bg-secondary">User</span>
                                    <small class="d-block">{{ $listing->user->name ?? 'N/A' }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <small>{{ Str::limit($listing->location, 20) }}</small>
                        </td>
                        <td>
                            @if($listing->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($listing->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($listing->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-info">{{ ucfirst($listing->status) }}</span>
                            @endif
                            @if($listing->expires_at && $listing->expires_at < now())
                                <span class="badge bg-dark mt-1 d-block">Expired</span>
                            @endif
                        </td>
                        <td>
                            <i class="fas fa-eye text-muted"></i> {{ $listing->views ?? 0 }}
                        </td>
                        <td>
                            <small>{{ $listing->created_at->format('M d, Y') }}</small>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewAd({{ $listing->id }})">
                                        <i class="fas fa-eye"></i> View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="editAd({{ $listing->id }})">
                                        <i class="fas fa-edit"></i> Edit Ad
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    @if($listing->status == 'pending')
                                        <li><a class="dropdown-item text-success" href="javascript:void(0)" onclick="approveAd({{ $listing->id }})">
                                            <i class="fas fa-check"></i> Approve
                                        </a></li>
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="rejectAd({{ $listing->id }})">
                                            <i class="fas fa-times"></i> Reject
                                        </a></li>
                                    @endif
                                    
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="toggleFeatured({{ $listing->id }}, {{ $listing->is_featured ? 'true' : 'false' }})">
                                        <i class="fas fa-star"></i> {{ $listing->is_featured ? 'Unfeature' : 'Feature' }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="toggleBoosted({{ $listing->id }}, {{ $listing->is_boosted ? 'true' : 'false' }})">
                                        <i class="fas fa-rocket"></i> {{ $listing->is_boosted ? 'Remove Boost' : 'Mark Boosted' }}
                                    </a></li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="extendExpiry({{ $listing->id }})">
                                        <i class="fas fa-calendar-plus"></i> Extend Expiry
                                    </a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeCategory({{ $listing->id }})">
                                        <i class="fas fa-exchange-alt"></i> Change Category
                                    </a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="toggleHidden({{ $listing->id }}, {{ $listing->is_hidden ? 'true' : 'false' }})">
                                        <i class="fas fa-eye-slash"></i> {{ $listing->is_hidden ? 'Unhide' : 'Hide Ad' }}
                                    </a></li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewReports({{ $listing->id }})">
                                        <i class="fas fa-flag"></i> View Reports
                                    </a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewAnalytics({{ $listing->id }})">
                                        <i class="fas fa-chart-line"></i> View Analytics
                                    </a></li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteAd({{ $listing->id }})">
                                        <i class="fas fa-trash"></i> Delete
                                    </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-ad fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No ads found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($listings->hasPages())
        <div class="p-3 border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $listings->firstItem() }} to {{ $listings->lastItem() }} of {{ $listings->total() }} ads
                </div>
                <div>
                    {{ $listings->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- View Ad Modal -->
<div class="modal fade" id="viewAdModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ad Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="adDetails">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Ad Modal -->
<div class="modal fade" id="editAdModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Ad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAdForm">
                <input type="hidden" id="editAdId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" id="editTitle" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" id="editPrice" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="editCategoryId" required>
                                <!-- Options loaded dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" id="editLocation" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="editPhone">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" rows="4" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Ad Modal -->
<div class="modal fade" id="rejectAdModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Ad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectAdForm">
                <input type="hidden" id="rejectAdId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="rejectionReason" rows="4" required placeholder="Provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Ad</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Category Modal -->
<div class="modal fade" id="changeCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="changeCategoryForm">
                <input type="hidden" id="changeCatAdId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select New Category</label>
                        <select class="form-select" id="newCategoryId" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Live search
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

document.getElementById('categoryFilter').addEventListener('change', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);

function applyFilters() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    window.location.href = '{{ route("admin.listings.index") }}?' + params.toString();
}

function filterByStatus(status) {
    document.getElementById('statusFilter').value = status === 'all' ? '' : status;
    applyFilters();
}

function filterBy(field, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(field, value);
    window.location.href = url.toString();
}

function clearFilters() {
    window.location.href = '{{ route("admin.listings.index") }}';
}

function viewAd(id) {
    fetch(`/admin/listings/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to load ad details');
        return res.json();
    })
    .then(data => {
        if (data.success) {
            const ad = data.listing;
            document.getElementById('adDetails').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Title:</strong> ${ad.title}</p>
                        <p><strong>Price:</strong> $${ad.price}</p>
                        <p><strong>Category:</strong> ${ad.category?.name || 'N/A'}</p>
                        <p><strong>Location:</strong> ${ad.location}</p>
                        <p><strong>Phone:</strong> ${ad.phone || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span class="badge bg-${ad.status === 'active' ? 'success' : ad.status === 'pending' ? 'warning' : 'danger'}">${ad.status}</span></p>
                        <p><strong>Views:</strong> ${ad.views || 0}</p>
                        <p><strong>Featured:</strong> ${ad.is_featured ? '✓ Yes' : '✗ No'}</p>
                        <p><strong>Boosted:</strong> ${ad.is_boosted ? '✓ Yes' : '✗ No'}</p>
                        <p><strong>Hidden:</strong> ${ad.is_hidden ? '✓ Yes' : '✗ No'}</p>
                    </div>
                    <div class="col-md-12 mt-3">
                        <p><strong>Description:</strong></p>
                        <p>${ad.description || 'N/A'}</p>
                    </div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('viewAdModal')).show();
        }
    })
    .catch(err => {
        console.error(err);
        if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
            alert('Network error: Please check your internet connection and try again.');
        } else {
            alert('Error loading ad details: ' + err.message);
        }
    });
}

function editAd(id) {
    fetch(`/admin/listings/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to load ad for editing');
        return res.json();
    })
    .then(data => {
        if (data.success) {
            const ad = data.listing;
            const categories = data.categories;
            
            document.getElementById('editAdId').value = ad.id;
            document.getElementById('editTitle').value = ad.title;
            document.getElementById('editPrice').value = ad.price;
            document.getElementById('editLocation').value = ad.location;
            document.getElementById('editPhone').value = ad.phone || '';
            document.getElementById('editDescription').value = ad.description;
            
            let catOptions = '';
            categories.forEach(cat => {
                catOptions += `<option value="${cat.id}" ${cat.id == ad.category_id ? 'selected' : ''}>${cat.name}</option>`;
            });
            document.getElementById('editCategoryId').innerHTML = catOptions;
            
            new bootstrap.Modal(document.getElementById('editAdModal')).show();
        }
    })
    .catch(err => {
        console.error(err);
        if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
            alert('Network error: Please check your internet connection and try again.');
        } else {
            alert('Error loading ad for editing: ' + err.message);
        }
    });
}

document.getElementById('editAdForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('editAdId').value;
    const formData = {
        title: document.getElementById('editTitle').value,
        price: document.getElementById('editPrice').value,
        category_id: document.getElementById('editCategoryId').value,
        location: document.getElementById('editLocation').value,
        phone: document.getElementById('editPhone').value,
        description: document.getElementById('editDescription').value,
    };
    
    fetch(`/admin/listings/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to update ad');
        return res.json();
    })
    .then(data => {
        alert(data.message || 'Ad updated successfully');
        if (data.success) {
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
            alert('Network error: Please check your internet connection and try again.');
        } else {
            alert('Error updating ad: ' + err.message);
        }
    });
});

function approveAd(id) {
    if (confirm('Approve this ad?')) {
        fetch(`/admin/listings/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to approve ad');
            return res.json();
        })
        .then(data => {
            alert(data.message || 'Ad approved');
            if (data.success) {
                location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
                alert('Network error: Please check your internet connection and try again.');
            } else {
                alert('Error approving ad: ' + err.message);
            }
        });
    }
}

function rejectAd(id) {
    document.getElementById('rejectAdId').value = id;
    new bootstrap.Modal(document.getElementById('rejectAdModal')).show();
}

document.getElementById('rejectAdForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('rejectAdId').value;
    const reason = document.getElementById('rejectionReason').value;
    
    fetch(`/admin/listings/${id}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({reason})
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to reject ad');
        return res.json();
    })
    .then(data => {
        alert(data.message || 'Ad rejected');
        if (data.success) {
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
            alert('Network error: Please check your internet connection and try again.');
        } else {
            alert('Error rejecting ad: ' + err.message);
        }
    });
});

function toggleFeatured(id, isFeatured) {
    const action = isFeatured === 'true' ? 'remove from featured' : 'mark as featured';
    if (confirm(`${action}?`)) {
        fetch(`/admin/listings/${id}/toggle-featured`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to toggle featured status');
            return res.json();
        })
        .then(data => {
            alert(data.message || 'Featured status updated');
            if (data.success) {
                location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
                alert('Network error: Please check your internet connection and try again.');
            } else {
                alert('Error updating featured status: ' + err.message);
            }
        });
    }
}

function toggleBoosted(id, isBoosted) {
    const action = isBoosted === 'true' ? 'remove boost' : 'mark as boosted';
    if (confirm(`${action}?`)) {
        fetch(`/admin/listings/${id}/toggle-boosted`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to toggle boosted status');
            return res.json();
        })
        .then(data => {
            alert(data.message || 'Boosted status updated');
            if (data.success) {
                location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
                alert('Network error: Please check your internet connection and try again.');
            } else {
                alert('Error updating boosted status: ' + err.message);
            }
        });
    }
}

function extendExpiry(id) {
    const days = prompt('Extend expiry by how many days?', '30');
    if (days !== null && parseInt(days) > 0) {
        fetch(`/admin/listings/${id}/extend-expiry`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({days: parseInt(days)})
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to extend expiry');
            return res.json();
        })
        .then(data => {
            alert(data.message || 'Expiry extended');
            if (data.success) {
                location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
                alert('Network error: Please check your internet connection and try again.');
            } else {
                alert('Error extending expiry: ' + err.message);
            }
        });
    }
}

function changeCategory(id) {
    document.getElementById('changeCatAdId').value = id;
    new bootstrap.Modal(document.getElementById('changeCategoryModal')).show();
}

document.getElementById('changeCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('changeCatAdId').value;
    const categoryId = document.getElementById('newCategoryId').value;
    
    fetch(`/admin/listings/${id}/change-category`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({category_id: categoryId})
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to change category');
        return res.json();
    })
    .then(data => {
        alert(data.message || 'Category changed');
        if (data.success) {
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
            alert('Network error: Please check your internet connection and try again.');
        } else {
            alert('Error changing category: ' + err.message);
        }
    });
});

function toggleHidden(id, isHidden) {
    const action = isHidden === 'true' ? 'unhide' : 'hide';
    if (confirm(`${action} this ad?`)) {
        fetch(`/admin/listings/${id}/toggle-hidden`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to toggle hidden status');
            return res.json();
        })
        .then(data => {
            alert(data.message || 'Hidden status updated');
            if (data.success) {
                location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
                alert('Network error: Please check your internet connection and try again.');
            } else {
                alert('Error updating hidden status: ' + err.message);
            }
        });
    }
}

function viewReports(id) {
    fetch(`/admin/listings/${id}/reports`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to load reports');
        return res.json();
    })
    .then(data => {
        if (data.success) {
            const reports = data.reports;
            if (reports.length === 0) {
                alert('No reports found for this ad.');
            } else {
                window.location.href = `/admin/reports?listing_id=${id}`;
            }
        }
    })
    .catch(err => {
        console.error(err);
        if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
            alert('Network error: Please check your internet connection and try again.');
        } else {
            alert('Error loading reports: ' + err.message);
        }
    });
}

function viewAnalytics(id) {
    fetch(`/admin/listings/${id}/analytics`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to load analytics');
        return res.json();
    })
    .then(data => {
        if (data.success) {
            const a = data.analytics;
            alert(`Ad Analytics:\n\nViews: ${a.views}\nReports: ${a.reports_count}\nDays Active: ${a.days_active}\nFeatured: ${a.is_featured ? 'Yes' : 'No'}\nBoosted: ${a.is_boosted ? 'Yes' : 'No'}\nStatus: ${a.status}\nCreated: ${a.created_at}`);
        }
    })
    .catch(err => {
        console.error(err);
        if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
            alert('Network error: Please check your internet connection and try again.');
        } else {
            alert('Error loading analytics: ' + err.message);
        }
    });
}

function deleteAd(id) {
    if (confirm('Delete this ad? This action cannot be undone.')) {
        fetch(`/admin/listings/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to delete ad');
            return res.json();
        })
        .then(data => {
            alert(data.message || 'Ad deleted');
            if (data.success) {
                location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
                alert('Network error: Please check your internet connection and try again.');
            } else {
                alert('Error deleting ad: ' + err.message);
            }
        });
    }
}
</script>
@endsection
