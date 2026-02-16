@extends('admin.layouts.app')

@section('title', 'User Posts & Listings')

@section('styles')
<style>
    body { background: #f5f7fa; }
    
    /* Modern Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    
    .stat-card.active {
        border-color: #4CAF50;
        background: #f1f8f4;
    }
    
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 12px;
    }
    
    .stat-card.all .stat-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .stat-card.pending .stat-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
    .stat-card.approved .stat-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
    .stat-card.rejected .stat-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
    .stat-card.featured .stat-icon { background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%); color: white; }
    
    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }
    
    .stat-label {
        font-size: 14px;
        color: #7f8c8d;
        margin: 4px 0 0 0;
        font-weight: 500;
    }
    
    /* Content Card */
    .content-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .content-header {
        padding: 24px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .content-header h2 {
        font-size: 22px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    /* Search & Filters */
    .filters-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .search-box {
        position: relative;
        min-width: 280px;
    }
    
    .search-box input {
        padding-left: 40px;
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s;
    }
    
    .search-box input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #95a5a6;
    }
    
    .filter-select {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 8px 14px;
        transition: all 0.3s;
    }
    
    .filter-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .btn-filter {
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    /* Listing Cards Grid */
    .listings-grid {
        padding: 24px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }
    
    .listing-card {
        background: white;
        border-radius: 12px;
        border: 2px solid #e9ecef;
        overflow: hidden;
        transition: all 0.3s;
        position: relative;
    }
    
    .listing-card:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    
    .listing-checkbox {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 10;
        width: 24px;
        height: 24px;
        cursor: pointer;
    }
    
    .listing-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f8f9fa;
    }
    
    .listing-content {
        padding: 16px;
    }
    
    .listing-title {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 8px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .listing-info {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #7f8c8d;
    }
    
    .info-item i {
        font-size: 12px;
    }
    
    .listing-price {
        font-size: 20px;
        font-weight: 700;
        color: #27ae60;
        margin: 12px 0;
    }
    
    .listing-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    
    .badge-custom {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-active { background: #d1ecf1; color: #0c5460; }
    .badge-rejected { background: #f8d7da; color: #721c24; }
    .badge-featured { background: #ffd700; color: #000; }
    
    .listing-actions {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 6px;
    }
    
    .action-btn {
        padding: 8px;
        border-radius: 8px;
        border: none;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }
    
    .action-btn:hover {
        transform: scale(1.05);
    }
    
    .btn-approve { background: #d4edda; color: #155724; }
    .btn-approve:hover { background: #c3e6cb; }
    
    .btn-reject { background: #f8d7da; color: #721c24; }
    .btn-reject:hover { background: #f5c6cb; }
    
    .btn-star { background: #fff3cd; color: #856404; }
    .btn-star:hover { background: #ffeeba; }
    
    .btn-delete { background: #f8f9fa; color: #dc3545; }
    .btn-delete:hover { background: #e2e6ea; }
    
    /* Bulk Actions Bar */
    .bulk-actions-bar {
        position: fixed;
        bottom: -100px;
        left: 0;
        right: 0;
        background: #2c3e50;
        color: white;
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 -4px 12px rgba(0,0,0,0.15);
        transition: bottom 0.3s;
        z-index: 1000;
    }
    
    .bulk-actions-bar.show {
        bottom: 0;
    }
    
    .bulk-info {
        font-weight: 600;
    }
    
    .bulk-buttons {
        display: flex;
        gap: 12px;
    }
    
    .bulk-btn {
        padding: 8px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
    }
    
    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.3;
    }
    
    .empty-state h3 {
        color: #7f8c8d;
        margin-bottom: 8px;
    }
    
    /* Loading */
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.9);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    
    .loading-overlay.show {
        display: flex;
    }
    
    .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card all" data-status="">
        <div class="stat-icon"><i class="fas fa-list"></i></div>
        <h3 class="stat-number" id="totalListings">0</h3>
        <p class="stat-label">All Posts</p>
    </div>
    <div class="stat-card pending" data-status="pending">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <h3 class="stat-number" id="pendingListings">0</h3>
        <p class="stat-label">Pending Review</p>
    </div>
    <div class="stat-card approved" data-status="active">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <h3 class="stat-number" id="activeListings">0</h3>
        <p class="stat-label">Approved</p>
    </div>
    <div class="stat-card rejected" data-status="rejected">
        <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
        <h3 class="stat-number" id="rejectedListings">0</h3>
        <p class="stat-label">Rejected</p>
    </div>
    <div class="stat-card featured" data-status="featured">
        <div class="stat-icon"><i class="fas fa-star"></i></div>
        <h3 class="stat-number" id="featuredListings">0</h3>
        <p class="stat-label">Featured</p>
    </div>
</div>

<!-- Content Card -->
<div class="content-card">
    <div class="content-header">
        <h2><i class="fas fa-th-large me-2"></i>User Posts Management</h2>
        
        <div class="filters-row">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Search posts...">
            </div>
            
            <select id="categoryFilter" class="form-select filter-select" style="width: auto;">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            
            <button id="resetBtn" class="btn btn-outline-secondary btn-filter">
                <i class="fas fa-redo me-1"></i>Reset
            </button>
        </div>
    </div>
    
    <!-- Listings Grid -->
    <div class="listings-grid" id="listingsGrid">
        <!-- Listings will be loaded here -->
    </div>
</div>

<!-- Bulk Actions Bar -->
<div class="bulk-actions-bar" id="bulkBar">
    <div class="bulk-info">
        <span id="selectedCount">0</span> post(s) selected
    </div>
    <div class="bulk-buttons">
        <button class="bulk-btn btn-success" onclick="bulkAction('active')">
            <i class="fas fa-check me-1"></i>Approve
        </button>
        <button class="bulk-btn btn-warning" onclick="bulkAction('pending')">
            <i class="fas fa-clock me-1"></i>Mark Pending
        </button>
        <button class="bulk-btn btn-info" onclick="bulkAction('feature')">
            <i class="fas fa-star me-1"></i>Feature
        </button>
        <button class="bulk-btn btn-danger" onclick="bulkAction('delete')">
            <i class="fas fa-trash me-1"></i>Delete
        </button>
        <button class="bulk-btn btn-secondary" onclick="clearSelection()">
            <i class="fas fa-times me-1"></i>Cancel
        </button>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>

@endsection

@section('scripts')
<script>
let allListings = [];
let filteredListings = [];
let currentFilter = {
    status: '',
    category: '',
    search: ''
};

$(document).ready(function() {
    loadStats();
    loadListings();
    
    // Stat Card Click Filter
    $('.stat-card').on('click', function() {
        $('.stat-card').removeClass('active');
        $(this).addClass('active');
        currentFilter.status = $(this).data('status');
        filterListings();
    });
    
    // Search Input
    $('#searchInput').on('input', function() {
        currentFilter.search = $(this).val().toLowerCase();
        filterListings();
    });
    
    // Category Filter
    $('#categoryFilter').on('change', function() {
        currentFilter.category = $(this).val();
        filterListings();
    });
    
    // Reset Button
    $('#resetBtn').on('click', function() {
        currentFilter = { status: '', category: '', search: '' };
        $('#searchInput').val('');
        $('#categoryFilter').val('');
        $('.stat-card').removeClass('active');
        $('.stat-card.all').addClass('active');
        filterListings();
    });
    
    // Update selected count
    $(document).on('change', '.listing-checkbox', function() {
        updateSelectionUI();
    });
});

function loadStats() {
    $.get('{{ route('admin.api.listings.stats') }}', function(data) {
        $('#totalListings').text(data.total || 0);
        $('#pendingListings').text(data.pending || 0);
        $('#activeListings').text(data.active || 0);
        $('#rejectedListings').text(data.rejected || 0);
        $('#featuredListings').text(data.featured || 0);
    });
}

function loadListings() {
    showLoading();
    $.get('{{ route('admin.listings.index') }}?all=1', function(response) {
        allListings = response.data || [];
        filterListings();
        hideLoading();
    }).fail(function() {
        hideLoading();
        $('#listingsGrid').html('<div class="empty-state"><i class="fas fa-exclamation-circle"></i><h3>Failed to load listings</h3></div>');
    });
}

function filterListings() {
    filteredListings = allListings.filter(listing => {
        // Status filter
        if (currentFilter.status && currentFilter.status !== 'featured') {
            if (listing.status !== currentFilter.status) return false;
        }
        if (currentFilter.status === 'featured' && !listing.is_featured) return false;
        
        // Category filter
        if (currentFilter.category && listing.category_id != currentFilter.category) return false;
        
        // Search filter
        if (currentFilter.search) {
            const searchText = (listing.title + ' ' + listing.description).toLowerCase();
            if (!searchText.includes(currentFilter.search)) return false;
        }
        
        return true;
    });
    
    renderListings();
}

function renderListings() {
    const grid = $('#listingsGrid');
    
    if (filteredListings.length === 0) {
        grid.html(`
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fas fa-inbox"></i>
                <h3>No listings found</h3>
                <p>Try adjusting your filters</p>
            </div>
        `);
        return;
    }
    
    const html = filteredListings.map(listing => createListingCard(listing)).join('');
    grid.html(html);
}

function createListingCard(listing) {
    const statusBadge = {
        'pending': '<span class="badge-custom badge-pending">Pending</span>',
        'active': '<span class="badge-custom badge-active">Active</span>',
        'rejected': '<span class="badge-custom badge-rejected">Rejected</span>',
        'sold': '<span class="badge-custom badge-sold">Sold</span>'
    }[listing.status] || '';
    
    const featuredBadge = listing.is_featured ? '<span class="badge-custom badge-featured"><i class="fas fa-star"></i> Featured</span>' : '';
    
    const image = listing.images && listing.images.length > 0 
        ? `/storage/${listing.images[0]}` 
        : 'https://via.placeholder.com/400x300?text=No+Image';
    
    return `
        <div class="listing-card" data-id="${listing.id}">
            <input type="checkbox" class="listing-checkbox" value="${listing.id}">
            <img src="${image}" alt="${listing.title}" class="listing-image" onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
            <div class="listing-content">
                <h4 class="listing-title">${listing.title}</h4>
                <div class="listing-info">
                    <span class="info-item"><i class="fas fa-user"></i>${listing.user ? listing.user.name : 'Unknown'}</span>
                    <span class="info-item"><i class="fas fa-eye"></i>${listing.views || 0} views</span>
                    <span class="info-item"><i class="fas fa-map-marker-alt"></i>${listing.location || 'N/A'}</span>
                </div>
                <div class="listing-price">৳${parseFloat(listing.price || 0).toLocaleString()}</div>
                <div class="listing-badges">
                    ${statusBadge}
                    ${featuredBadge}
                    <span class="badge-custom" style="background: #e9ecef; color: #495057;">${listing.category ? listing.category.name : 'Uncategorized'}</span>
                </div>
                <div class="listing-actions">
                    <button class="action-btn btn-approve" onclick="updateStatus(${listing.id}, 'active')" title="Approve">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="action-btn btn-reject" onclick="updateStatus(${listing.id}, 'rejected')" title="Reject">
                        <i class="fas fa-times"></i>
                    </button>
                    <button class="action-btn btn-star" onclick="toggleFeatured(${listing.id})" title="Toggle Featured">
                        <i class="fas fa-star"></i>
                    </button>
                    <button class="action-btn btn-delete" onclick="deleteListing(${listing.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

function updateStatus(id, status) {
    if (!confirm(`Are you sure you want to ${status === 'active' ? 'approve' : 'reject'} this listing?`)) return;
    
    showLoading();
    $.ajax({
        url: `/admin/listings/${id}/status`,
        type: 'POST',
        data: { 
            _token: '{{ csrf_token() }}',
            status: status
        },
        success: function(response) {
            showNotification(`Listing ${status === 'active' ? 'approved' : 'rejected'} successfully!`, 'success');
            loadStats();
            loadListings();
        },
        error: function() {
            hideLoading();
            showNotification('Failed to update status', 'error');
        }
    });
}

function toggleFeatured(id) {
    showLoading();
    $.ajax({
        url: `/admin/listings/${id}/toggle-featured`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            showNotification(response.is_featured ? 'Marked as featured!' : 'Removed from featured!', 'success');
            loadStats();
            loadListings();
        },
        error: function() {
            hideLoading();
            showNotification('Failed to toggle featured status', 'error');
        }
    });
}

function deleteListing(id) {
    if (!confirm('Are you sure you want to delete this listing? This action cannot be undone.')) return;
    
    showLoading();
    $.ajax({
        url: `/admin/listings/${id}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function() {
            showNotification('Listing deleted successfully!', 'success');
            loadStats();
            loadListings();
        },
        error: function() {
            hideLoading();
            showNotification('Failed to delete listing', 'error');
        }
    });
}

function updateSelectionUI() {
    const count = $('.listing-checkbox:checked').length;
    $('#selectedCount').text(count);
    if (count > 0) {
        $('#bulkBar').addClass('show');
    } else {
        $('#bulkBar').removeClass('show');
    }
}

function bulkAction(action) {
    const ids = $('.listing-checkbox:checked').map(function() { return this.value; }).get();
    
    if (ids.length === 0) {
        alert('Please select at least one listing');
        return;
    }
    
    const confirmMsg = action === 'delete' 
        ? `Delete ${ids.length} listing(s)? This cannot be undone.`
        : `Apply "${action}" to ${ids.length} listing(s)?`;
    
    if (!confirm(confirmMsg)) return;
    
    showLoading();
    $.ajax({
        url: '{{ route('admin.listings.bulk') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            action: action,
            ids: ids
        },
        success: function(response) {
            showNotification(response.message || 'Bulk action completed!', 'success');
            clearSelection();
            loadStats();
            loadListings();
        },
        error: function() {
            hideLoading();
            showNotification('Bulk action failed', 'error');
        }
    });
}

function clearSelection() {
    $('.listing-checkbox').prop('checked', false);
    updateSelectionUI();
}

function showLoading() {
    $('#loadingOverlay').addClass('show');
}

function hideLoading() {
    $('#loadingOverlay').removeClass('show');
}

function showNotification(message, type) {
    const color = type === 'success' ? '#27ae60' : '#e74c3c';
    const toast = $(`
        <div class="alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed top-0 end-0 m-3" 
             style="z-index: 9999; min-width: 300px;">
            ${message}
        </div>
    `).appendTo('body').fadeIn().delay(3000).fadeOut(function() { $(this).remove(); });
}
</script>
@endsection
