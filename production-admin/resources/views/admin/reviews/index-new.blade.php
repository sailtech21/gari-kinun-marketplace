@extends('admin.layouts.app')

@section('title', 'Reviews Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-star text-warning me-2"></i>Reviews Management</h2>
            <p class="text-muted mb-0">Manage and approve customer reviews</p>
        </div>
        <div>
            <button class="btn btn-outline-primary" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card total-card">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3 id="totalCount">0</h3>
                    <p>Total Reviews</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card pending-card" onclick="filterByStatus('pending')" style="cursor: pointer;">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3 id="pendingCount">0</h3>
                    <p>Pending Approval</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card approved-card" onclick="filterByStatus('approved')" style="cursor: pointer;">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 id="approvedCount">0</h3>
                    <p>Approved</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card featured-card" onclick="filterByStatus('featured')" style="cursor: pointer;">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-content">
                    <h3 id="featuredCount">0</h3>
                    <p>Featured</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-search me-1"></i>Search</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by name, location, comment...">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-filter me-1"></i>Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="featured">Featured</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-star me-1"></i>Rating</label>
                    <select class="form-select" id="ratingFilter">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <button class="btn btn-secondary w-100" onclick="clearFilters()">
                        <i class="fas fa-times me-1"></i>Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Grid -->
    <div class="row g-3" id="reviewsContainer">
        <!-- Reviews will be loaded here -->
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center py-5" style="display: none;">
        <i class="fas fa-star fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Reviews Found</h4>
        <p class="text-muted">Reviews from your website will appear here.</p>
    </div>
</div>

<!-- View Review Modal -->
<div class="modal fade" id="viewReviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Review Details</h5>
                <button type="button" class="btn-close" onclick="closeViewModal()"></button>
            </div>
            <div class="modal-body" id="viewReviewContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
/* Stats Cards */
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.stat-card .stat-icon {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 48px;
    opacity: 0.15;
}

.stat-card .stat-content h3 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 4px;
    color: #2c3e50;
}

.stat-card .stat-content p {
    margin: 0;
    color: #7f8c8d;
    font-size: 14px;
    font-weight: 500;
}

.total-card { border-left: 4px solid #3498db; }
.pending-card { border-left: 4px solid #f39c12; }
.approved-card { border-left: 4px solid #27ae60; }
.featured-card { border-left: 4px solid #9b59b6; }

.total-card .stat-icon { color: #3498db; }
.pending-card .stat-icon { color: #f39c12; }
.approved-card .stat-icon { color: #27ae60; }
.featured-card .stat-icon { color: #9b59b6; }

/* Review Card */
.review-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
}

.review-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.review-card.pending {
    border-left: 4px solid #f39c12;
}

.review-card.approved {
    border-left: 4px solid #27ae60;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.review-author {
    flex: 1;
}

.review-name {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
}

.review-location {
    font-size: 13px;
    color: #7f8c8d;
    display: flex;
    align-items: center;
    gap: 4px;
}

.review-rating {
    display: flex;
    gap: 2px;
}

.star {
    color: #ffd700;
    font-size: 16px;
}

.star.empty {
    color: #ddd;
}

.review-comment {
    margin: 12px 0;
    color: #34495e;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.review-meta {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 12px;
    font-size: 12px;
}

.meta-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #7f8c8d;
    background: #f8f9fa;
    padding: 4px 10px;
    border-radius: 12px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-approved { background: #d1e7dd; color: #0a3622; }
.status-featured { background: #e7d4ff; color: #5c1fab; }

.review-actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 6px;
    font-weight: 500;
}

/* Modal */
.modal-header {
    background: linear-gradient(135deg, #ffa751 0%, #ffe259 100%);
    color: white;
}

/* Form Controls */
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 8px 12px;
}

.form-control:focus, .form-select:focus {
    border-color: #ffa751;
    box-shadow: 0 0 0 3px rgba(255, 167, 81, 0.1);
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.review-card {
    animation: fadeIn 0.3s ease;
}

.featured-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}
</style>

<script>
let allReviews = [];
let filteredReviews = [];

// Load reviews on page load
document.addEventListener('DOMContentLoaded', function() {
    loadReviews();
    
    // Search with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 300);
    });
    
    // Filter listeners
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    document.getElementById('ratingFilter').addEventListener('change', applyFilters);
});

function loadReviews() {
    fetch('/admin/reviews/all')
        .then(response => response.json())
        .then(data => {
            allReviews = data.reviews;
            filteredReviews = allReviews;
            updateStats();
            renderReviews();
        })
        .catch(error => {
            console.error('Error loading reviews:', error);
            showError('Failed to load reviews');
        });
}

function updateStats() {
    const total = allReviews.length;
    const pending = allReviews.filter(r => !r.is_approved).length;
    const approved = allReviews.filter(r => r.is_approved).length;
    const featured = allReviews.filter(r => r.is_featured).length;
    
    document.getElementById('totalCount').textContent = total;
    document.getElementById('pendingCount').textContent = pending;
    document.getElementById('approvedCount').textContent = approved;
    document.getElementById('featuredCount').textContent = featured;
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const ratingFilter = document.getElementById('ratingFilter').value;
    
    filteredReviews = allReviews.filter(review => {
        // Search filter
        const searchMatch = !searchTerm || 
            review.name.toLowerCase().includes(searchTerm) ||
            (review.location && review.location.toLowerCase().includes(searchTerm)) ||
            (review.comment && review.comment.toLowerCase().includes(searchTerm));
        
        // Status filter
        let statusMatch = true;
        if (statusFilter === 'pending') {
            statusMatch = !review.is_approved;
        } else if (statusFilter === 'approved') {
            statusMatch = review.is_approved && !review.is_featured;
        } else if (statusFilter === 'featured') {
            statusMatch = review.is_featured;
        }
        
        // Rating filter
        const ratingMatch = !ratingFilter || review.rating == ratingFilter;
        
        return searchMatch && statusMatch && ratingMatch;
    });
    
    renderReviews();
}

function renderReviews() {
    const container = document.getElementById('reviewsContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (filteredReviews.length === 0) {
        container.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    container.innerHTML = filteredReviews.map(review => renderReviewCard(review)).join('');
}

function renderReviewCard(review) {
    const statusClass = review.is_approved ? 'approved' : 'pending';
    const statusLabel = review.is_featured ? 'Featured' : (review.is_approved ? 'Approved' : 'Pending');
    const statusBadgeClass = review.is_featured ? 'status-featured' : (review.is_approved ? 'status-approved' : 'status-pending');
    
    // Generate star rating
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<i class="fas fa-star star ${i <= review.rating ? '' : 'empty'}"></i>`;
    }
    
    const featuredBadge = review.is_featured ? 
        '<div class="featured-badge"><i class="fas fa-trophy"></i> Featured</div>' : '';
    
    return `
        <div class="col-md-6 col-lg-4">
            <div class="review-card ${statusClass}">
                ${featuredBadge}
                <div class="review-header">
                    <div class="review-author">
                        <div class="review-name">${review.name}</div>
                        ${review.location ? `<div class="review-location"><i class="fas fa-map-marker-alt"></i>${review.location}</div>` : ''}
                    </div>
                    <div class="review-rating">${stars}</div>
                </div>
                
                ${review.comment ? `<div class="review-comment">${review.comment}</div>` : ''}
                
                <div class="review-meta">
                    <span class="meta-badge">
                        <i class="fas fa-calendar"></i>
                        ${new Date(review.created_at).toLocaleDateString()}
                    </span>
                    ${review.purchase ? `<span class="meta-badge"><i class="fas fa-shopping-cart"></i>${review.purchase}</span>` : ''}
                    <span class="status-badge ${statusBadgeClass}">${statusLabel}</span>
                </div>
                
                <div class="review-actions">
                    <button class="btn btn-sm btn-info" onclick="viewReview(${review.id})">
                        <i class="fas fa-eye me-1"></i>View
                    </button>
                    ${!review.is_approved ? `
                    <button class="btn btn-sm btn-success" onclick="approveReview(${review.id})">
                        <i class="fas fa-check me-1"></i>Approve
                    </button>
                    ` : ''}
                    <button class="btn btn-sm ${review.is_featured ? 'btn-warning' : 'btn-primary'}" 
                            onclick="toggleFeatured(${review.id})">
                        <i class="fas fa-trophy me-1"></i>
                        ${review.is_featured ? 'Unfeature' : 'Feature'}
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteReview(${review.id})">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    `;
}

function viewReview(id) {
    const review = allReviews.find(r => r.id === id);
    if (!review) return;
    
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<i class="fas fa-star star ${i <= review.rating ? '' : 'empty'}"></i>`;
    }
    
    const statusLabel = review.is_featured ? 'Featured' : (review.is_approved ? 'Approved' : 'Pending');
    const statusBadgeClass = review.is_featured ? 'status-featured' : (review.is_approved ? 'status-approved' : 'status-pending');
    
    const content = `
        <table class="table">
            <tr><th width="200">Name:</th><td>${review.name}</td></tr>
            ${review.location ? `<tr><th>Location:</th><td>${review.location}</td></tr>` : ''}
            <tr><th>Rating:</th><td><div class="review-rating">${stars}</div></td></tr>
            ${review.comment ? `<tr><th>Comment:</th><td>${review.comment}</td></tr>` : ''}
            ${review.purchase ? `<tr><th>Purchase:</th><td>${review.purchase}</td></tr>` : ''}
            <tr><th>Status:</th><td><span class="status-badge ${statusBadgeClass}">${statusLabel}</span></td></tr>
            <tr><th>Created:</th><td>${new Date(review.created_at).toLocaleString()}</td></tr>
        </table>
    `;
    
    document.getElementById('viewReviewContent').innerHTML = content;
    $('#viewReviewModal').modal('show');
}

function closeViewModal() {
    document.activeElement.blur();
    $('#viewReviewModal').modal('hide');
}

function approveReview(id) {
    if (!confirm('Are you sure you want to approve this review?')) {
        return;
    }
    
    fetch(`/admin/reviews/${id}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(() => {
        showSuccess('Review approved successfully');
        loadReviews();
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to approve review');
    });
}

function toggleFeatured(id) {
    fetch(`/admin/reviews/${id}/toggle-featured`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(() => {
        showSuccess('Featured status updated');
        loadReviews();
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to update featured status');
    });
}

function deleteReview(id) {
    if (!confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/reviews/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ _method: 'DELETE' })
    })
    .then(response => response.json())
    .then(() => {
        showSuccess('Review deleted successfully');
        loadReviews();
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to delete review');
    });
}

function filterByStatus(status) {
    document.getElementById('statusFilter').value = status;
    applyFilters();
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('ratingFilter').value = '';
    applyFilters();
}

function showSuccess(message) {
    alert(message);
}

function showError(message) {
    alert('Error: ' + message);
}
</script>
@endsection
