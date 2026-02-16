@extends('admin.layouts.app')

@section('title', 'Advertisements Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-bullhorn text-primary me-2"></i>Advertisements Management</h2>
            <p class="text-muted mb-0">Manage website advertisements and promotions</p>
        </div>
        <div>
            <button class="btn btn-outline-primary me-2" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
            <button class="btn btn-success" onclick="showAddAdvertisementModal()">
                <i class="fas fa-plus me-1"></i>Add Advertisement
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card total-card">
                <div class="stat-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div class="stat-content">
                    <h3 id="totalCount">0</h3>
                    <p>Total Ads</p>
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
            <div class="stat-card expired-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="stat-content">
                    <h3 id="expiredCount">0</h3>
                    <p>Expired</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-search me-1"></i>Search</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by title...">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Position</label>
                    <select class="form-select" id="positionFilter">
                        <option value="">All Positions</option>
                        <option value="home">Home</option>
                        <option value="listing">Listing</option>
                        <option value="category">Category</option>
                        <option value="sidebar">Sidebar</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-tag me-1"></i>Type</label>
                    <select class="form-select" id="typeFilter">
                        <option value="">All Types</option>
                        <option value="banner">Banner</option>
                        <option value="popup">Popup</option>
                        <option value="sidebar">Sidebar</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-filter me-1"></i>Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">&nbsp;</label>
                    <button class="btn btn-secondary w-100" onclick="clearFilters()">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Advertisements Grid -->
    <div class="row g-3" id="advertisementsContainer">
        <!-- Advertisements will be loaded here -->
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center py-5" style="display: none;">
        <i class="fas fa-bullhorn fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Advertisements Found</h4>
        <p class="text-muted">Start by adding your first advertisement.</p>
        <button class="btn btn-primary mt-3" onclick="showAddAdvertisementModal()">
            <i class="fas fa-plus me-1"></i>Add Advertisement
        </button>
    </div>
</div>

<!-- Add/Edit Advertisement Modal -->
<div class="modal fade" id="advertisementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-bullhorn me-2"></i>Add Advertisement
                </h5>
                <button type="button" class="btn-close" onclick="closeAdvertisementModal()"></button>
            </div>
            <form id="advertisementForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="advertisementId">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="advertisementTitle" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Position <span class="text-danger">*</span></label>
                            <select class="form-select" id="advertisementPosition" required>
                                <option value="">Select Position</option>
                                <option value="home">Home Page</option>
                                <option value="listing">Listing Page</option>
                                <option value="category">Category Page</option>
                                <option value="sidebar">Sidebar</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="advertisementDescription" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="advertisementType" required>
                                <option value="">Select Type</option>
                                <option value="banner">Banner</option>
                                <option value="popup">Popup</option>
                                <option value="sidebar">Sidebar</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="advertisementOrder" value="0" min="0">
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Link URL</label>
                            <input type="url" class="form-control" id="advertisementLink" placeholder="https://...">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Button Text</label>
                            <input type="text" class="form-control" id="advertisementButtonText" placeholder="e.g., Learn More">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="advertisementStartDate">
                            <small class="text-muted">Leave empty for immediate start</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" id="advertisementEndDate">
                            <small class="text-muted">Leave empty for no expiration</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Advertisement Image <span class="text-danger" id="imageRequired">*</span></label>
                        <input type="file" class="form-control" id="advertisementImage" accept="image/*">
                        <small class="text-muted">Recommended: 1200x400px, Max 2MB</small>
                        <div id="currentImage" style="display: none;" class="mt-2">
                            <img id="currentImagePreview" src="" class="img-thumbnail" style="max-height: 100px;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="advertisementActive" checked>
                            <label class="form-check-label" for="advertisementActive">
                                <strong>Active</strong> (visible on website)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAdvertisementModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Advertisement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Advertisement Modal -->
<div class="modal fade" id="viewAdvertisementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Advertisement Details</h5>
                <button type="button" class="btn-close" onclick="closeViewModal()"></button>
            </div>
            <div class="modal-body" id="viewAdvertisementContent">
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
.expired-card { border-left: 4px solid #95a5a6; }

.total-card .stat-icon { color: #3498db; }
.active-card .stat-icon { color: #27ae60; }
.inactive-card .stat-icon { color: #e74c3c; }
.expired-card .stat-icon { color: #95a5a6; }

/* Advertisement Card */
.advertisement-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.advertisement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.advertisement-card.inactive {
    opacity: 0.6;
}

.advertisement-card.expired {
    opacity: 0.5;
    border: 2px solid #e74c3c;
}

.advertisement-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
    background: #f8f9fa;
}

.advertisement-image-placeholder {
    width: 100%;
    height: 180px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 48px;
}

.advertisement-content {
    padding: 15px;
}

.advertisement-title {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.advertisement-meta {
    display: flex;
    gap: 8px;
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
.position-sidebar { background: #fff3e0; color: #e65100; }

.type-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.type-banner { background: #e1f5fe; color: #0277bd; }
.type-popup { background: #fce4ec; color: #c2185b; }
.type-sidebar { background: #f1f8e9; color: #558b2f; }

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active { background: #d1e7dd; color: #0a3622; }
.status-inactive { background: #f8d7da; color: #58151c; }
.status-expired { background: #6c757d; color: white; }

.date-badge {
    font-size: 11px;
    color: #666;
    background: #f8f9fa;
    padding: 3px 8px;
    border-radius: 4px;
}

.advertisement-actions {
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

/* Form Controls */
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 8px 12px;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.advertisement-card {
    animation: fadeIn 0.3s ease;
}
</style>

<script>
let allAdvertisements = [];
let filteredAdvertisements = [];
let editingId = null;

// Load advertisements on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAdvertisements();
    
    // Search with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 300);
    });
    
    // Filter listeners
    document.getElementById('positionFilter').addEventListener('change', applyFilters);
    document.getElementById('typeFilter').addEventListener('change', applyFilters);
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    
    // Form submission
    document.getElementById('advertisementForm').addEventListener('submit', handleSubmit);
});

function loadAdvertisements() {
    fetch('/admin/advertisements/all')
        .then(response => response.json())
        .then(data => {
            allAdvertisements = data.advertisements;
            filteredAdvertisements = allAdvertisements;
            updateStats();
            renderAdvertisements();
        })
        .catch(error => {
            console.error('Error loading advertisements:', error);
            showError('Failed to load advertisements');
        });
}

function updateStats() {
    const total = allAdvertisements.length;
    const active = allAdvertisements.filter(a => a.is_active && !isExpired(a)).length;
    const inactive = allAdvertisements.filter(a => !a.is_active && !isExpired(a)).length;
    const expired = allAdvertisements.filter(a => isExpired(a)).length;
    
    document.getElementById('totalCount').textContent = total;
    document.getElementById('activeCount').textContent = active;
    document.getElementById('inactiveCount').textContent = inactive;
    document.getElementById('expiredCount').textContent = expired;
}

function isExpired(ad) {
    if (!ad.end_date) return false;
    const endDate = new Date(ad.end_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return endDate < today;
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const positionFilter = document.getElementById('positionFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    filteredAdvertisements = allAdvertisements.filter(ad => {
        // Search filter
        const searchMatch = !searchTerm || ad.title.toLowerCase().includes(searchTerm);
        
        // Position filter
        const positionMatch = !positionFilter || ad.position === positionFilter;
        
        // Type filter
        const typeMatch = !typeFilter || ad.type === typeFilter;
        
        // Status filter
        let statusMatch = true;
        if (statusFilter === 'active') {
            statusMatch = ad.is_active === 1 && !isExpired(ad);
        } else if (statusFilter === 'inactive') {
            statusMatch = ad.is_active === 0 && !isExpired(ad);
        } else if (statusFilter === 'expired') {
            statusMatch = isExpired(ad);
        }
        
        return searchMatch && positionMatch && typeMatch && statusMatch;
    });
    
    renderAdvertisements();
}

function renderAdvertisements() {
    const container = document.getElementById('advertisementsContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (filteredAdvertisements.length === 0) {
        container.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    container.innerHTML = filteredAdvertisements.map(ad => renderAdvertisementCard(ad)).join('');
}

function renderAdvertisementCard(ad) {
    const expired = isExpired(ad);
    let statusClass = expired ? 'expired' : (ad.is_active ? 'active' : 'inactive');
    let statusLabel = expired ? 'Expired' : (ad.is_active ? 'Active' : 'Inactive');
    const positionClass = `position-${ad.position}`;
    const typeClass = `type-${ad.type}`;
    
    let imageHtml = '';
    if (ad.image) {
        imageHtml = `<img src="/storage/${ad.image}" alt="${ad.title}" class="advertisement-image">`;
    } else {
        imageHtml = `<div class="advertisement-image-placeholder"><i class="fas fa-ad"></i></div>`;
    }
    
    let dateInfo = '';
    if (ad.start_date || ad.end_date) {
        const startStr = ad.start_date ? new Date(ad.start_date).toLocaleDateString() : 'Now';
        const endStr = ad.end_date ? new Date(ad.end_date).toLocaleDateString() : 'No end';
        dateInfo = `<span class="date-badge"><i class="fas fa-calendar"></i> ${startStr} - ${endStr}</span>`;
    }
    
    return `
        <div class="col-md-6 col-lg-4">
            <div class="advertisement-card ${statusClass}">
                ${imageHtml}
                <div class="advertisement-content">
                    <div class="advertisement-title">${ad.title}</div>
                    <div class="advertisement-meta">
                        <span class="position-badge ${positionClass}">${ad.position}</span>
                        <span class="type-badge ${typeClass}">${ad.type}</span>
                        <span class="meta-badge">
                            <i class="fas fa-sort"></i>
                            ${ad.order}
                        </span>
                        <span class="status-badge status-${statusClass}">${statusLabel}</span>
                        ${dateInfo}
                    </div>
                    <div class="advertisement-actions">
                        <button class="btn btn-sm btn-info" onclick="viewAdvertisement(${ad.id})">
                            <i class="fas fa-eye me-1"></i>View
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="editAdvertisement(${ad.id})">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                        ${!expired ? `
                        <button class="btn btn-sm ${ad.is_active ? 'btn-warning' : 'btn-success'}" 
                                onclick="toggleStatus(${ad.id})">
                            <i class="fas fa-${ad.is_active ? 'eye-slash' : 'eye'} me-1"></i>
                            ${ad.is_active ? 'Hide' : 'Show'}
                        </button>
                        ` : ''}
                        <button class="btn btn-sm btn-danger" onclick="deleteAdvertisement(${ad.id})">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function showAddAdvertisementModal() {
    editingId = null;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-bullhorn me-2"></i>Add Advertisement';
    document.getElementById('advertisementForm').reset();
    document.getElementById('advertisementId').value = '';
    document.getElementById('advertisementActive').checked = true;
    document.getElementById('imageRequired').style.display = 'inline';
    document.getElementById('currentImage').style.display = 'none';
    $('#advertisementModal').modal('show');
}

function editAdvertisement(id) {
    const ad = allAdvertisements.find(a => a.id === id);
    if (!ad) return;
    
    editingId = id;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Advertisement';
    document.getElementById('advertisementId').value = ad.id;
    document.getElementById('advertisementTitle').value = ad.title;
    document.getElementById('advertisementDescription').value = ad.description || '';
    document.getElementById('advertisementLink').value = ad.link || '';
    document.getElementById('advertisementButtonText').value = ad.button_text || '';
    document.getElementById('advertisementPosition').value = ad.position;
    document.getElementById('advertisementType').value = ad.type;
    document.getElementById('advertisementOrder').value = ad.order || 0;
    document.getElementById('advertisementStartDate').value = ad.start_date || '';
    document.getElementById('advertisementEndDate').value = ad.end_date || '';
    document.getElementById('advertisementActive').checked = ad.is_active === 1;
    document.getElementById('imageRequired').style.display = 'none';
    
    if (ad.image) {
        document.getElementById('currentImage').style.display = 'block';
        document.getElementById('currentImagePreview').src = `/storage/${ad.image}`;
    }
    
    $('#advertisementModal').modal('show');
}

function viewAdvertisement(id) {
    const ad = allAdvertisements.find(a => a.id === id);
    if (!ad) return;
    
    const imageUrl = ad.image ? `/storage/${ad.image}` : '';
    const expired = isExpired(ad);
    const statusClass = expired ? 'expired' : (ad.is_active ? 'active' : 'inactive');
    const statusLabel = expired ? 'Expired' : (ad.is_active ? 'Active' : 'Inactive');
    
    const content = `
        <div class="text-center mb-3">
            <img src="${imageUrl}" class="img-fluid rounded" style="max-height: 400px;">
        </div>
        <table class="table">
            <tr><th width="200">Title:</th><td>${ad.title}</td></tr>
            ${ad.description ? `<tr><th>Description:</th><td>${ad.description}</td></tr>` : ''}
            <tr><th>Position:</th><td><span class="position-badge position-${ad.position}">${ad.position}</span></td></tr>
            <tr><th>Type:</th><td><span class="type-badge type-${ad.type}">${ad.type}</span></td></tr>
            <tr><th>Display Order:</th><td>${ad.order}</td></tr>
            <tr><th>Status:</th><td><span class="status-badge status-${statusClass}">${statusLabel}</span></td></tr>
            ${ad.link ? `<tr><th>Link:</th><td><a href="${ad.link}" target="_blank">${ad.link}</a></td></tr>` : ''}
            ${ad.button_text ? `<tr><th>Button Text:</th><td>${ad.button_text}</td></tr>` : ''}
            ${ad.start_date ? `<tr><th>Start Date:</th><td>${new Date(ad.start_date).toLocaleDateString()}</td></tr>` : ''}
            ${ad.end_date ? `<tr><th>End Date:</th><td>${new Date(ad.end_date).toLocaleDateString()}</td></tr>` : ''}
            <tr><th>Created:</th><td>${new Date(ad.created_at).toLocaleString()}</td></tr>
        </table>
    `;
    
    document.getElementById('viewAdvertisementContent').innerHTML = content;
    $('#viewAdvertisementModal').modal('show');
}

function closeAdvertisementModal() {
    document.activeElement.blur();
    $('#advertisementModal').modal('hide');
}

function closeViewModal() {
    document.activeElement.blur();
    $('#viewAdvertisementModal').modal('hide');
}

function handleSubmit(e) {
    e.preventDefault();
    
    const id = document.getElementById('advertisementId').value;
    const formData = new FormData();
    
    formData.append('title', document.getElementById('advertisementTitle').value);
    formData.append('description', document.getElementById('advertisementDescription').value);
    formData.append('link', document.getElementById('advertisementLink').value);
    formData.append('button_text', document.getElementById('advertisementButtonText').value);
    formData.append('position', document.getElementById('advertisementPosition').value);
    formData.append('type', document.getElementById('advertisementType').value);
    formData.append('order', document.getElementById('advertisementOrder').value);
    formData.append('start_date', document.getElementById('advertisementStartDate').value);
    formData.append('end_date', document.getElementById('advertisementEndDate').value);
    formData.append('is_active', document.getElementById('advertisementActive').checked ? 1 : 0);
    
    const imageFile = document.getElementById('advertisementImage').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    if (id) {
        formData.append('_method', 'PUT');
    }
    
    const url = id ? `/admin/advertisements/${id}` : '/admin/advertisements';
    
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
                return { success: true };
            }
        });
    })
    .then(data => {
        showSuccess(id ? 'Advertisement updated successfully' : 'Advertisement created successfully');
        closeAdvertisementModal();
        loadAdvertisements();
    })
    .catch(error => {
        console.error('Error:', error);
        if (error.errors) {
            const errorMsg = Object.values(error.errors).flat().join(', ');
            showError(errorMsg);
        } else {
            showError(error.message || 'Failed to save advertisement');
        }
    });
}

function toggleStatus(id) {
    fetch(`/admin/advertisements/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(() => {
        showSuccess('Advertisement status updated');
        loadAdvertisements();
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to update status');
    });
}

function deleteAdvertisement(id) {
    if (!confirm('Are you sure you want to delete this advertisement? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/advertisements/${id}`, {
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
        showSuccess('Advertisement deleted successfully');
        loadAdvertisements();
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to delete advertisement');
    });
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('positionFilter').value = '';
    document.getElementById('typeFilter').value = '';
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
