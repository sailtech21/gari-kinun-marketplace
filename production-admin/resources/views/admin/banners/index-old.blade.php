@extends('admin.layouts.app')

@section('title', 'Banner Management')

@section('styles')
<style>
    .banner-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .banner-header h2 {
        margin: 0;
        font-weight: 700;
    }
    .stats-row {
        margin-bottom: 25px;
    }
    .stat-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }
    .stat-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
    }
    .stat-box i {
        font-size: 40px;
        opacity: 0.3;
        position: absolute;
        right: 20px;
        top: 20px;
    }
    .stat-box h3 {
        font-size: 36px;
        font-weight: bold;
        margin: 0;
        position: relative;
        z-index: 1;
    }
    .stat-box p {
        margin: 5px 0 0 0;
        font-size: 14px;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }
    .stat-box.blue {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .stat-box.green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .stat-box.red {
        background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
    }
    .filter-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .filter-section .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    #bannersTable thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        border: none;
    }
    #bannersTable tbody tr {
        transition: all 0.2s ease;
    }
    #bannersTable tbody tr:hover {
        background-color: #f0f8ff !important;
        transform: scale(1.01);
    }
    .action-buttons .btn {
        margin: 0 2px;
    }
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .img-thumbnail {
        border-radius: 10px;
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #e9ecef;
    }
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
</style>
@endsection

@section('content')
<div class="banner-header">
    <h2><i class="fas fa-image me-3"></i>Banner Management</h2>
    <p class="mb-0 mt-2">Create and manage promotional banners for your marketplace</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Statistics Row -->
<div class="row stats-row">
    <div class="col-md-4">
        <div class="stat-box blue" onclick="loadBanners('all')">
            <i class="fas fa-images"></i>
            <h3 id="totalCount">{{ $stats['total'] }}</h3>
            <p>Total Banners</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box green" onclick="loadBanners('active')">
            <i class="fas fa-check-circle"></i>
            <h3 id="activeCount">{{ $stats['active'] }}</h3>
            <p>Active Banners</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box red" onclick="loadBanners('inactive')">
            <i class="fas fa-times-circle"></i>
            <h3 id="inactiveCount">{{ $stats['inactive'] }}</h3>
            <p>Inactive Banners</p>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="card mb-4">
    <div class="card-body filter-section">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Position</label>
                <select id="positionFilter" class="form-select">
                    <option value="">All Positions</option>
                    <option value="home">Home Page</option>
                    <option value="listing">Listing Page</option>
                    <option value="category">Category Page</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-toggle-on me-2"></i>Status</label>
                <select id="statusFilter" class="form-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button id="resetFilters" class="btn btn-secondary w-100">
                    <i class="fas fa-redo me-2"></i>Reset Filters
                </button>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                    <i class="fas fa-plus me-2"></i>Add Banner
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Banners Table -->
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Banners List</h5>
            <button class="btn btn-primary btn-sm" onclick="location.reload()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="bannersTable" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag me-1"></i>ID</th>
                    <th><i class="fas fa-image me-1"></i>Image</th>
                    <th><i class="fas fa-heading me-1"></i>Title</th>
                    <th><i class="fas fa-map-marker-alt me-1"></i>Position</th>
                    <th><i class="fas fa-sort-numeric-up me-1"></i>Order</th>
                    <th><i class="fas fa-toggle-on me-1"></i>Status</th>
                    <th><i class="fas fa-cogs me-1"></i>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Add Banner Modal -->
<div class="modal fade" id="addBannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Banner</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-heading me-2"></i>Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter banner title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Position *</label>
                            <select name="position" class="form-select" required>
                                <option value="">Select position...</option>
                                <option value="home">Home Page</option>
                                <option value="listing">Listing Page</option>
                                <option value="category">Category Page</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label"><i class="fas fa-align-left me-2"></i>Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Enter banner description"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-link me-2"></i>Link URL</label>
                            <input type="url" name="link" class="form-control" placeholder="https://example.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-mouse-pointer me-2"></i>Button Text</label>
                            <input type="text" name="button_text" class="form-control" placeholder="e.g., Learn More">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-sort-numeric-up me-2"></i>Display Order</label>
                            <input type="number" name="order" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-image me-2"></i>Banner Image *</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                            <small class="text-muted">Recommended size: 1920x600px</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" id="newBannerActive" checked>
                                <label class="form-check-label" for="newBannerActive">
                                    <strong>Active</strong> - Banner will be visible on the website
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Banner
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bannerDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Banner Modal -->
<div class="modal fade" id="editBannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Banner</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBannerForm" enctype="multipart/form-data">
                <input type="hidden" id="editBannerId">
                <input type="hidden" id="editBannerCurrentImage">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-heading me-2"></i>Title *</label>
                            <input type="text" id="editBannerTitle" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Position *</label>
                            <select id="editBannerPosition" class="form-select" required>
                                <option value="home">Home Page</option>
                                <option value="listing">Listing Page</option>
                                <option value="category">Category Page</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label"><i class="fas fa-align-left me-2"></i>Description</label>
                            <textarea id="editBannerDescription" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-link me-2"></i>Link URL</label>
                            <input type="url" id="editBannerLink" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-mouse-pointer me-2"></i>Button Text</label>
                            <input type="text" id="editBannerButtonText" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-sort-numeric-up me-2"></i>Display Order</label>
                            <input type="number" id="editBannerOrder" class="form-control" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-image me-2"></i>Banner Image</label>
                            <input type="file" id="editBannerImage" class="form-control" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image</small>
                        </div>
                        <div class="col-md-12 mb-3" id="editBannerCurrentImagePreview">
                            <!-- Image preview will be shown here -->
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" id="editBannerActive" class="form-check-input" value="1">
                                <label class="form-check-label" for="editBannerActive">
                                    <strong>Active</strong> - Banner will be visible on the website
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>Update Banner
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
        // Initialize DataTable
        table = $('#bannersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.banners.index') }}',
                data: function(d) {
                    d.position = $('#positionFilter').val();
                    d.status = $('#statusFilter').val();
                }
            },
            columns: [
                { data: 'id', width: '50px' },
                { data: 'image_preview', orderable: false, width: '120px' },
                { 
                    data: 'title',
                    render: (data, type, row) => {
                        return `<strong>${data}</strong>${row.description ? '<br><small class="text-muted">' + row.description.substring(0, 50) + '...</small>' : ''}`;
                    }
                },
                { 
                    data: 'position',
                    render: (data) => {
                        const positions = {
                            'home': '<span class="badge bg-primary">Home</span>',
                            'listing': '<span class="badge bg-info">Listing</span>',
                            'category': '<span class="badge bg-success">Category</span>'
                        };
                        return positions[data] || data;
                    }
                },
                { data: 'order', width: '80px' },
                { data: 'status_badge', width: '100px' },
                { data: 'action', orderable: false, width: '220px' }
            ],
            order: [[4, 'asc']],
            pageLength: 25,
            language: {
                processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading banners...'
            }
        });

        // Filter change handlers
        $('#positionFilter, #statusFilter').on('change', function() {
            table.ajax.reload();
        });

        // Reset filters
        $('#resetFilters').on('click', function() {
            $('#positionFilter, #statusFilter').val('');
            table.ajax.reload();
        });

        // Edit Banner
        $(document).on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            
            $.get(`/admin/banners/${id}`, function(banner) {
                $('#editBannerId').val(banner.id);
                $('#editBannerTitle').val(banner.title);
                $('#editBannerDescription').val(banner.description || '');
                $('#editBannerLink').val(banner.link || '');
                $('#editBannerButtonText').val(banner.button_text || '');
                $('#editBannerPosition').val(banner.position);
                $('#editBannerOrder').val(banner.order);
                $('#editBannerActive').prop('checked', banner.is_active);
                $('#editBannerCurrentImage').val(banner.image);
                
                // Show current image preview
                if (banner.image) {
                    $('#editBannerCurrentImagePreview').html(
                        '<label class="text-muted small">Current Image</label><br>' +
                        '<img src="/storage/' + banner.image + '" class="img-fluid rounded" style="max-height: 200px;">'
                    );
                }
                
                $('#editBannerModal').modal('show');
            });
        });

        // Update Banner Form Submit
        $('#editBannerForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#editBannerId').val();
            const formData = new FormData();
            
            formData.append('title', $('#editBannerTitle').val());
            formData.append('description', $('#editBannerDescription').val());
            formData.append('link', $('#editBannerLink').val());
            formData.append('button_text', $('#editBannerButtonText').val());
            formData.append('position', $('#editBannerPosition').val());
            formData.append('order', $('#editBannerOrder').val());
            formData.append('is_active', $('#editBannerActive').is(':checked') ? 1 : 0);
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PUT');
            
            // Add image if selected
            const imageFile = $('#editBannerImage')[0].files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }
            
            $.ajax({
                url: `/admin/banners/${id}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert(response.message);
                    $('#editBannerModal').modal('hide');
                    table.ajax.reload();
                    location.reload(); // Reload to update stats
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                }
            });
        });

        // View Banner
        $(document).on('click', '.view-btn', function() {
            const id = $(this).data('id');
            
            $.get(`/admin/banners/${id}`, function(banner) {
                let content = `
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <img src="${banner.image ? '/storage/' + banner.image : ''}" class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Title</label>
                            <p class="fw-bold">${banner.title}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Position</label>
                            <p class="fw-bold">${banner.position.charAt(0).toUpperCase() + banner.position.slice(1)}</p>
                        </div>
                        ${banner.description ? `
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small">Description</label>
                            <p>${banner.description}</p>
                        </div>` : ''}
                        ${banner.link ? `
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Link</label>
                            <p><a href="${banner.link}" target="_blank">${banner.link}</a></p>
                        </div>` : ''}
                        ${banner.button_text ? `
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Button Text</label>
                            <p>${banner.button_text}</p>
                        </div>` : ''}
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Order</label>
                            <p>${banner.order}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status</label>
                            <p>${banner.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</p>
                        </div>
                    </div>
                `;
                
                $('#bannerDetailsContent').html(content);
                $('#viewBannerModal').modal('show');
            });
        });

        // Delete Banner
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            
            if(confirm('Are you sure you want to delete this banner? This action cannot be undone.')) {
                $.ajax({
                    url: `/admin/banners/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        table.ajax.reload();
                        location.reload(); // Reload to update stats
                    },
                    error: function(xhr) {
                        alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                    }
                });
            }
        });

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });

    // Load banners by status
    function loadBanners(status) {
        if (status === 'all') {
            $('#statusFilter').val('');
        } else {
            $('#statusFilter').val(status);
        }
        table.ajax.reload();
    }
</script>
@endsection
