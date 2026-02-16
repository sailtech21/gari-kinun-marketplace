@extends('admin.layouts.app')

@section('title', 'Categories Management')

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
    
    .category-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        cursor: move;
        border-left: 4px solid #667eea;
    }
    
    .category-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .category-card.inactive {
        opacity: 0.6;
        border-left-color: #ccc;
    }
    
    .category-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 1.5rem;
    }
    
    .subcategory-item {
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .drag-handle {
        cursor: move;
        color: #6c757d;
    }
    
    .posting-fee-badge {
        background: #10b981;
        color: white;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">📂 Categories Management</h1>
        <div>
            <button class="btn btn-secondary me-2" onclick="toggleReorderMode()">
                <i class="fas fa-sort"></i> Reorder
            </button>
            <button class="btn btn-primary" onclick="showAddCategoryModal()">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card bg-gradient-primary">
            <div class="icon"><i class="fas fa-folder"></i></div>
            <div class="number">{{ $categories->count() }}</div>
            <div class="label">Total Categories</div>
        </div>
        <div class="stat-card bg-gradient-success">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <div class="number">{{ $categories->where('is_active', true)->count() }}</div>
            <div class="label">Active</div>
        </div>
        <div class="stat-card bg-gradient-warning">
            <div class="icon"><i class="fas fa-sitemap"></i></div>
            <div class="number">{{ $categories->sum(function($cat) { return $cat->children->count(); }) }}</div>
            <div class="label">Subcategories</div>
        </div>
        <div class="stat-card bg-gradient-info">
            <div class="icon"><i class="fas fa-car"></i></div>
            <div class="number">{{ $categories->sum('listings_count') }}</div>
            <div class="label">Total Listings</div>
        </div>
    </div>

    <!-- Reorder Mode Notice -->
    <div id="reorderNotice" class="alert alert-info" style="display: none;">
        <i class="fas fa-info-circle"></i> <strong>Reorder Mode:</strong> Drag and drop categories to reorder. Click "Save Order" when done.
        <button class="btn btn-success btn-sm float-end" onclick="saveOrder()">
            <i class="fas fa-save"></i> Save Order
        </button>
        <button class="btn btn-secondary btn-sm float-end me-2" onclick="cancelReorder()">
            Cancel
        </button>
    </div>

    <!-- Categories Grid -->
    <div class="row g-4" id="categoriesContainer">
        @forelse($categories as $category)
        <div class="col-md-6 col-lg-4 category-item" data-id="{{ $category->id }}" data-order="{{ $category->order }}">
            <div class="category-card {{ !$category->is_active ? 'inactive' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center">
                        <span class="drag-handle me-3" style="display: none;">
                            <i class="fas fa-grip-vertical fa-2x"></i>
                        </span>
                        <div class="category-icon me-3">
                            <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $category->name }}</h5>
                            <small class="text-muted">{{ $category->listings_count }} listings</small>
                            @if($category->posting_fee > 0)
                                <span class="posting-fee-badge d-block mt-1">
                                    Fee: ${{ number_format($category->posting_fee, 2) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="editCategory({{ $category->id }})">
                                <i class="fas fa-edit"></i> Edit
                            </a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="addSubcategory({{ $category->id }})">
                                <i class="fas fa-plus"></i> Add Subcategory
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="setPostingFee({{ $category->id }}, {{ $category->posting_fee }})">
                                <i class="fas fa-dollar-sign"></i> Set Posting Fee
                            </a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="manageFilters({{ $category->id }})">
                                <i class="fas fa-filter"></i> Manage Filters
                            </a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="manageRequiredFields({{ $category->id }})">
                                <i class="fas fa-asterisk"></i> Required Fields
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="toggleCategory({{ $category->id }}, {{ $category->is_active ? 'true' : 'false' }})">
                                <i class="fas fa-toggle-{{ $category->is_active ? 'on' : 'off' }}"></i> {{ $category->is_active ? 'Disable' : 'Enable' }}
                            </a></li>
                            <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteCategory({{ $category->id }})">
                                <i class="fas fa-trash"></i> Delete
                            </a></li>
                        </ul>
                    </div>
                </div>
                
                @if($category->description)
                    <p class="text-muted small mb-3">{{ Str::limit($category->description, 80) }}</p>
                @endif
                
                @if($category->children->count() > 0)
                    <div class="mt-3">
                        <strong class="text-muted small">Subcategories ({{ $category->children->count() }}):</strong>
                        <div class="mt-2">
                            @foreach($category->children as $sub)
                                <div class="subcategory-item">
                                    <span>
                                        <i class="{{ $sub->icon ?? 'fas fa-circle' }} me-2"></i>
                                        {{ $sub->name }}
                                        <small class="text-muted">({{ $sub->listings_count }})</small>
                                    </span>
                                    <div>
                                        <button class="btn btn-sm btn-light" onclick="editCategory({{ $sub->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No categories found</h4>
            <button class="btn btn-primary mt-3" onclick="showAddCategoryModal()">
                <i class="fas fa-plus"></i> Add First Category
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                <input type="hidden" id="categoryId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name*</label>
                        <input type="text" class="form-control" id="categoryName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon (Font Awesome class)</label>
                        <input type="text" class="form-control" id="categoryIcon" placeholder="fas fa-car">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="categoryDescription" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="categoryActive" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Subcategory Modal -->
<div class="modal fade" id="subcategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Subcategory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="subcategoryForm">
                <input type="hidden" id="parentCategoryId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subcategory Name*</label>
                        <input type="text" class="form-control" id="subcategoryName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon</label>
                        <input type="text" class="form-control" id="subcategoryIcon" placeholder="fas fa-circle">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="subcategoryDescription" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Subcategory</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Posting Fee Modal -->
<div class="modal fade" id="postingFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Posting Fee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="postingFeeForm">
                <input type="hidden" id="feeCategoryId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Posting Fee ($)*</label>
                        <input type="number" class="form-control" id="postingFeeAmount" step="0.01" min="0" required>
                        <small class="text-muted">Enter 0 for free posting</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Fee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Filters Modal -->
<div class="modal fade" id="filtersModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Category Filters</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="filtersForm">
                <input type="hidden" id="filterCategoryId">
                <div class="modal-body">
                    <p class="text-muted">Enable filters to show on frontend for this category:</p>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="filterFuelType">
                        <label class="form-check-label">Fuel Type Filter</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="filterTransmission">
                        <label class="form-check-label">Transmission Filter</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="filterBodyType">
                        <label class="form-check-label">Body Type Filter</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="filterYear">
                        <label class="form-check-label">Year Filter</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="filterMileage">
                        <label class="form-check-label">Mileage Filter</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="filterEngineCapacity">
                        <label class="form-check-label">Engine Capacity Filter</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="filterCondition">
                        <label class="form-check-label">Condition Filter</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Required Fields Modal -->
<div class="modal fade" id="requiredFieldsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Required Fields</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="requiredFieldsForm">
                <input type="hidden" id="reqFieldsCategoryId">
                <div class="modal-body">
                    <p class="text-muted">Select fields that are required when posting in this category:</p>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="reqModel" value="model">
                        <label class="form-check-label">Model</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="reqYear" value="year">
                        <label class="form-check-label">Year</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="reqCondition" value="condition">
                        <label class="form-check-label">Condition</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="reqMileage" value="mileage">
                        <label class="form-check-label">Mileage</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="reqPhone" value="phone">
                        <label class="form-check-label">Phone Number</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Required Fields</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let reorderMode = false;
let sortable = null;

function showAddCategoryModal() {
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('modalTitle').textContent = 'Add Category';
    document.getElementById('categoryActive').checked = true;
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('categoryId').value;
    const url = id ? `/admin/categories/${id}` : '/admin/categories';
    const method = id ? 'PUT' : 'POST';
    
    const formData = {
        name: document.getElementById('categoryName').value,
        icon: document.getElementById('categoryIcon').value,
        description: document.getElementById('categoryDescription').value,
        is_active: document.getElementById('categoryActive').checked ? 1 : 0,
    };
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.success) {
            alert(data.message || 'Category saved successfully');
            location.reload();
        } else {
            alert(data.message || 'Error saving category');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error saving category: ' + (err.message || 'Unknown error'));
    });
});

function editCategory(id) {
    fetch(`/admin/categories/${id}/edit`)
    .then(res => res.text())
    .then(html => {
        // Since we're getting HTML, we'll reload the page for now
        // Or you can fetch category data via AJAX
        window.location.href = `/admin/categories/${id}/edit`;
    });
}

function addSubcategory(parentId) {
    document.getElementById('parentCategoryId').value = parentId;
    document.getElementById('subcategoryForm').reset();
    new bootstrap.Modal(document.getElementById('subcategoryModal')).show();
}

document.getElementById('subcategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const parentId = document.getElementById('parentCategoryId').value;
    
    const formData = {
        name: document.getElementById('subcategoryName').value,
        icon: document.getElementById('subcategoryIcon').value,
        description: document.getElementById('subcategoryDescription').value,
    };
    
    fetch(`/admin/categories/${parentId}/add-subcategory`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Error adding subcategory');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error adding subcategory: ' + (err.message || 'Unknown error'));
    });
});

function setPostingFee(id, currentFee) {
    document.getElementById('feeCategoryId').value = id;
    document.getElementById('postingFeeAmount').value = currentFee;
    new bootstrap.Modal(document.getElementById('postingFeeModal')).show();
}

document.getElementById('postingFeeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('feeCategoryId').value;
    const fee = document.getElementById('postingFeeAmount').value;
    
    fetch(`/admin/categories/${id}/posting-fee`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({posting_fee: fee})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        }
    });
});

function manageFilters(id) {
    document.getElementById('filterCategoryId').value = id;
    // Load current filter settings
    fetch(`/admin/categories/${id}/edit`)
    .then(() => {
        new bootstrap.Modal(document.getElementById('filtersModal')).show();
    });
}

document.getElementById('filtersForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('filterCategoryId').value;
    
    const formData = new FormData();
    if (document.getElementById('filterFuelType').checked) formData.append('show_fuel_type', '1');
    if (document.getElementById('filterTransmission').checked) formData.append('show_transmission', '1');
    if (document.getElementById('filterBodyType').checked) formData.append('show_body_type', '1');
    if (document.getElementById('filterYear').checked) formData.append('show_year', '1');
    if (document.getElementById('filterMileage').checked) formData.append('show_mileage', '1');
    if (document.getElementById('filterEngineCapacity').checked) formData.append('show_engine_capacity', '1');
    if (document.getElementById('filterCondition').checked) formData.append('show_condition', '1');
    
    fetch(`/admin/categories/${id}/update-filters`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': csrfToken},
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('filtersModal')).hide();
        }
    });
});

function manageRequiredFields(id) {
    document.getElementById('reqFieldsCategoryId').value = id;
    new bootstrap.Modal(document.getElementById('requiredFieldsModal')).show();
}

document.getElementById('requiredFieldsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('reqFieldsCategoryId').value;
    
    const requiredFields = [];
    document.querySelectorAll('#requiredFieldsModal input[type="checkbox"]:checked').forEach(cb => {
        requiredFields.push(cb.value);
    });
    
    fetch(`/admin/categories/${id}/required-fields`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({required_fields: requiredFields})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('requiredFieldsModal')).hide();
        }
    });
});

function toggleCategory(id, isActive) {
    if (confirm(`${isActive === 'true' ? 'Disable' : 'Enable'} this category?`)) {
        fetch(`/admin/categories/${id}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(() => location.reload());
    }
}

function deleteCategory(id) {
    if (confirm('Delete this category? This action cannot be undone.')) {
        fetch(`/admin/categories/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(() => location.reload());
    }
}

function toggleReorderMode() {
    reorderMode = !reorderMode;
    const notice = document.getElementById('reorderNotice');
    const handles = document.querySelectorAll('.drag-handle');
    
    if (reorderMode) {
        notice.style.display = 'block';
        handles.forEach(h => h.style.display = 'inline-block');
        
        sortable = new Sortable(document.getElementById('categoriesContainer'), {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'bg-light',
        });
    } else {
        notice.style.display = 'none';
        handles.forEach(h => h.style.display = 'none');
        if (sortable) sortable.destroy();
    }
}

function cancelReorder() {
    location.reload();
}

function saveOrder() {
    const items = document.querySelectorAll('.category-item');
    const categories = [];
    
    items.forEach((item, index) => {
        categories.push({
            id: parseInt(item.dataset.id),
            order: index
        });
    });
    
    fetch('/admin/categories/reorder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({categories})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        }
    });
}
</script>
@endsection
