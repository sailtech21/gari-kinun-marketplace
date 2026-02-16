@extends('admin.layouts.app')

@section('title', 'Locations Management')

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
    .stat-card.bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-card.bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    
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
    
    .division-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        margin-bottom: 20px;
        cursor: move;
        border-left: 5px solid #667eea;
    }
    
    .division-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .division-card.inactive {
        opacity: 0.6;
        border-left-color: #ccc;
    }
    
    .district-item {
        background: #f8f9fa;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
        cursor: move;
    }
    
    .district-item:hover {
        background: #e9ecef;
    }
    
    .drag-handle {
        cursor: move;
        color: #6c757d;
        margin-right: 10px;
    }
    
    .division-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .division-icon {
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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">📍 Locations Management</h1>
        <div>
            <button class="btn btn-secondary me-2" onclick="toggleReorderMode()">
                <i class="fas fa-sort"></i> Reorder
            </button>
            <button class="btn btn-primary" onclick="showAddDivisionModal()">
                <i class="fas fa-plus"></i> Add Division
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card bg-gradient-primary">
            <div class="icon"><i class="fas fa-map-marked-alt"></i></div>
            <div class="number">{{ $locations->count() }}</div>
            <div class="label">Divisions</div>
        </div>
        <div class="stat-card bg-gradient-success">
            <div class="icon"><i class="fas fa-map-pin"></i></div>
            <div class="number">{{ $locations->sum(function($loc) { return $loc->districts->count(); }) }}</div>
            <div class="label">Districts</div>
        </div>
        <div class="stat-card bg-gradient-info">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <div class="number">{{ $locations->where('is_active', true)->count() }}</div>
            <div class="label">Active Divisions</div>
        </div>
        <div class="stat-card bg-gradient-warning">
            <div class="icon"><i class="fas fa-map"></i></div>
            <div class="number">{{ $locations->count() + $locations->sum(function($loc) { return $loc->districts->count(); }) }}</div>
            <div class="label">Total Locations</div>
        </div>
    </div>

    <!-- Reorder Mode Notice -->
    <div id="reorderNotice" class="alert alert-info" style="display: none;">
        <i class="fas fa-info-circle"></i> <strong>Reorder Mode:</strong> Drag and drop divisions to reorder. Click "Save Order" when done.
        <button class="btn btn-success btn-sm float-end" onclick="saveOrder()">
            <i class="fas fa-save"></i> Save Order
        </button>
        <button class="btn btn-secondary btn-sm float-end me-2" onclick="cancelReorder()">
            Cancel
        </button>
    </div>

    <!-- Divisions List -->
    <div id="divisionsContainer">
        @forelse($locations as $division)
        <div class="division-card {{ !$division->is_active ? 'inactive' : '' }}" data-id="{{ $division->id }}" data-order="{{ $division->order }}">
            <div class="division-header">
                <div class="d-flex align-items-center">
                    <span class="drag-handle" style="display: none;">
                        <i class="fas fa-grip-vertical fa-2x"></i>
                    </span>
                    <div class="division-icon me-3">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $division->name }}</h4>
                        <small class="text-muted">
                            {{ $division->districts->count() }} districts
                        </small>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="editLocation({{ $division->id }})">
                            <i class="fas fa-edit"></i> Edit Division
                        </a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="addDistrict({{ $division->id }})">
                            <i class="fas fa-plus"></i> Add District
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="toggleLocation({{ $division->id }}, {{ $division->is_active ? 'true' : 'false' }})">
                            <i class="fas fa-toggle-{{ $division->is_active ? 'on' : 'off' }}"></i> {{ $division->is_active ? 'Disable' : 'Enable' }}
                        </a></li>
                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteLocation({{ $division->id }}, 'division')">
                            <i class="fas fa-trash"></i> Delete
                        </a></li>
                    </ul>
                </div>
            </div>
            
            @if($division->districts && $division->districts->count() > 0)
                <div class="mt-3">
                    <strong class="text-muted small">Districts:</strong>
                    <div class="mt-2" data-division-id="{{ $division->id }}">
                        @foreach(($division->districtsWithCount ?? $division->districts ?? []) as $district)
                            <div class="district-item" data-id="{{ $district->id }}" data-order="{{ $district->order }}">
                                <div class="d-flex align-items-center">
                                    <span class="drag-handle-district" style="display: none;">
                                        <i class="fas fa-grip-horizontal"></i>
                                    </span>
                                    <i class="fas fa-map-pin me-2 text-primary"></i>
                                    <span>{{ $district->name }}</span>
                                    @if(!$district->is_active)
                                        <span class="badge bg-secondary ms-2">Inactive</span>
                                    @endif
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-light me-1" onclick="editLocation({{ $district->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light me-1" onclick="toggleLocation({{ $district->id }}, {{ $district->is_active ? 'true' : 'false' }})">
                                        <i class="fas fa-toggle-{{ $district->is_active ? 'on' : 'off' }}"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger" onclick="deleteLocation({{ $district->id }}, 'district')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center text-muted py-3">
                    <small>No districts yet. <a href="javascript:void(0)" onclick="addDistrict({{ $division->id }})">Add first district</a></small>
                </div>
            @endif
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fas fa-map-marked-alt fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No divisions found</h4>
            <button class="btn btn-primary mt-3" onclick="showAddDivisionModal()">
                <i class="fas fa-plus"></i> Add First Division
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Add Division Modal -->
<div class="modal fade" id="divisionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Division</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="divisionForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Division Name*</label>
                        <input type="text" class="form-control" id="divisionName" required placeholder="e.g., Dhaka">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Division</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add District Modal -->
<div class="modal fade" id="districtModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add District</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="districtForm">
                <input type="hidden" id="parentDivisionId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">District Name*</label>
                        <input type="text" class="form-control" id="districtName" required placeholder="e.g., Dhaka">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add District</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Location Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm">
                <input type="hidden" id="editLocationId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name*</label>
                        <input type="text" class="form-control" id="editLocationName" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="editLocationActive">
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
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
let divisionSortable = null;
let districtSortables = [];

function showAddDivisionModal() {
    document.getElementById('divisionForm').reset();
    new bootstrap.Modal(document.getElementById('divisionModal')).show();
}

document.getElementById('divisionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch('/admin/locations/add-division', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            name: document.getElementById('divisionName').value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error adding division');
    });
});

function addDistrict(divisionId) {
    document.getElementById('parentDivisionId').value = divisionId;
    document.getElementById('districtForm').reset();
    new bootstrap.Modal(document.getElementById('districtModal')).show();
}

document.getElementById('districtForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const divisionId = document.getElementById('parentDivisionId').value;
    
    fetch(`/admin/locations/${divisionId}/add-district`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            name: document.getElementById('districtName').value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error adding district');
    });
});

function editLocation(id) {
    fetch(`/admin/locations/${id}/edit`, {
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('editLocationId').value = data.location.id;
            document.getElementById('editLocationName').value = data.location.name;
            document.getElementById('editLocationActive').checked = data.location.is_active == 1;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error loading location');
    });
}

document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('editLocationId').value;
    
    fetch(`/admin/locations/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            name: document.getElementById('editLocationName').value,
            is_active: document.getElementById('editLocationActive').checked ? 1 : 0,
            country: 'Bangladesh'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error updating location');
    });
});

function toggleLocation(id, isActive) {
    if (confirm(`${isActive === 'true' ? 'Disable' : 'Enable'} this location?`)) {
        fetch(`/admin/locations/${id}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(() => location.reload())
        .catch(err => {
            console.error(err);
            alert('Error toggling location');
        });
    }
}

function deleteLocation(id, type) {
    if (confirm(`Delete this ${type}? This action cannot be undone.`)) {
        fetch(`/admin/locations/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Location deleted successfully');
                location.reload();
            } else {
                alert(data.message || 'Error deleting location');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error deleting location');
        });
    }
}

function toggleReorderMode() {
    reorderMode = !reorderMode;
    const notice = document.getElementById('reorderNotice');
    const handles = document.querySelectorAll('.drag-handle');
    const districtHandles = document.querySelectorAll('.drag-handle-district');
    
    if (reorderMode) {
        notice.style.display = 'block';
        handles.forEach(h => h.style.display = 'inline-block');
        districtHandles.forEach(h => h.style.display = 'inline-block');
        
        // Enable division sorting
        divisionSortable = new Sortable(document.getElementById('divisionsContainer'), {
            animation: 150,
            handle: '.drag-handle',
            draggable: '.division-card',
            ghostClass: 'bg-light',
        });
        
        // Enable district sorting within each division
        document.querySelectorAll('[data-division-id]').forEach(container => {
            const sortable = new Sortable(container, {
                animation: 150,
                handle: '.drag-handle-district',
                draggable: '.district-item',
                ghostClass: 'bg-light',
            });
            districtSortables.push(sortable);
        });
    } else {
        notice.style.display = 'none';
        handles.forEach(h => h.style.display = 'none');
        districtHandles.forEach(h => h.style.display = 'none');
        
        if (divisionSortable) divisionSortable.destroy();
        districtSortables.forEach(s => s.destroy());
        districtSortables = [];
    }
}

function cancelReorder() {
    location.reload();
}

function saveOrder() {
    const locations = [];
    
    // Get division order
    document.querySelectorAll('.division-card').forEach((div, index) => {
        locations.push({
            id: parseInt(div.dataset.id),
            order: index
        });
    });
    
    // Get district orders
    document.querySelectorAll('.district-item').forEach((dist, index) => {
        locations.push({
            id: parseInt(dist.dataset.id),
            order: index
        });
    });
    
    fetch('/admin/locations/reorder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ locations })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error saving order');
    });
}
</script>
@endsection
