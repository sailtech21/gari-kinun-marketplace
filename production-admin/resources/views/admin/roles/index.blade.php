@extends('admin.layouts.app')

@section('title', 'Admin Roles Management')

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
    
    .role-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    
    .role-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .role-card.system-role {
        border-left: 4px solid #667eea;
    }
    
    .role-card.custom-role {
        border-left: 4px solid #11998e;
    }
    
    .permission-group {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }
    
    .permission-group h6 {
        color: #667eea;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }
    
    .permission-group h6 i {
        margin-right: 8px;
    }
    
    .permission-checkbox {
        margin-bottom: 8px;
    }
    
    .permission-checkbox label {
        margin-left: 8px;
        cursor: pointer;
    }
    
    .badge-system {
        background: #667eea;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
    }
    
    .badge-custom {
        background: #11998e;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">👮 Admin Roles Management</h1>
        <button class="btn btn-primary" onclick="showCreateRoleModal()">
            <i class="fas fa-plus"></i> Create New Role
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card bg-gradient-primary">
            <div class="icon"><i class="fas fa-user-shield"></i></div>
            <div class="number" id="statTotal">0</div>
            <div class="label">Total Roles</div>
        </div>
        <div class="stat-card bg-gradient-success">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <div class="number" id="statActive">0</div>
            <div class="label">Active Roles</div>
        </div>
        <div class="stat-card bg-gradient-warning">
            <div class="icon"><i class="fas fa-shield-alt"></i></div>
            <div class="number" id="statSystem">0</div>
            <div class="label">System Roles</div>
        </div>
        <div class="stat-card bg-gradient-info">
            <div class="icon"><i class="fas fa-user-cog"></i></div>
            <div class="number" id="statCustom">0</div>
            <div class="label">Custom Roles</div>
        </div>
    </div>

    <!-- Roles List -->
    <div id="rolesList"></div>
</div>

<!-- Create/Edit Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle">Create New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="roleForm">
                <div class="modal-body">
                    <input type="hidden" id="roleId" name="roleId">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Role Name*</label>
                            <input type="text" class="form-control" id="roleName" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug (auto-generated)</label>
                            <input type="text" class="form-control" id="roleSlug" name="slug" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="roleDescription" name="description" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="roleActive" name="is_active" checked>
                            <label class="form-check-label" for="roleActive">Active Role</label>
                        </div>
                    </div>

                    <h6 class="mb-3">Permission Control</h6>
                    <p class="text-muted small mb-3">Select permissions for this role to control access to different sections</p>

                    <!-- Users Permissions -->
                    <div class="permission-group">
                        <h6><i class="fas fa-users"></i> Users Management</h6>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_users_view" value="users.view">
                            <label for="perm_users_view">View Users</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_users_create" value="users.create">
                            <label for="perm_users_create">Create Users</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_users_edit" value="users.edit">
                            <label for="perm_users_edit">Edit Users</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_users_delete" value="users.delete">
                            <label for="perm_users_delete">Delete Users</label>
                        </div>
                    </div>

                    <!-- Ads Permissions -->
                    <div class="permission-group">
                        <h6><i class="fas fa-bullhorn"></i> Ads Management</h6>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_ads_view" value="ads.view">
                            <label for="perm_ads_view">View Ads</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_ads_create" value="ads.create">
                            <label for="perm_ads_create">Create Ads</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_ads_edit" value="ads.edit">
                            <label for="perm_ads_edit">Edit Ads</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_ads_delete" value="ads.delete">
                            <label for="perm_ads_delete">Delete Ads</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_ads_approve" value="ads.approve">
                            <label for="perm_ads_approve">Approve Ads</label>
                        </div>
                    </div>

                    <!-- Reports Permissions -->
                    <div class="permission-group">
                        <h6><i class="fas fa-flag"></i> Reports Management</h6>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_reports_view" value="reports.view">
                            <label for="perm_reports_view">View Reports</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_reports_manage" value="reports.manage">
                            <label for="perm_reports_manage">Manage Reports</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_reports_resolve" value="reports.resolve">
                            <label for="perm_reports_resolve">Resolve Reports</label>
                        </div>
                    </div>

                    <!-- Payments Permissions -->
                    <div class="permission-group">
                        <h6><i class="fas fa-dollar-sign"></i> Payments Management</h6>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_payments_view" value="payments.view">
                            <label for="perm_payments_view">View Payments</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_payments_manage" value="payments.manage">
                            <label for="perm_payments_manage">Manage Payments</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_payments_refund" value="payments.refund">
                            <label for="perm_payments_refund">Process Refunds</label>
                        </div>
                    </div>

                    <!-- Settings Permissions -->
                    <div class="permission-group">
                        <h6><i class="fas fa-cog"></i> Settings Management</h6>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_settings_view" value="settings.view">
                            <label for="perm_settings_view">View Settings</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_settings_edit" value="settings.edit">
                            <label for="perm_settings_edit">Edit Settings</label>
                        </div>
                    </div>

                    <!-- Content Permissions -->
                    <div class="permission-group">
                        <h6><i class="fas fa-file-alt"></i> Content Management</h6>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_content_view" value="content.view">
                            <label for="perm_content_view">View Content</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_content_edit" value="content.edit">
                            <label for="perm_content_edit">Edit Content</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_content_delete" value="content.delete">
                            <label for="perm_content_delete">Delete Content</label>
                        </div>
                    </div>

                    <!-- Analytics Permissions -->
                    <div class="permission-group">
                        <h6><i class="fas fa-chart-line"></i> Analytics</h6>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_analytics_view" value="analytics.view">
                            <label for="perm_analytics_view">View Analytics</label>
                        </div>
                        <div class="permission-checkbox">
                            <input type="checkbox" id="perm_analytics_export" value="analytics.export">
                            <label for="perm_analytics_export">Export Analytics</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Users Modal -->
<div class="modal fade" id="viewUsersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Users with Role: <span id="viewUsersRoleName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="usersList"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Load stats
function loadStats() {
    fetch('/admin/roles/stats')
        .then(res => res.json())
        .then(data => {
            document.getElementById('statTotal').textContent = data.total_roles;
            document.getElementById('statActive').textContent = data.active_roles;
            document.getElementById('statSystem').textContent = data.system_roles;
            document.getElementById('statCustom').textContent = data.custom_roles;
        });
}

// Load all roles
function loadRoles() {
    fetch('/admin/roles/all')
        .then(res => res.json())
        .then(roles => {
            const container = document.getElementById('rolesList');
            
            if (roles.length === 0) {
                container.innerHTML = '<div class="alert alert-info">No roles found. Create your first role!</div>';
                return;
            }

            let html = '';
            roles.forEach(role => {
                const badgeClass = role.is_system ? 'badge-system' : 'badge-custom';
                const cardClass = role.is_system ? 'system-role' : 'custom-role';
                const statusBadge = role.is_active 
                    ? '<span class="badge bg-success">Active</span>' 
                    : '<span class="badge bg-secondary">Inactive</span>';
                
                const permissions = Array.isArray(role.permissions) ? role.permissions : [];
                const permCount = permissions.length;

                html += `
                    <div class="role-card ${cardClass}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h5>${role.name} 
                                    <span class="${badgeClass}">${role.is_system ? 'System' : 'Custom'}</span>
                                    ${statusBadge}
                                </h5>
                                <p class="text-muted mb-2">${role.description || 'No description'}</p>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt"></i> ${permCount} permissions
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-users"></i> ${role.users_count || 0} users
                                    </small>
                                </div>
                                <div>
                                    ${permissions.slice(0, 5).map(p => 
                                        `<span class="badge bg-light text-dark me-1">${p}</span>`
                                    ).join('')}
                                    ${permCount > 5 ? `<span class="badge bg-secondary">+${permCount - 5} more</span>` : ''}
                                </div>
                            </div>
                            <div class="ms-3">
                                <button class="btn btn-sm btn-info mb-1" onclick="viewRoleUsers(${role.id}, '${role.name}')" title="View Users">
                                    <i class="fas fa-users"></i>
                                </button>
                                <button class="btn btn-sm btn-primary mb-1" onclick="editRole(${role.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${!role.is_system ? `
                                <button class="btn btn-sm btn-warning mb-1" onclick="toggleRole(${role.id})" title="Toggle Status">
                                    <i class="fas fa-power-off"></i>
                                </button>
                                <button class="btn btn-sm btn-danger mb-1" onclick="deleteRole(${role.id}, '${role.name}')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        });
}

// Show create role modal
function showCreateRoleModal() {
    document.getElementById('roleModalTitle').textContent = 'Create New Role';
    document.getElementById('roleForm').reset();
    document.getElementById('roleId').value = '';
    
    // Uncheck all permissions
    document.querySelectorAll('[id^="perm_"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    new bootstrap.Modal(document.getElementById('roleModal')).show();
}

// Auto-generate slug from name
document.getElementById('roleName').addEventListener('input', function(e) {
    const slug = e.target.value.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
    document.getElementById('roleSlug').value = slug;
});

// Edit role
function editRole(id) {
    fetch(`/admin/roles/${id}/edit`)
        .then(res => res.json())
        .then(role => {
            document.getElementById('roleModalTitle').textContent = 'Edit Role';
            document.getElementById('roleId').value = role.id;
            document.getElementById('roleName').value = role.name;
            document.getElementById('roleSlug').value = role.slug;
            document.getElementById('roleDescription').value = role.description || '';
            document.getElementById('roleActive').checked = role.is_active;

            // Uncheck all first
            document.querySelectorAll('[id^="perm_"]').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Check role permissions
            const permissions = Array.isArray(role.permissions) ? role.permissions : [];
            permissions.forEach(perm => {
                const checkbox = document.querySelector(`[value="${perm}"]`);
                if (checkbox) checkbox.checked = true;
            });

            new bootstrap.Modal(document.getElementById('roleModal')).show();
        });
}

// Save role (create or update)
document.getElementById('roleForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const roleId = document.getElementById('roleId').value;
    const formData = {
        name: document.getElementById('roleName').value,
        slug: document.getElementById('roleSlug').value,
        description: document.getElementById('roleDescription').value,
        is_active: document.getElementById('roleActive').checked,
        permissions: []
    };

    // Collect selected permissions
    document.querySelectorAll('[id^="perm_"]:checked').forEach(checkbox => {
        formData.permissions.push(checkbox.value);
    });

    const url = roleId ? `/admin/roles/${roleId}` : '/admin/roles';
    const method = roleId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('roleModal')).hide();
            loadRoles();
            loadStats();
        }
    })
    .catch(err => {
        alert('Error saving role');
        console.error(err);
    });
});

// Toggle role status
function toggleRole(id) {
    if (!confirm('Toggle role status?')) return;

    fetch(`/admin/roles/${id}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            loadRoles();
            loadStats();
        }
    });
}

// Delete role
function deleteRole(id, name) {
    if (!confirm(`Delete role "${name}"? This action cannot be undone.`)) return;

    fetch(`/admin/roles/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            loadRoles();
            loadStats();
        }
    });
}

// View users with role
function viewRoleUsers(roleId, roleName) {
    document.getElementById('viewUsersRoleName').textContent = roleName;
    
    fetch(`/admin/roles/${roleId}/users`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('usersList');
            
            if (data.users.length === 0) {
                container.innerHTML = '<p class="text-muted">No users assigned to this role yet.</p>';
            } else {
                let html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>Name</th><th>Email</th><th>Joined</th></tr></thead><tbody>';
                
                data.users.forEach(user => {
                    html += `
                        <tr>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${new Date(user.created_at).toLocaleDateString()}</td>
                        </tr>
                    `;
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            }
            
            new bootstrap.Modal(document.getElementById('viewUsersModal')).show();
        });
}

// Load on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadRoles();
});
</script>
@endsection
