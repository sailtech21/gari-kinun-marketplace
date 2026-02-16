@extends('admin.layouts.app')

@section('title', 'Listings Management')

@section('styles')
<style>
    .stats-row {
        margin-bottom: 25px;
    }
    .stat-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    .stat-box:hover {
        transform: translateY(-5px);
    }
    .stat-box.green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .stat-box.blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .stat-box.orange {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    .stat-box h3 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }
    .stat-box p {
        margin: 5px 0 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .filter-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .table-card {
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .action-buttons .btn {
        margin: 0 2px;
    }
    #listingsTable tbody tr {
        transition: background-color 0.2s;
    }
    #listingsTable tbody tr:hover {
        background-color: #f0f8ff !important;
    }
</style>
@endsection

@section('content')
<!-- Statistics Row -->
<div class="row stats-row">
    <div class="col-md-3">
        <div class="stat-box">
            <i class="fas fa-list fa-2x mb-2"></i>
            <h3 id="totalListings">0</h3>
            <p>Total Listings</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box green">
            <i class="fas fa-check-circle fa-2x mb-2"></i>
            <h3 id="activeListings">0</h3>
            <p>Active Listings</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box blue">
            <i class="fas fa-clock fa-2x mb-2"></i>
            <h3 id="pendingListings">0</h3>
            <p>Pending Review</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box orange">
            <i class="fas fa-star fa-2x mb-2"></i>
            <h3 id="featuredListings">0</h3>
            <p>Featured</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <div class="row">
        <div class="col-md-3">
            <label class="form-label"><i class="fas fa-filter me-1"></i>Status Filter</label>
            <select id="statusFilter" class="form-select">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="sold">Sold</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label"><i class="fas fa-tag me-1"></i>Category</label>
            <select id="categoryFilter" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label"><i class="fas fa-star me-1"></i>Featured</label>
            <select id="featuredFilter" class="form-select">
                <option value="">All Listings</option>
                <option value="1">Featured Only</option>
                <option value="0">Non-Featured</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <button id="resetFilters" class="btn btn-secondary w-100">
                <i class="fas fa-redo me-1"></i>Reset Filters
            </button>
        </div>
    </div>
</div>

<!-- Listings Table -->
<div class="card table-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-table me-2"></i>All Listings</h5>
        <div>
            <select id="bulkAction" class="form-select d-inline-block w-auto me-2">
                <option value="">Bulk Actions</option>
                <option value="active">Mark as Active</option>
                <option value="pending">Mark as Pending</option>
                <option value="rejected">Mark as Rejected</option>
                <option value="feature">Mark as Featured</option>
                <option value="unfeature">Remove Featured</option>
                <option value="delete">Delete Selected</option>
            </select>
            <button id="applyBulk" class="btn btn-primary">
                <i class="fas fa-check me-1"></i>Apply
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="listingsTable" class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th width="40"><input type="checkbox" id="selectAll"></th>
                    <th width="60">ID</th>
                    <th>Title</th>
                    <th>User</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Featured</th>
                    <th width="200">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Edit Listing Modal -->
<div class="modal fade" id="editListingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Listing</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editListingForm">
                <input type="hidden" id="editListingId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label"><i class="fas fa-heading me-2"></i>Title *</label>
                            <input type="text" id="editTitle" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label"><i class="fas fa-align-left me-2"></i>Description *</label>
                            <textarea id="editDescription" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-dollar-sign me-2"></i>Price *</label>
                            <input type="number" id="editPrice" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Location *</label>
                            <input type="text" id="editLocation" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-folder me-2"></i>Category *</label>
                            <select id="editCategory" class="form-select" required>
                                <option value="">Select category...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-info-circle me-2"></i>Status *</label>
                            <select id="editStatus" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="sold">Sold</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>Update Listing
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    let table;
    
    $(document).ready(function() {
        // Load statistics
        loadStats();
        
        // Initialize DataTable
        table = $('#listingsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.listings.index') }}',
                data: function(d) {
                    d.status = $('#statusFilter').val();
                    d.category = $('#categoryFilter').val();
                    d.featured = $('#featuredFilter').val();
                }
            },
            columns: [
                { 
                    data: 'id', 
                    orderable: false,
                    render: (data) => `<input type="checkbox" class="select-item" value="${data}">` 
                },
                { data: 'id' },
                { 
                    data: 'title',
                    render: (data, type, row) => {
                        let badge = row.is_featured ? '<i class="fas fa-star text-warning ms-1"></i>' : '';
                        return `<strong>${data}</strong>${badge}`;
                    }
                },
                { data: 'user.name' },
                { 
                    data: 'category.name',
                    render: (data) => `<span class="badge bg-secondary">${data}</span>`
                },
                { 
                    data: 'price', 
                    render: (data) => `<strong class="text-success">$${parseFloat(data).toFixed(2)}</strong>` 
                },
                { data: 'status' },
                { 
                    data: 'views',
                    render: (data) => `<i class="fas fa-eye text-info me-1"></i>${data}`
                },
                {
                    data: 'is_featured',
                    render: (data) => data ? 
                        '<span class="badge bg-warning"><i class="fas fa-star"></i> Yes</span>' : 
                        '<span class="badge bg-light text-dark">No</span>'
                },
                { data: 'action', orderable: false }
            ],
            pageLength: 25,
            order: [[1, 'desc']],
            language: {
                processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading...'
            }
        });

        // Select All
        $('#selectAll').on('click', function() {
            $('.select-item').prop('checked', this.checked);
        });

        // Apply Bulk Action
        $('#applyBulk').on('click', function() {
            const action = $('#bulkAction').val();
            const ids = $('.select-item:checked').map(function() { return this.value; }).get();
            
            if(!action) {
                alert('Please select an action');
                return;
            }
            
            if(ids.length === 0) {
                alert('Please select at least one listing');
                return;
            }

            const actionText = action === 'delete' ? 'delete' : 'update';
            if(confirm(`Are you sure you want to ${actionText} ${ids.length} listing(s)?`)) {
                $.ajax({
                    url: '{{ route('admin.listings.bulk') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        action: action,
                        ids: ids
                    },
                    success: function(response) {
                        alert(response.message);
                        table.ajax.reload();
                        loadStats();
                        $('#selectAll').prop('checked', false);
                    },
                    error: function(xhr) {
                        alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                    }
                });
            }
        });

        // Delete Single Listing
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            
            if(confirm('Are you sure you want to delete this listing?')) {
                $.ajax({
                    url: `/admin/listings/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        table.ajax.reload();
                        loadStats();
                    },
                    error: function(xhr) {
                        alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                    }
                });
            }
        });

        // Toggle Featured Status
        $(document).on('click', '.toggle-featured', function() {
            const id = $(this).data('id');
            const button = $(this);
            
            $.ajax({
                url: `/admin/listings/${id}/toggle-featured`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Update button appearance
                    if(response.is_featured) {
                        button.removeClass('btn-outline-secondary').addClass('btn-outline-warning');
                        button.html('<i class="fas fa-star"></i>');
                        button.attr('title', 'Remove from Featured');
                    } else {
                        button.removeClass('btn-outline-warning').addClass('btn-outline-secondary');
                        button.html('<i class="far fa-star"></i>');
                        button.attr('title', 'Mark as Featured');
                    }
                    
                    // Show success message
                    const message = response.is_featured ? 
                        'Listing marked as featured!' : 
                        'Listing removed from featured!';
                    
                    // Simple notification
                    const toast = $('<div class="alert alert-success position-fixed top-0 end-0 m-3" style="z-index: 9999;">')
                        .text(message)
                        .appendTo('body')
                        .fadeIn()
                        .delay(2000)
                        .fadeOut(function() { $(this).remove(); });
                    
                    table.ajax.reload(null, false); // Reload without resetting pagination
                    loadStats();
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                }
            });
        });

        // Edit Button
        $(document).on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            
            $.get(`/admin/listings/${id}`, function(response) {
                // Extract listing data from the HTML response or make another API call
                $.ajax({
                    url: `/admin/listings/${id}`,
                    type: 'GET',
                    headers: {'Accept': 'application/json'},
                    success: function(listing) {
                        $('#editListingId').val(listing.id);
                        $('#editTitle').val(listing.title);
                        $('#editDescription').val(listing.description);
                        $('#editPrice').val(listing.price);
                        $('#editLocation').val(listing.location);
                        $('#editCategory').val(listing.category_id);
                        $('#editStatus').val(listing.status);
                        $('#editListingModal').modal('show');
                    }
                });
            }).fail(function() {
                // If show returns HTML, redirect to a simple edit page
                window.location.href = `/admin/listings/${id}`;
            });
        });

        // Update Listing Form Submit
        $('#editListingForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#editListingId').val();
            const formData = {
                title: $('#editTitle').val(),
                description: $('#editDescription').val(),
                price: $('#editPrice').val(),
                location: $('#editLocation').val(),
                category_id: $('#editCategory').val(),
                status: $('#editStatus').val(),
                _token: '{{ csrf_token() }}'
            };
            
            $.ajax({
                url: `/admin/listings/${id}`,
                type: 'PUT',
                data: formData,
                success: function(response) {
                    alert(response.message);
                    $('#editListingModal').modal('hide');
                    table.ajax.reload();
                    loadStats();
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                }
            });
        });

        // View Button
        $(document).on('click', '.view-btn', function() {
            const id = $(this).data('id');
            window.location.href = `/admin/listings/${id}`;
        });

        // Filter Events
        $('#statusFilter, #categoryFilter, #featuredFilter').on('change', function() {
            table.ajax.reload();
            loadStats();
        });

        // Reset Filters
        $('#resetFilters').on('click', function() {
            $('#statusFilter').val('');
            $('#categoryFilter').val('');
            $('#featuredFilter').val('');
            table.ajax.reload();
            loadStats();
        });
    });

    // Load Statistics
    function loadStats() {
        $.get('{{ route('admin.api.listings.stats') }}', function(data) {
            $('#totalListings').text(data.total);
            $('#activeListings').text(data.active);
            $('#pendingListings').text(data.pending);
            $('#featuredListings').text(data.featured);
        }).fail(function() {
            // Fallback if API fails
            console.log('Stats API unavailable');
        });
    }
</script>
@endsection
