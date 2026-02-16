@extends('admin.layouts.app')

@section('title', 'Users Management')

@section('styles')
<style>
    .stat-card {
        border-radius: 15px;
        padding: 25px;
        color: white;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3) !important;
    }
    .stat-card i {
        font-size: 3rem;
        opacity: 0.2;
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
    }
    .stat-card h2 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .stat-card p {
        margin-bottom: 0;
        font-size: 1rem;
        opacity: 0.9;
    }
    
    .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-purple { background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%); }

    .filter-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }
    
    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        font-weight: 700;
    }
    
    .action-btn {
        padding: 8px 12px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.875rem;
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
</style>
@endsection

@section('content')
<div class="mb-4">
    <h2 class="mb-0"><i class="fas fa-users me-2"></i>👤 Users Management</h2>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
        <div class="card stat-card bg-gradient-primary" onclick="filterUsers('')">
            <i class="fas fa-users"></i>
            <h2 id="totalUsers">{{ number_format($stats['total']) }}</h2>
            <p>Total Users</p>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
        <div class="card stat-card bg-gradient-success" onclick="filterUsers('active')">
            <i class="fas fa-check-circle"></i>
            <h2 id="activeUsers">{{ number_format($stats['active']) }}</h2>
            <p>Active</p>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
        <div class="card stat-card bg-gradient-warning" onclick="filterUsers('suspended')">
            <i class="fas fa-pause-circle"></i>
            <h2 id="suspendedUsers">{{ number_format($stats['suspended']) }}</h2>
            <p>Suspended</p>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
        <div class="card stat-card bg-gradient-danger" onclick="filterUsers('banned')">
            <i class="fas fa-ban"></i>
            <h2 id="bannedUsers">{{ number_format($stats['banned']) }}</h2>
            <p>Banned</p>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
        <div class="card stat-card bg-gradient-info" onclick="filterUsers('', 'verified')">
            <i class="fas fa-user-check"></i>
            <h2 id="verifiedUsers">{{ number_format($stats['verified']) }}</h2>
            <p>Verified</p>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
        <div class="card stat-card bg-gradient-purple" onclick="filterUsers('', '', 'premium')">
            <i class="fas fa-crown"></i>
            <h2 id="premiumUsers">{{ number_format($stats['premium']) }}</h2>
            <p>Premium</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Search & Filter Options</h5>
    <form id="filterForm">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search Users</label>
                <input type="text" name="search" id="searchInput" class="form-control" placeholder="Name, email, phone...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" id="statusFilter" class="form-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="banned">Banned</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Verification</label>
                <select name="verification" id="verificationFilter" class="form-select">
                    <option value="">All</option>
                    <option value="verified">Verified</option>
                    <option value="unverified">Unverified</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" id="dateFrom" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" id="dateTo" class="form-control">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" onclick="applyFilters()" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">User</th>
                        <th width="15%">Email</th>
                        <th width="10%">Listings</th>
                        <th width="10%">Status</th>
                        <th width="10%">Verification</th>
                        <th width="10%">Joined</th>
                        <th width="15%">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-2">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->is_premium)
                                            <span class="badge bg-warning ms-1"><i class="fas fa-crown"></i> Premium</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><small>{{ $user->email }}</small></td>
                            <td><span class="badge bg-info">{{ $user->listings_count }} ads</span></td>
                            <td>
                                @if($user->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($user->status == 'suspended')
                                    <span class="badge bg-warning">Suspended</span>
                                @else
                                    <span class="badge bg-danger">Banned</span>
                                @endif
                            </td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Verified</span>
                                @else
                                    <span class="badge bg-secondary">Unverified</span>
                                @endif
                            </td>
                            <td><small>{{ $user->created_at->format('M d, Y') }}</small></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-info" onclick="viewUser({{ $user->id }})" title="View Profile">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="editUser({{ $user->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" title="More Actions">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(!$user->email_verified_at)
                                            <li><a class="dropdown-item" href="#" onclick="verifyUser({{ $user->id }})"><i class="fas fa-user-check me-2"></i>Verify User</a></li>
                                        @endif
                                        @if($user->status == 'active')
                                            <li><a class="dropdown-item" href="#" onclick="suspendUser({{ $user->id }})"><i class="fas fa-pause-circle me-2"></i>Suspend</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="banUser({{ $user->id }})"><i class="fas fa-ban me-2"></i>Ban User</a></li>
                                        @else
                                            <li><a class="dropdown-item" href="#" onclick="activateUser({{ $user->id }})"><i class="fas fa-check-circle me-2"></i>Activate</a></li>
                                        @endif
                                        <li><a class="dropdown-item" href="#" onclick="resetPassword({{ $user->id }})"><i class="fas fa-key me-2"></i>Reset Password</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="togglePremium({{ $user->id }})"><i class="fas fa-crown me-2"></i>Toggle Premium</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="limitPosting({{ $user->id }})"><i class="fas fa-lock me-2"></i>Limit Posting</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="viewUserAds({{ $user->id }})"><i class="fas fa-list me-2"></i>View Ads</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="viewUserReports({{ $user->id }})"><i class="fas fa-flag me-2"></i>View Reports</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="sendNotification({{ $user->id }})"><i class="fas fa-bell me-2"></i>Send Notification</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteUser({{ $user->id }})"><i class="fas fa-trash me-2"></i>Delete User</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No users found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-user-circle me-2"></i>User Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewUserContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                <div class="modal-body">
                    <input type="hidden" id="editUserId">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="editName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" id="editPhone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" id="editRole">
                            <option value="user">User</option>
                            <option value="dealer">Dealer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="editStatus">
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                            <option value="banned">Banned</option>
                        </select>
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

<!-- Send Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-bell me-2"></i>Send Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="notificationForm">
                <div class="modal-body">
                    <input type="hidden" id="notifyUserId">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" id="notifyTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" id="notifyMessage" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" id="notifyType">
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                            <option value="success">Success</option>
                            <option value="danger">Danger</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Send Notification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function filterUsers(status = '', verification = '', premium = '') {
    const form = document.getElementById('filterForm');
    if (status) document.getElementById('statusFilter').value = status;
    if (verification) document.getElementById('verificationFilter').value = verification;
    applyFilters();
}

function applyFilters() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    window.location.href = '{{ route("admin.users.index") }}?' + params.toString();
}

function viewUser(id) {
    fetch(`/admin/users/${id}`, {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Failed to load user data');
        }
        return res.json();
    })
    .then(user => {
        if (!user || !user.name) {
            throw new Error('Invalid user data');
        }
        document.getElementById('viewUserContent').innerHTML = `
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 3rem;">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    ${user.is_premium ? '<span class="badge bg-warning"><i class="fas fa-crown"></i> Premium</span>' : ''}
                </div>
                <div class="col-md-8">
                    <h4>${user.name}</h4>
                    <p class="text-muted">${user.email}</p>
                    <hr>
                    <p><strong>Phone:</strong> ${user.phone || 'N/A'}</p>
                    <p><strong>Role:</strong> <span class="badge bg-primary">${user.role || 'user'}</span></p>
                    <p><strong>Status:</strong> <span class="badge bg-${user.status == 'active' ? 'success' : 'warning'}">${user.status || 'active'}</span></p>
                    <p><strong>Verified:</strong> ${user.email_verified_at ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'}</p>
                    <p><strong>Total Listings:</strong> ${user.listings_count || 0}</p>
                    <p><strong>Total Reports:</strong> ${user.reports_count || 0}</p>
                    <p><strong>Joined:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
                </div>
            </div>
        `;
        new bootstrap.Modal(document.getElementById('viewUserModal')).show();
    })
    .catch(err => {
        console.error('Error loading user:', err);
        alert('Failed to load user data. Please try again.');
    });
}

function editUser(id) {
    fetch(`/admin/users/${id}/edit`, {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to load user');
        return res.json();
    })
    .then(user => {
        if (!user || !user.id) throw new Error('Invalid user data');
        document.getElementById('editUserId').value = user.id;
        document.getElementById('editName').value = user.name || '';
        document.getElementById('editEmail').value = user.email || '';
        document.getElementById('editPhone').value = user.phone || '';
        document.getElementById('editRole').value = user.role || 'user';
        document.getElementById('editStatus').value = user.status || 'active';
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    })
    .catch(err => {
        console.error('Error loading user:', err);
        alert('Failed to load user data. Please try again.');
    });
}

document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('editUserId').value;
    const data = {
        name: document.getElementById('editName').value,
        email: document.getElementById('editEmail').value,
        phone: document.getElementById('editPhone').value,
        role: document.getElementById('editRole').value,
        status: document.getElementById('editStatus').value,
    };
    
    fetch(`/admin/users/${id}`, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            alert('User updated successfully');
            bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
            location.reload();
        } else {
            alert(result.message || 'Failed to update user');
        }
    })
    .catch(err => {
        console.error('Error updating user:', err);
        alert('Failed to update user. Please try again.');
    });
});

function verifyUser(id) {
    if (!confirm('Verify this user?')) return;
    fetch(`/admin/users/${id}/verify`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.success) {
            location.reload();
        }
    })
    .catch(err => {
        console.error('Error verifying user:', err);
        alert('Failed to verify user. Please try again.');
    });
}

function suspendUser(id) {
    if (!confirm('Suspend this user?')) return;
    fetch(`/admin/users/${id}/suspend`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.success) {
            location.reload();
        }
    })
    .catch(err => {
        console.error('Error suspending user:', err);
        alert('Failed to suspend user. Please try again.');
    });
}

function banUser(id) {
    if (!confirm('Ban this user? This is a serious action.')) return;
    fetch(`/admin/users/${id}/ban`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.success) {
            location.reload();
        }
    })
    .catch(err => {
        console.error('Error banning user:', err);
        alert('Failed to ban user. Please try again.');
    });
}

function activateUser(id) {
    if (!confirm('Activate this user?')) return;
    fetch(`/admin/users/${id}/activate`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.success) {
            location.reload();
        }
    })
    .catch(err => {
        console.error('Error activating user:', err);
        alert('Failed to activate user. Please try again.');
    });
}

function resetPassword(id) {
    if (!confirm('Reset password for this user? A new password will be generated.')) return;
    fetch(`/admin/users/${id}/reset-password`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(result => {
        if (result.success && result.new_password) {
            alert(`Password reset! New password: ${result.new_password}`);
        } else {
            alert(result.message || 'Password reset successfully');
        }
    })
    .catch(err => {
        console.error('Error resetting password:', err);
        alert('Failed to reset password. Please try again.');
    });
}

function togglePremium(id) {
    fetch(`/admin/users/${id}/make-premium`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.success) {
            location.reload();
        }
    })
    .catch(err => {
        console.error('Error toggling premium:', err);
        alert('Failed to toggle premium status. Please try again.');
    });
}

function limitPosting(id) {
    const canPost = confirm('Allow or disallow posting for this user?\nOK = Allow, Cancel = Disallow');
    let limit = null;
    if (canPost) {
        limit = prompt('Enter listing limit (leave empty for unlimited):');
    }
    
    fetch(`/admin/users/${id}/limit-posting`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ can_post: canPost, listing_limit: limit ? parseInt(limit) : null })
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.success) {
            location.reload();
        }
    })
    .catch(err => {
        console.error('Error updating posting limits:', err);
        alert('Failed to update posting limits. Please try again.');
    });
}

function viewUserAds(id) {
    fetch(`/admin/users/${id}/ads`, {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(result => {
        if (!result.success) {
            alert(result.message || 'Failed to load user ads');
            return;
        }
        const listings = result.listings || [];
        if (listings.length === 0) {
            alert('This user has no listings');
            return;
        }
        window.location.href = `/admin/listings?user_id=${id}`;
    })
    .catch(err => {
        console.error('Error loading user ads:', err);
        alert('Failed to load user ads. Please try again.');
    });
}

function viewUserReports(id) {
    fetch(`/admin/users/${id}/reports`, {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(result => {
        if (!result.success) {
            alert(result.message || 'Reports feature not available');
            return;
        }
        const reports = result.reports || [];
        if (reports.length === 0) {
            alert('No reports found for this user');
            return;
        }
        alert(`Found ${reports.length} reports. Redirecting...`);
        window.location.href = '/admin/reports';
    })
    .catch(err => {
        console.error('Error loading user reports:', err);
        alert('Failed to load user reports. Please try again.');
    });
}

function sendNotification(id) {
    document.getElementById('notifyUserId').value = id;
    document.getElementById('notificationForm').reset();
    new bootstrap.Modal(document.getElementById('notificationModal')).show();
}

document.getElementById('notificationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('notifyUserId').value;
    const data = {
        title: document.getElementById('notifyTitle').value,
        message: document.getElementById('notifyMessage').value,
        type: document.getElementById('notifyType').value,
    };
    
    fetch(`/admin/users/${id}/send-notification`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(data)
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to send notification');
        return res.json();
    })
    .then(result => {
        alert(result.message);
        bootstrap.Modal.getInstance(document.getElementById('notificationModal')).hide();
    })
    .catch(err => {
        console.error('Error sending notification:', err);
        alert('Failed to send notification. Please try again.');
    });
});

function deleteUser(id) {
    if (!confirm('Delete this user permanently? This action cannot be undone.')) return;
    fetch(`/admin/users/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to delete user');
        return res.json();
    })
    .then(result => {
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert(result.message);
        }
    })
    .catch(err => {
        console.error('Error deleting user:', err);
        alert('Failed to delete user. Please try again.');
    });
}

// Search input live filtering
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});
</script>
@endsection
