@extends('admin.layouts.app')

@section('title', 'Banners Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-image text-warning me-2"></i>Banners Management</h2>
            <p class="text-muted mb-0">Manage website banners and advertisements</p>
        </div>
        <div>
            <button class="btn btn-outline-primary me-2" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
            <button class="btn btn-success" onclick="showAddBannerModal()">
                <i class="fas fa-plus me-1"></i>Add Banner
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card total-card">
                <div class="stat-icon">
                    <i class="fas fa-image"></i>
                </div>
                <div class="stat-content">
                    <h3 id="totalCount">0</h3>
                    <p>Total Banners</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card active-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 id="activeCount">0</h3>
                    <p>Active</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card inactive-card">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 id="inactiveCount">0</h3>
                    <p>Inactive</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card views-card">
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-content">
                    <h3 id="impressionsCount">0</h3>
                    <p>Total Impressions</p>
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
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by title...">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Position</label>
                    <select class="form-select" id="positionFilter">
                        <option value="">All Positions</option>
                        <option value="home">Home</option>
                        <option value="listing">Listing</option>
                        <option value="category">Category</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-filter me-1"></i>Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
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

    <!-- Banners Grid -->
    <div class="row g-3" id="bannersContainer">
        <!-- Banners will be loaded here -->
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center py-5" style="display: none;">
        <i class="fas fa-image fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Banners Found</h4>
        <p class="text-muted">Start by adding your first banner.</p>
        <button class="btn btn-primary mt-3" onclick="showAddBannerModal()">
            <i class="fas fa-plus me-1"></i>Add Banner
        </button>
    </div>
</div>

<!-- Add/Edit Banner Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-image me-2"></i>Add Banner
                </h5>
                <button type="button" class="btn-close" onclick="closeBannerModal()"></button>
            </div>
            <form id="bannerForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="bannerId">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="bannerTitle" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Position <span class="text-danger">*</span></label>
                            <select class="form-select" id="bannerPosition" required>
                                <option value="">Select Position</option>
                                <option value="home">Home Page</option>
                                <option value="listing">Listing Page</option>
                                <option value="category">Category Page</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="bannerDescription" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Link URL</label>
                            <input type="url" class="form-control" id="bannerLink" placeholder="https://...">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Button Text</label>
                            <input type="text" class="form-control" id="bannerButtonText" placeholder="e.g., Learn More">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="bannerOrder" value="0" min="0">
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="bannerActive" checked>
                                <label class="form-check-label" for="bannerActive">
                                    Active (visible on website)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Banner Image <span class="text-danger" id="imageRequired">*</span></label>
                        <input type="file" class="form-control" id="bannerImage" accept="image/*">
                        <small class="text-muted">Recommended: 1200x400px, Max 2MB</small>
                        <div id="currentImage" style="display: none;" class="mt-2">
                            <img id="currentImagePreview" src="" class="img-thumbnail" style="max-height: 100px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeBannerModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Banner Modal -->
<div class="modal fade" id="viewBannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Banner Details</h5>
                <button type="button" class="btn-close" onclick="closeViewModal()"></button>
            </div>
            <div class="modal-body" id="viewBannerContent">
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
.active-card { border-left: 4px solid #27ae60; }
.inactive-card { border-left: 4px solid #e74c3c; }
.views-card { border-left: 4px solid #9b59b6; }

.total-card .stat-icon { color: #3498db; }
.active-card .stat-icon { color: #27ae60; }
.inactive-card .stat-icon { color: #e74c3c; }
.views-card .stat-icon { color: #9b59b6; }

/* Banner Card */
.banner-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.banner-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.banner-card.inactive {
    opacity: 0.6;
}

.banner-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
    background: #f8f9fa;
}

.banner-content {
    padding: 15px;
}

.banner-title {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.banner-meta {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 12px;
    font-size: 13px;
}

.meta-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #7f8c8d;
}

.meta-badge i {
    width: 14px;
    text-align: center;
}

.position-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.position-home { background: #e3f2fd; color: #1976d2; }
.position-listing { background: #f3e5f5; color: #7b1fa2; }
.position-category { background: #e8f5e9; color: #388e3c; }

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active { background: #d1e7dd; color: #0a3622; }
.status-inactive { background: #f8d7da; color: #58151c; }

.banner-actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 6px 10px;
    font-size: 12px;
    border-radius: 6px;
    font-weight: 500;
}

/* Modal */
.modal-header {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

/* Form Controls */
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 8px 12px;
}

.form-control:focus, .form-select:focus {
    border-color: #f093fb;
    box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.1);
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.banner-card {
    animation: fadeIn 0.3s ease;
}
</style>

<script>
let allBanners = [];
let filteredBanners = [];
let editingId = null;

// Load banners on page load
document.addEventListener('DOMContentLoaded', function() {
    loadBanners();
    
    // Search with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 300);
    });
    
    // Filter listeners
    document.getElementById('positionFilter').addEventListener('change', applyFilters);
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    
    // Form submission
    document.getElementById('bannerForm').addEventListener('submit', handleSubmit);
});

function loadBanners() {
    fetch('/admin/banners/all')
        .then(response => response.json())
        .then(data => {
            allBanners = data.banners;
            filteredBanners = allBanners;
            updateStats();
            renderBanners();
        })
        .catch(error => {
            console.error('Error loading banners:', error);
            showError('Failed to load banners');
        });
}

function updateStats() {
    const total = allBanners.length;
    const active = allBanners.filter(b => b.is_active).length;
    const inactive = total - active;
    const impressions = allBanners.reduce((sum, b) => sum + (b.impressions || 0), 0);
    
    document.getElementById('totalCount').textContent = total;
    document.getElementById('activeCount').textContent = active;
    document.getElementById('inactiveCount').textContent = inactive;
    document.getElementById('impressionsCount').textContent = impressions;
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const positionFilter = document.getElementById('positionFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    filteredBanners = allBanners.filter(banner => {
        // Search filter
        const searchMatch = !searchTerm || banner.title.toLowerCase().includes(searchTerm);
        
        // Position filter
        const positionMatch = !positionFilter || banner.position === positionFilter;
        
        // Status filter
        let statusMatch = true;
        if (statusFilter === 'active') {
            statusMatch = banner.is_active === 1;
        } else if (statusFilter === 'inactive') {
            statusMatch = banner.is_active === 0;
        }
        
        return searchMatch && positionMatch && statusMatch;
    });
    
    renderBanners();
}

function renderBanners() {
    const container = document.getElementById('bannersContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (filteredBanners.length === 0) {
        container.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    container.innerHTML = filteredBanners.map(banner => renderBannerCard(banner)).join('');
}

function renderBannerCard(banner) {
    const statusClass = banner.is_active ? 'active' : 'inactive';
    const statusLabel = banner.is_active ? 'Active' : 'Inactive';
    const positionClass = `position-${banner.position}`;
    const imageUrl = banner.image ? `/storage/${banner.image}` : '/placeholder-banner.jpg';
    const impressions = banner.impressions || 0;
    const clicks = banner.clicks || 0;
    
    return `
        <div class="col-md-6 col-lg-4">
            <div class="banner-card ${statusClass}">
                <img src="${imageUrl}" alt="${banner.title}" class="banner-image" onerror="this.src='/placeholder-banner.jpg'">
                <div class="banner-content">
                    <div class="banner-title">${banner.title}</div>
                    <div class="banner-meta">
                        <span class="position-badge ${positionClass}">${banner.position}</span>
                        <span class="meta-badge">
                            <i class="fas fa-sort"></i>
                            Order: ${banner.order}
                        </span>
                        <span class="meta-badge">
                            <i class="fas fa-eye"></i>
                            ${impressions}
                        </span>
                        ${clicks > 0 ? `<span class="meta-badge"><i class="fas fa-mouse-pointer"></i>${clicks}</span>` : ''}
                        <span class="status-badge status-${statusClass}">${statusLabel}</span>
                    </div>
                    <div class="banner-actions">
                        <button class="btn btn-sm btn-info" onclick="viewBanner(${banner.id})">
                            <i class="fas fa-eye me-1"></i>View
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="editBanner(${banner.id})">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                        <button class="btn btn-sm ${banner.is_active ? 'btn-warning' : 'btn-success'}" 
                                onclick="toggleStatus(${banner.id})">
                            <i class="fas fa-${banner.is_active ? 'eye-slash' : 'eye'} me-1"></i>
                            ${banner.is_active ? 'Hide' : 'Show'}
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteBanner(${banner.id})">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function showAddBannerModal() {
    editingId = null;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-image me-2"></i>Add Banner';
    document.getElementById('bannerForm').reset();
    document.getElementById('bannerId').value = '';
    document.getElementById('bannerActive').checked = true;
    document.getElementById('imageRequired').style.display = 'inline';
    document.getElementById('currentImage').style.display = 'none';
    $('#bannerModal').modal('show');
}

function editBanner(id) {
    const banner = allBanners.find(b => b.id === id);
    if (!banner) return;
    
    editingId = id;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Banner';
    document.getElementById('bannerId').value = banner.id;
    document.getElementById('bannerTitle').value = banner.title;
    document.getElementById('bannerDescription').value = banner.description || '';
    document.getElementById('bannerLink').value = banner.link || '';
    document.getElementById('bannerButtonText').value = banner.button_text || '';
    document.getElementById('bannerPosition').value = banner.position;
    document.getElementById('bannerOrder').value = banner.order || 0;
    document.getElementById('bannerActive').checked = banner.is_active === 1;
    document.getElementById('imageRequired').style.display = 'none';
    
    if (banner.image) {
        document.getElementById('currentImage').style.display = 'block';
        document.getElementById('currentImagePreview').src = `/storage/${banner.image}`;
    }
    
    $('#bannerModal').modal('show');
}

function viewBanner(id) {
    const banner = allBanners.find(b => b.id === id);
    if (!banner) return;
    
    const imageUrl = banner.image ? `/storage/${banner.image}` : '';
    const content = `
        <div class="text-center mb-3">
            <img src="${imageUrl}" class="img-fluid rounded" style="max-height: 400px;">
        </div>
        <table class="table">
            <tr><th width="200">Title:</th><td>${banner.title}</td></tr>
            ${banner.description ? `<tr><th>Description:</th><td>${banner.description}</td></tr>` : ''}
            <tr><th>Position:</th><td><span class="position-badge position-${banner.position}">${banner.position}</span></td></tr>
            <tr><th>Display Order:</th><td>${banner.order}</td></tr>
            <tr><th>Status:</th><td><span class="status-badge status-${banner.is_active ? 'active' : 'inactive'}">${banner.is_active ? 'Active' : 'Inactive'}</span></td></tr>
            ${banner.link ? `<tr><th>Link:</th><td><a href="${banner.link}" target="_blank">${banner.link}</a></td></tr>` : ''}
            ${banner.button_text ? `<tr><th>Button Text:</th><td>${banner.button_text}</td></tr>` : ''}
            <tr><th>Impressions:</th><td>${banner.impressions || 0}</td></tr>
            <tr><th>Clicks:</th><td>${banner.clicks || 0}</td></tr>
            <tr><th>Created:</th><td>${new Date(banner.created_at).toLocaleString()}</td></tr>
        </table>
    `;
    
    document.getElementById('viewBannerContent').innerHTML = content;
    $('#viewBannerModal').modal('show');
}

function closeBannerModal() {
    document.activeElement.blur();
    $('#bannerModal').modal('hide');
}

function closeViewModal() {
    document.activeElement.blur();
    $('#viewBannerModal').modal('hide');
}

function handleSubmit(e) {
    e.preventDefault();
    
    const id = document.getElementById('bannerId').value;
    const formData = new FormData();
    
    formData.append('title', document.getElementById('bannerTitle').value);
    formData.append('description', document.getElementById('bannerDescription').value);
    formData.append('link', document.getElementById('bannerLink').value);
    formData.append('button_text', document.getElementById('bannerButtonText').value);
    formData.append('position', document.getElementById('bannerPosition').value);
    formData.append('order', document.getElementById('bannerOrder').value);
    formData.append('is_active', document.getElementById('bannerActive').checked ? 1 : 0);
    
    const imageFile = document.getElementById('bannerImage').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    if (id) {
        formData.append('_method', 'PUT');
    }
    
    const url = id ? `/admin/banners/${id}` : '/admin/banners';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        return response.text().then(text => {
            if (!response.ok) {
                try {
                    const json = JSON.parse(text);
                    throw json;
                } catch (e) {
                    throw new Error('Server error: ' + response.status);
                }
            }
            try {
                return JSON.parse(text);
            } catch (e) {
                // HTML response means redirect/success
                return { success: true };
            }
        });
    })
    .then(data => {
        showSuccess(id ? 'Banner updated successfully' : 'Banner created successfully');
        closeBannerModal();
        loadBanners();
    })
    .catch(error => {
        console.error('Error:', error);
        if (error.errors) {
            const errorMsg = Object.values(error.errors).flat().join(', ');
            showError(errorMsg);
        } else {
            showError(error.message || 'Failed to save banner');
        }
    });
}

function toggleStatus(id) {
    fetch(`/admin/banners/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(() => {
        showSuccess('Banner status updated');
        loadBanners();
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to update status');
    });
}

function deleteBanner(id) {
    if (!confirm('Are you sure you want to delete this banner? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/banners/${id}`, {
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
        showSuccess('Banner deleted successfully');
        loadBanners();
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to delete banner');
    });
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('positionFilter').value = '';
    document.getElementById('statusFilter').value = '';
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
