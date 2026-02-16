@extends('admin.layouts.app')

@section('title', 'Users Management')

@section('styles')
<style>
    .stats-row {
        margin-bottom: 30px;
    }
    .stat-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }
    .stat-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    .stat-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.1);
        transform: translateX(-100%);
        transition: transform 0.3s;
    }
    .stat-box:hover::before {
        transform: translateX(100%);
    }
    .stat-box.blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .stat-box.green {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    .stat-box.purple {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    .stat-box.orange {
        background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%);
    }
    .stat-box i {
        font-size: 3rem;
        opacity: 0.3;
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
    }
    .stat-box h3 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 5px;
    }
    .stat-box p {
        margin: 0;
        font-size: 1rem;
        opacity: 0.95;
    }
    .filter-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .table-card {
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
    }
    #usersTable tbody tr {
        transition: background-color 0.2s;
    }
    #usersTable tbody tr:hover {
        background-color: #f0f8ff !important;
    }
</style>
@endsection

@section('content')
<!-- Statistics Row -->
<div class="row stats-row">
    <div class="col-md-3">
        <div class="stat-box blue">
            <i class="fas fa-users"></i>
            <h3 id="totalUsers">{{ $stats['total'] }}</h3>
            <p>Total Users</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box green">
            <i class="fas fa-user-check"></i>
            <h3 id="verifiedUsers">{{ $stats['verified'] }}</h3>
            <p>Verified Users</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box purple">
            <i class="fas fa-user-clock"></i>
            <h3 id="activeUsers">{{ $stats['active_today'] }}</h3>
            <p>Active Today</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box orange">
            <i class="fas fa-user-slash"></i>
            <h3 id="unverifiedUsers">{{ $stats['total'] - $stats['verified'] }}</h3>
            <p>Unverified</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <div class="row">
        <div class="col-md-3">
            <label class="form-label"><i class="fas fa-filter me-1"></i>Verification Status</label>
            <select id="verificationFilter" class="form-select">
                <option value="">All Users</option>
                <option value="verified">Verified Only</option>
                <option value="unverified">Unverified Only</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label"><i class="fas fa-calendar me-1"></i>Registration Date</label>
            <select id="dateFilter" class="form-select">
                <option value="">All Time</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label"><i class="fas fa-list me-1"></i>Listings Count</label>
            <select id="listingsFilter" class="form-select">
                <option value="">Any</option>
                <option value="has">Has Listings</option>
                <option value="none">No Listings</option>
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

<!-- Users Table -->
<div class="card table-card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i>All Users</h5>
    </div>
    <div class="card-body">
        <table id="usersTable" class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th width="60">ID</th>
                    <th width="80">Avatar</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Listings</th>
                    <th>Status</th>
                    <th>Last Activity</th>
                    <th>Joined</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let table;
    
    $(document).ready(function() {
        // Initialize DataTable
        table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.users.index') }}',
                data: function(d) {
                    d.verification = $('#verificationFilter').val();
                    d.date = $('#dateFilter').val();
                    d.listings = $('#listingsFilter').val();
                }
            },
            columns: [
                { data: 'id' },
                { 
                    data: 'name',
                    orderable: false,
                    render: function(data, type, row) {
                        let initial = data.charAt(0).toUpperCase();
                        return `<div class="user-avatar">${initial}</div>`;
                    }
                },
                { 
                    data: 'name',
                    render: function(data, type, row) {
                        return `<strong>${data}</strong>`;
                    }
                },
                { 
                    data: 'email',
                    render: function(data) {
                        return `<a href="mailto:${data}">${data}</a>`;
                    }
                },
                { 
                    data: 'listings_count',
                    render: function(data) {
                        let color = data > 0 ? 'success' : 'secondary';
                        return `<span class="badge bg-${color}"><i class="fas fa-list me-1"></i>${data}</span>`;
                    }
                },
                { data: 'email_verified_at' },
                {
                    data: 'updated_at',
                    render: function(data) {
                        let date = new Date(data);
                        let now = new Date();
                        let diff = Math.floor((now - date) / (1000 * 60));
                        
                        if (diff < 60) return `<small class="text-success">${diff}m ago</small>`;
                        if (diff < 1440) return `<small class="text-info">${Math.floor(diff/60)}h ago</small>`;
                        return `<small class="text-muted">${date.toLocaleDateString()}</small>`;
                    }
                },
                { 
                    data: 'created_at', 
                    render: (data) => new Date(data).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    })
                },
                { data: 'action', orderable: false }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            language: {
                processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading...'
            }
        });

        // View User Details
        $(document).on('click', '.view-btn', function() {
            const id = $(this).data('id');
            
            $.get(`/admin/users/${id}`, function(user) {
                let verified = user.email_verified_at ? 
                    '<span class="badge bg-success"><i class="fas fa-check"></i> Verified</span>' : 
                    '<span class="badge bg-warning"><i class="fas fa-clock"></i> Unverified</span>';
                
                let modal = `
                    <div class="modal fade" id="userModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="fas fa-user me-2"></i>User Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Name</label>
                                            <p class="fw-bold">${user.name}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Email</label>
                                            <p class="fw-bold">${user.email}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Status</label>
                                            <p>${verified}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Total Listings</label>
                                            <p class="fw-bold">${user.listings_count}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Joined</label>
                                            <p>${new Date(user.created_at).toLocaleString()}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Last Activity</label>
                                            <p>${new Date(user.updated_at).toLocaleString()}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('body').append(modal);
                $('#userModal').modal('show');
                $('#userModal').on('hidden.bs.modal', function() {
                    $(this).remove();
                });
            });
        });

        // Delete User
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            
            if(confirm('Are you sure you want to delete this user? All their listings will also be deleted.')) {
                $.ajax({
                    url: `/admin/users/${id}`,
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

        // Verify User
        $(document).on('click', '.verify-btn', function() {
            const id = $(this).data('id');
            
            if(confirm('Are you sure you want to verify this user?')) {
                $.ajax({
                    url: `/admin/users/${id}/verify`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showNotification('User verified successfully!', 'success');
                        table.ajax.reload(null, false);
                        loadStats();
                    },
                    error: function(xhr) {
                        alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                    }
                });
            }
        });

        // Unverify User
        $(document).on('click', '.unverify-btn', function() {
            const id = $(this).data('id');
            
            if(confirm('Are you sure you want to unverify this user?')) {
                $.ajax({
                    url: `/admin/users/${id}/unverify`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showNotification('User unverified successfully!', 'warning');
                        table.ajax.reload(null, false);
                        loadStats();
                    },
                    error: function(xhr) {
                        alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                    }
                });
            }
        });

        // Filter Events
        $('#verificationFilter, #dateFilter, #listingsFilter').on('change', function() {
            table.ajax.reload();
        });

        // Reset Filters
        $('#resetFilters').on('click', function() {
            $('#verificationFilter').val('');
            $('#dateFilter').val('');
            $('#listingsFilter').val('');
            table.ajax.reload();
        });

        // Load Stats
        function loadStats() {
            $.get('{{ route('admin.users.stats') }}', function(data) {
                $('#totalUsers').text(data.total);
                $('#verifiedUsers').text(data.verified);
                $('#activeUsers').text(data.active_today);
                $('#unverifiedUsers').text(data.unverified);
            });
        }

        // Show Notification
        function showNotification(message, type) {
            const bgClass = type === 'success' ? 'bg-success' : 'bg-warning';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            const toast = $(`
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                    <div class="toast show align-items-center text-white ${bgClass} border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas ${icon} me-2"></i>${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                </div>
            `);
            
            $('body').append(toast);
            setTimeout(function() {
                toast.fadeOut(function() { $(this).remove(); });
            }, 3000);
        }
    });
</script>
@endsection
