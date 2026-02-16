@extends('admin.layouts.app')

@section('title', 'Notifications Management')

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
    .stat-card.bg-gradient-danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
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
    
    .notification-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        margin-bottom: 15px;
        border-left: 4px solid #3498db;
    }
    
    .notification-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .notification-card.global {
        border-left-color: #9b59b6;
    }
    
    .notification-card.unread {
        border-left-color: #e74c3c;
    }
    
    .type-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .type-card.disabled {
        opacity: 0.6;
        background: #f8f9fa;
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        padding: 12px 24px;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #667eea;
        background: transparent;
        border-bottom: 3px solid #667eea;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">🔔 Notifications Management</h1>
        <button class="btn btn-secondary" onclick="loadStats()">
            <i class="fas fa-sync"></i> Refresh
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card bg-gradient-primary">
            <div class="icon"><i class="fas fa-bell"></i></div>
            <div class="number" id="statTotal">0</div>
            <div class="label">Total Notifications</div>
        </div>
        <div class="stat-card bg-gradient-success">
            <div class="icon"><i class="fas fa-globe"></i></div>
            <div class="number" id="statGlobal">0</div>
            <div class="label">Global</div>
        </div>
        <div class="stat-card bg-gradient-warning">
            <div class="icon"><i class="fas fa-user"></i></div>
            <div class="number" id="statUserSpecific">0</div>
            <div class="label">User Specific</div>
        </div>
        <div class="stat-card bg-gradient-danger">
            <div class="icon"><i class="fas fa-envelope-open"></i></div>
            <div class="number" id="statUnread">0</div>
            <div class="label">Unread</div>
        </div>
        <div class="stat-card bg-gradient-info">
            <div class="icon"><i class="fas fa-envelope"></i></div>
            <div class="number" id="statEmailSent">0</div>
            <div class="label">Emails Sent</div>
        </div>
    </div>

    <!-- Admin Controls -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-cog"></i> Admin Controls</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <button class="btn btn-primary w-100" onclick="showBroadcastModal()">
                        <i class="fas fa-bullhorn"></i> Send Broadcast Notification
                    </button>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-info w-100" onclick="showTargetedModal()">
                        <i class="fas fa-users"></i> Send Targeted Notification
                    </button>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-warning w-100" onclick="showEmailAllModal()">
                        <i class="fas fa-envelope"></i> Send Email to All Users
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#notificationsTab">
                <i class="fas fa-bell"></i> View System Notifications
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#typesTab">
                <i class="fas fa-cog"></i> Manage Notification Types
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Notifications Tab -->
        <div class="tab-pane fade show active" id="notificationsTab">
            <div id="notificationsContainer">
                <!-- Notifications will be loaded here -->
            </div>
        </div>

        <!-- Notification Types Tab -->
        <div class="tab-pane fade" id="typesTab">
            <div id="typesContainer">
                <!-- Notification types will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Broadcast Modal -->
<div class="modal fade" id="broadcastModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Broadcast Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="broadcastForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Type*</label>
                        <select class="form-control" id="broadcastType" required>
                            <option value="system">System</option>
                            <option value="announcement">Announcement</option>
                            <option value="message">Message</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title*</label>
                        <input type="text" class="form-control" id="broadcastTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message*</label>
                        <textarea class="form-control" id="broadcastMessage" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon (Font Awesome)</label>
                        <input type="text" class="form-control" id="broadcastIcon" placeholder="fa-bell">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Action URL</label>
                        <input type="url" class="form-control" id="broadcastUrl" placeholder="https://example.com">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send to All Users</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Targeted Modal -->
<div class="modal fade" id="targetedModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Targeted Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="targetedForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Users*</label>
                        <select class="form-control" id="targetedUsers" multiple required style="height: 150px;">
                            <!-- Users will be loaded here -->
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple users</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type*</label>
                        <select class="form-control" id="targetedType" required>
                            <option value="system">System</option>
                            <option value="announcement">Announcement</option>
                            <option value="message">Message</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title*</label>
                        <input type="text" class="form-control" id="targetedTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message*</label>
                        <textarea class="form-control" id="targetedMessage" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Send to Selected Users</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Email All Modal -->
<div class="modal fade" id="emailAllModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Email to All Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="emailAllForm">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> This will send an email to all registered users. Use with caution!
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject*</label>
                        <input type="text" class="form-control" id="emailSubject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message*</label>
                        <textarea class="form-control" id="emailMessage" rows="6" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Send Email to All</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Notification Modal -->
<div class="modal fade" id="viewNotificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="notificationDetails">
                <!-- Details will be loaded here -->
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
    fetch('/admin/notifications/stats')
        .then(res => res.json())
        .then(data => {
            document.getElementById('statTotal').textContent = data.total;
            document.getElementById('statGlobal').textContent = data.global;
            document.getElementById('statUserSpecific').textContent = data.user_specific;
            document.getElementById('statUnread').textContent = data.unread;
            document.getElementById('statEmailSent').textContent = data.email_sent;
        });
}

// Load notifications
function loadNotifications() {
    fetch('/admin/notifications/all')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('notificationsContainer');
            container.innerHTML = '';
            
            if (data.length === 0) {
                container.innerHTML = '<div class="text-center py-5"><i class="fas fa-inbox fa-4x text-muted mb-3"></i><h5 class="text-muted">No notifications found</h5></div>';
                return;
            }
            
            data.forEach(notification => {
                const cardClass = notification.is_global ? 'global' : (notification.is_read ? '' : 'unread');
                const scopeBadge = notification.is_global 
                    ? '<span class="badge bg-purple">Global</span>' 
                    : `<span class="badge bg-info">User: ${notification.user_name}</span>`;
                
                container.innerHTML += `
                    <div class="notification-card ${cardClass}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    <i class="fas ${notification.icon || 'fa-bell'}"></i>
                                    <strong>${notification.title}</strong>
                                    ${scopeBadge}
                                    <span class="badge bg-secondary">${notification.type}</span>
                                </h6>
                                <p class="mb-2">${notification.message}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> ${new Date(notification.created_at).toLocaleString()}
                                </small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog"></i> Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewNotification(${notification.id})">
                                        <i class="fas fa-eye"></i> View Details
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteNotification(${notification.id})">
                                        <i class="fas fa-trash"></i> Delete
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            });
        });
}

// Load notification types
function loadNotificationTypes() {
    fetch('/admin/notifications/types')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('typesContainer');
            container.innerHTML = '';
            
            if (!data.success || data.types.length === 0) {
                container.innerHTML = '<div class="text-center py-5"><i class="fas fa-inbox fa-4x text-muted mb-3"></i><h5 class="text-muted">No notification types found</h5></div>';
                return;
            }
            
            data.types.forEach(type => {
                const disabledClass = type.is_enabled ? '' : 'disabled';
                const enabledBadge = type.is_enabled 
                    ? '<span class="badge bg-success">Enabled</span>' 
                    : '<span class="badge bg-secondary">Disabled</span>';
                const emailBadge = type.email_enabled 
                    ? '<span class="badge bg-info">Email Enabled</span>' 
                    : '<span class="badge bg-secondary">Email Disabled</span>';
                
                container.innerHTML += `
                    <div class="type-card ${disabledClass}">
                        <div class="flex-grow-1">
                            <h6 class="mb-2">
                                <i class="fas ${type.icon}"></i> ${type.label}
                                ${enabledBadge}
                                ${emailBadge}
                            </h6>
                            <p class="mb-0 text-muted">${type.description || 'No description'}</p>
                            <small class="text-muted">Priority: ${type.priority}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm ${type.is_enabled ? 'btn-danger' : 'btn-success'}" 
                                    onclick="toggleNotificationType(${type.id}, ${type.is_enabled})">
                                <i class="fas fa-${type.is_enabled ? 'times' : 'check'}"></i> 
                                ${type.is_enabled ? 'Disable' : 'Enable'}
                            </button>
                            <button class="btn btn-sm ${type.email_enabled ? 'btn-warning' : 'btn-info'} ms-2" 
                                    onclick="toggleEmailForType(${type.id}, ${type.email_enabled})">
                                <i class="fas fa-envelope"></i> 
                                ${type.email_enabled ? 'Disable Email' : 'Enable Email'}
                            </button>
                        </div>
                    </div>
                `;
            });
        });
}

// Show broadcast modal
function showBroadcastModal() {
    document.getElementById('broadcastForm').reset();
    new bootstrap.Modal(document.getElementById('broadcastModal')).show();
}

// Show targeted modal
function showTargetedModal() {
    // Load users
    fetch('/admin/notifications/users')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('targetedUsers');
            select.innerHTML = '';
            
            if (data.success && data.users) {
                data.users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.name} (${user.email})`;
                    select.appendChild(option);
                });
            }
        });
    
    document.getElementById('targetedForm').reset();
    new bootstrap.Modal(document.getElementById('targetedModal')).show();
}

// Show email all modal
function showEmailAllModal() {
    document.getElementById('emailAllForm').reset();
    new bootstrap.Modal(document.getElementById('emailAllModal')).show();
}

// Submit broadcast form
document.getElementById('broadcastForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch('/admin/notifications/broadcast', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            type: document.getElementById('broadcastType').value,
            title: document.getElementById('broadcastTitle').value,
            message: document.getElementById('broadcastMessage').value,
            icon: document.getElementById('broadcastIcon').value || 'fa-bell',
            action_url: document.getElementById('broadcastUrl').value || null
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('broadcastModal')).hide();
            loadNotifications();
            loadStats();
        }
    });
});

// Submit targeted form
document.getElementById('targetedForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const selectedUsers = Array.from(document.getElementById('targetedUsers').selectedOptions)
        .map(option => option.value);
    
    if (selectedUsers.length === 0) {
        alert('Please select at least one user');
        return;
    }
    
    fetch('/admin/notifications/targeted', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            user_ids: selectedUsers,
            type: document.getElementById('targetedType').value,
            title: document.getElementById('targetedTitle').value,
            message: document.getElementById('targetedMessage').value,
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('targetedModal')).hide();
            loadNotifications();
            loadStats();
        }
    });
});

// Submit email all form
document.getElementById('emailAllForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!confirm('Are you sure you want to send email to ALL users? This cannot be undone.')) {
        return;
    }
    
    const btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    fetch('/admin/notifications/email-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            subject: document.getElementById('emailSubject').value,
            message: document.getElementById('emailMessage').value,
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        btn.disabled = false;
        btn.innerHTML = 'Send Email to All';
        
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('emailAllModal')).hide();
            loadNotifications();
            loadStats();
        }
    });
});

function viewNotification(id) {
    fetch(`/admin/notifications/${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const notification = data.notification;
                document.getElementById('notificationDetails').innerHTML = `
                    <div class="mb-3">
                        <strong>Title:</strong> ${notification.title}
                    </div>
                    <div class="mb-3">
                        <strong>Type:</strong> ${notification.type}
                    </div>
                    <div class="mb-3">
                        <strong>Message:</strong> ${notification.message}
                    </div>
                    <div class="mb-3">
                        <strong>Scope:</strong> ${notification.is_global ? 'Global (All Users)' : 'User: ' + notification.user_name}
                    </div>
                    <div class="mb-3">
                        <strong>Date:</strong> ${new Date(notification.created_at).toLocaleString()}
                    </div>
                `;
                new bootstrap.Modal(document.getElementById('viewNotificationModal')).show();
            }
        });
}

function deleteNotification(id) {
    if (confirm('Delete this notification?')) {
        fetch(`/admin/notifications/${id}`, {
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
                loadNotifications();
                loadStats();
            }
        });
    }
}

function toggleNotificationType(id, currentStatus) {
    fetch(`/admin/notifications/types/${id}/toggle`, {
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
            loadNotificationTypes();
        }
    });
}

function toggleEmailForType(id, currentStatus) {
    fetch(`/admin/notifications/types/${id}/toggle-email`, {
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
            loadNotificationTypes();
        }
    });
}

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadNotifications();
    loadNotificationTypes();
    
    // Reload data when switching tabs
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            if (e.target.getAttribute('href') === '#notificationsTab') {
                loadNotifications();
            } else if (e.target.getAttribute('href') === '#typesTab') {
                loadNotificationTypes();
            }
        });
    });
});
</script>
@endsection
