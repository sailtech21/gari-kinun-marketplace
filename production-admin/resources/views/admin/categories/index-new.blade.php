@extends('admin.layouts.app')

@section('title', 'Categories Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-folder text-primary me-2"></i>Categories Management</h2>
            <p class="text-muted mb-0">Manage vehicle categories for your marketplace</p>
        </div>
        <div>
            <button class="btn btn-outline-primary me-2" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
            <button class="btn btn-success" onclick="showAddCategoryModal()">
                <i class="fas fa-plus me-1"></i>Add Category
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card total-card">
                <div class="stat-icon">
                    <i class="fas fa-folder"></i>
                </div>
                <div class="stat-content">
                    <h3 id="totalCount">0</h3>
                    <p>Total Categories</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stat-card active-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 id="activeCount">0</h3>
                    <p>Active Categories</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stat-card listings-card">
                <div class="stat-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-content">
                    <h3 id="listingsCount">0</h3>
                    <p>Total Listings</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-search me-1"></i>Search Categories</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by name...">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-filter me-1"></i>Filter by Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
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

    <!-- Categories Grid -->
    <div class="row g-3" id="categoriesContainer">
        <!-- Categories will be loaded here -->
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center py-5" style="display: none;">
        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Categories Found</h4>
        <p class="text-muted">Start by adding your first category.</p>
        <button class="btn btn-primary mt-3" onclick="showAddCategoryModal()">
            <i class="fas fa-plus me-1"></i>Add Category
        </button>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-folder me-2"></i>Add Category
                </h5>
                <button type="button" class="btn-close" onclick="closeCategoryModal()"></button>
            </div>
            <form id="categoryForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="categoryId">
                    
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" required placeholder="e.g., Cars, Motorbikes">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Icon Class</label>
                        <input type="text" class="form-control" id="categoryIcon" placeholder="e.g., fas fa-car">
                        <small class="text-muted">Use Font Awesome icon classes</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="categoryActive" checked>
                            <label class="form-check-label" for="categoryActive">
                                Active (visible on website)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCategoryModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Category
                    </button>
                </div>
            </form>
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
.listings-card { border-left: 4px solid #9b59b6; }

.total-card .stat-icon { color: #3498db; }
.active-card .stat-icon { color: #27ae60; }
.listings-card .stat-icon { color: #9b59b6; }

/* Category Card */
.category-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 4px solid #3498db;
}

.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.category-card.inactive {
    opacity: 0.6;
    border-left-color: #95a5a6;
}

.category-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.category-icon-box {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 28px;
    flex-shrink: 0;
}

.category-card.inactive .category-icon-box {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
}

.category-info {
    flex: 1;
}

.category-name {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
}

.category-meta {
    display: flex;
    gap: 15px;
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

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: #d1e7dd;
    color: #0a3622;
}

.status-inactive {
    background: #f8d7da;
    color: #58151c;
}

.category-actions {
    display: flex;
    gap: 8px;
    margin-top: 15px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
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

.category-card {
    animation: fadeIn 0.3s ease;
}
</style>

<script>
let allCategories = [];
let filteredCategories = [];
let editingId = null;

// Load categories on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    
    // Search with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 300);
    });
    
    // Filter listeners
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    
    // Form submission
    document.getElementById('categoryForm').addEventListener('submit', handleSubmit);
});

function loadCategories() {
    fetch('/admin/categories/all')
        .then(response => response.json())
        .then(data => {
            allCategories = data.categories;
            filteredCategories = allCategories;
            updateStats();
            renderCategories();
        })
        .catch(error => {
            console.error('Error loading categories:', error);
            showError('Failed to load categories');
        });
}

function updateStats() {
    const total = allCategories.length;
    const active = allCategories.filter(c => c.is_active).length;
    const totalListings = allCategories.reduce((sum, c) => sum + (c.listings_count || 0), 0);
    
    document.getElementById('totalCount').textContent = total;
    document.getElementById('activeCount').textContent = active;
    document.getElementById('listingsCount').textContent = totalListings;
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    
    filteredCategories = allCategories.filter(category => {
        // Search filter
        const searchMatch = !searchTerm || category.name.toLowerCase().includes(searchTerm);
        
        // Status filter
        let statusMatch = true;
        if (statusFilter === 'active') {
            statusMatch = category.is_active === 1;
        } else if (statusFilter === 'inactive') {
            statusMatch = category.is_active === 0;
        }
        
        return searchMatch && statusMatch;
    });
    
    renderCategories();
}

function renderCategories() {
    const container = document.getElementById('categoriesContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (filteredCategories.length === 0) {
        container.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    container.innerHTML = filteredCategories.map(category => renderCategoryCard(category)).join('');
}

function renderCategoryCard(category) {
    const statusClass = category.is_active ? 'active' : 'inactive';
    const statusLabel = category.is_active ? 'Active' : 'Inactive';
    const icon = category.icon || 'fas fa-folder';
    const listingsCount = category.listings_count || 0;
    
    return `
        <div class="col-md-6 col-lg-4">
            <div class="category-card ${statusClass}">
                <div class="category-header">
                    <div class="category-icon-box">
                        <i class="${icon}"></i>
                    </div>
                    <div class="category-info">
                        <div class="category-name">${category.name}</div>
                        <div class="category-meta">
                            <span class="meta-badge">
                                <i class="fas fa-car"></i>
                                <span>${listingsCount} listings</span>
                            </span>
                            <span class="status-badge status-${statusClass}">${statusLabel}</span>
                        </div>
                    </div>
                </div>
                
                <div class="category-actions">
                    <button class="btn btn-sm btn-primary" onclick="editCategory(${category.id})">
                        <i class="fas fa-edit me-1"></i>Edit
                    </button>
                    <button class="btn btn-sm ${category.is_active ? 'btn-warning' : 'btn-success'}" 
                            onclick="toggleStatus(${category.id})">
                        <i class="fas fa-${category.is_active ? 'eye-slash' : 'eye'} me-1"></i>
                        ${category.is_active ? 'Deactivate' : 'Activate'}
                    </button>
                    ${listingsCount === 0 ? `
                        <button class="btn btn-sm btn-danger" onclick="deleteCategory(${category.id})">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

function showAddCategoryModal() {
    editingId = null;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-folder me-2"></i>Add Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryActive').checked = true;
    $('#categoryModal').modal('show');
}

function editCategory(id) {
    const category = allCategories.find(c => c.id === id);
    if (!category) return;
    
    editingId = id;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Category';
    document.getElementById('categoryId').value = category.id;
    document.getElementById('categoryName').value = category.name;
    document.getElementById('categoryIcon').value = category.icon || '';
    document.getElementById('categoryActive').checked = category.is_active === 1;
    
    $('#categoryModal').modal('show');
}

function closeCategoryModal() {
    document.activeElement.blur();
    $('#categoryModal').modal('hide');
}

function handleSubmit(e) {
    e.preventDefault();
    
    const id = document.getElementById('categoryId').value;
    const name = document.getElementById('categoryName').value;
    const icon = document.getElementById('categoryIcon').value;
    const isActive = document.getElementById('categoryActive').checked ? 1 : 0;
    
    const url = id ? `/admin/categories/${id}` : '/admin/categories';
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            icon: icon,
            is_active: isActive,
            type: 'vehicle'
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        showSuccess(id ? 'Category updated successfully' : 'Category created successfully');
        closeCategoryModal();
        loadCategories();
    })
    .catch(error => {
        console.error('Error:', error);
        if (error.errors) {
            const errorMsg = Object.values(error.errors).flat().join(', ');
            showError(errorMsg);
        } else {
            showError(error.message || 'Failed to save category');
        }
    });
}

function toggleStatus(id) {
    fetch(`/admin/categories/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(() => {
        showSuccess('Category status updated');
        loadCategories();
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to update status');
    });
}

function deleteCategory(id) {
    if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/categories/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        showSuccess('Category deleted successfully');
        loadCategories();
    })
    .catch(error => {
        console.error('Error:', error);
        showError(error.message || 'Failed to delete category');
    });
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    applyFilters();
}

function showSuccess(message) {
    // You can use your preferred notification library
    alert(message);
}

function showError(message) {
    // You can use your preferred notification library
    alert('Error: ' + message);
}
</script>
@endsection
