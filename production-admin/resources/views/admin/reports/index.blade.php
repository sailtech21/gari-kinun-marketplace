@extends('admin.layouts.app')

@section('title', 'Reports Management')

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
    
    .report-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        margin-bottom: 15px;
        border-left: 4px solid #e74c3c;
    }
    
    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .report-card.resolved {
        border-left-color: #2ecc71;
        opacity: 0.7;
    }
    
    .ad-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
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
        <h1 class="h3 mb-0 text-gray-800">🚨 Reports Management</h1>
        <button class="btn btn-secondary" onclick="exportReports()">
            <i class="fas fa-download"></i> Export Reports
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card bg-gradient-primary">
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="number" id="statTotal">0</div>
            <div class="label">Total Reports</div>
        </div>
        <div class="stat-card bg-gradient-warning">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <div class="number" id="statPending">0</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat-card bg-gradient-success">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <div class="number" id="statResolved">0</div>
            <div class="label">Resolved</div>
        </div>
        <div class="stat-card bg-gradient-danger">
            <div class="icon"><i class="fas fa-ban"></i></div>
            <div class="number" id="statReviewed">0</div>
            <div class="label">Under Review</div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#adReportsTab">
                <i class="fas fa-ad"></i> Ad Reports
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#userReportsTab">
                <i class="fas fa-user"></i> User Reports
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Ad Reports Tab -->
        <div class="tab-pane fade show active" id="adReportsTab">
            <div id="adReportsContainer">
                <!-- Ad reports will be loaded here -->
            </div>
        </div>

        <!-- User Reports Tab -->
        <div class="tab-pane fade" id="userReportsTab">
            <div id="userReportsContainer">
                <!-- User reports will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- View Report Modal -->
<div class="modal fade" id="viewReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="reportDetails">
                <!-- Report details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Warn User Modal -->
<div class="modal fade" id="warnUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Warn User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="warnUserForm">
                <input type="hidden" id="warnReportId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Warning Message*</label>
                        <textarea class="form-control" id="warningMessage" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Send Warning</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ban User Modal -->
<div class="modal fade" id="banUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ban User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="banUserForm">
                <input type="hidden" id="banReportId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ban Reason*</label>
                        <textarea class="form-control" id="banReason" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ban Duration*</label>
                        <select class="form-control" id="banDuration" required>
                            <option value="temporary">Temporary</option>
                            <option value="permanent">Permanent</option>
                        </select>
                    </div>
                    <div class="mb-3" id="banUntilContainer" style="display: none;">
                        <label class="form-label">Ban Until</label>
                        <input type="date" class="form-control" id="banUntil">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Ban User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suspend User Modal -->
<div class="modal fade" id="suspendUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Suspend User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="suspendUserForm">
                <input type="hidden" id="suspendReportId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Suspension Days*</label>
                        <input type="number" class="form-control" id="suspensionDays" min="1" max="365" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Suspend User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Load stats
function loadStats() {
    fetch('/admin/reports/stats')
        .then(res => res.json())
        .then(data => {
            document.getElementById('statTotal').textContent = data.total;
            document.getElementById('statPending').textContent = data.pending;
            document.getElementById('statResolved').textContent = data.resolved;
            document.getElementById('statReviewed').textContent = data.reviewed;
        });
}

// Load ad reports
function loadAdReports() {
    fetch('/admin/reports/ads')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('adReportsContainer');
            container.innerHTML = '';
            
            if (data.reports.length === 0) {
                container.innerHTML = '<div class="text-center py-5"><i class="fas fa-inbox fa-4x text-muted mb-3"></i><h5 class="text-muted">No ad reports found</h5></div>';
                return;
            }
            
            data.reports.forEach(report => {
                const statusClass = report.status === 'resolved' ? 'resolved' : '';
                const ad = report.listing;
                const adImage = ad && ad.images && Array.isArray(ad.images) && ad.images.length > 0 
                    ? `<img src="/storage/${ad.images[0]}" class="ad-thumbnail">` 
                    : '<div class="ad-thumbnail bg-light d-flex align-items-center justify-content-center"><i class="fas fa-image text-muted"></i></div>';
                
                const statusBadge = {
                    'pending': '<span class="badge bg-warning">Pending</span>',
                    'reviewed': '<span class="badge bg-info">Reviewed</span>',
                    'resolved': '<span class="badge bg-success">Resolved</span>'
                }[report.status] || '<span class="badge bg-secondary">Unknown</span>';
                
                container.innerHTML += `
                    <div class="report-card ${statusClass}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex gap-3 flex-grow-1">
                                ${adImage}
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">
                                        <strong>Ad:</strong> ${ad ? ad.title : 'N/A'}
                                        ${statusBadge}
                                    </h6>
                                    <p class="mb-2"><strong>Reason:</strong> ${report.reason}</p>
                                    <p class="mb-2 text-truncate"><strong>Description:</strong> ${report.description || 'No description'}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> Reported by: ${report.user ? report.user.name : 'N/A'}
                                        <i class="fas fa-clock ms-3"></i> ${new Date(report.created_at).toLocaleString()}
                                    </small>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog"></i> Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewReport(${report.id})">
                                        <i class="fas fa-eye"></i> View Details
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="removeAd(${report.id})">
                                        <i class="fas fa-trash"></i> Remove Ad
                                    </a></li>
                                    <li><a class="dropdown-item text-warning" href="javascript:void(0)" onclick="warnUser(${report.id})">
                                        <i class="fas fa-exclamation-triangle"></i> Warn User
                                    </a></li>
                                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="banUser(${report.id})">
                                        <i class="fas fa-ban"></i> Ban User
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="dismissReport(${report.id})">
                                        <i class="fas fa-times"></i> Dismiss Report
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            });
        });
}

// Load user reports
function loadUserReports() {
    fetch('/admin/reports/users')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('userReportsContainer');
            container.innerHTML = '';
            
            if (data.reports.length === 0) {
                container.innerHTML = '<div class="text-center py-5"><i class="fas fa-inbox fa-4x text-muted mb-3"></i><h5 class="text-muted">No user reports found</h5></div>';
                return;
            }
            
            data.reports.forEach(report => {
                const statusClass = report.status === 'resolved' ? 'resolved' : '';
                const reportedUser = report.reported_user;
                
                const statusBadge = {
                    'pending': '<span class="badge bg-warning">Pending</span>',
                    'reviewed': '<span class="badge bg-info">Reviewed</span>',
                    'resolved': '<span class="badge bg-success">Resolved</span>'
                }[report.status] || '<span class="badge bg-secondary">Unknown</span>';
                
                container.innerHTML += `
                    <div class="report-card ${statusClass}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                    <strong>User:</strong> ${reportedUser ? reportedUser.name : 'N/A'}
                                    ${statusBadge}
                                </h6>
                                <p class="mb-2"><strong>Reason:</strong> ${report.reason}</p>
                                <p class="mb-2"><strong>Description:</strong> ${report.description || 'No description'}</p>
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> Reported by: ${report.user ? report.user.name : 'N/A'}
                                    <i class="fas fa-clock ms-3"></i> ${new Date(report.created_at).toLocaleString()}
                                </small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog"></i> Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewReport(${report.id})">
                                        <i class="fas fa-eye"></i> View Details
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-warning" href="javascript:void(0)" onclick="suspendUser(${report.id})">
                                        <i class="fas fa-user-clock"></i> Suspend User
                                    </a></li>
                                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="banUser(${report.id})">
                                        <i class="fas fa-ban"></i> Ban User
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="dismissReport(${report.id})">
                                        <i class="fas fa-times"></i> Dismiss Report
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            });
        });
}

function viewReport(id) {
    fetch(`/admin/reports/${id}`)
        .then(res => res.json())
        .then(report => {
            const details = document.getElementById('reportDetails');
            details.innerHTML = `
                <div class="mb-3">
                    <strong>Reporter:</strong> ${report.user ? report.user.name : 'N/A'}
                </div>
                <div class="mb-3">
                    <strong>Reported:</strong> ${report.report_type === 'listing' ? 
                        (report.listing ? report.listing.title : 'N/A') : 
                        (report.reported_user ? report.reported_user.name : 'N/A')}
                </div>
                <div class="mb-3">
                    <strong>Reason:</strong> ${report.reason}
                </div>
                <div class="mb-3">
                    <strong>Description:</strong> ${report.description || 'No description provided'}
                </div>
                <div class="mb-3">
                    <strong>Status:</strong> <span class="badge bg-${report.status === 'resolved' ? 'success' : 'warning'}">${report.status}</span>
                </div>
                <div class="mb-3">
                    <strong>Date:</strong> ${new Date(report.created_at).toLocaleString()}
                </div>
            `;
            new bootstrap.Modal(document.getElementById('viewReportModal')).show();
        });
}

function removeAd(reportId) {
    if (confirm('Remove this ad? This action cannot be undone.')) {
        fetch(`/admin/reports/${reportId}/remove-ad`, {
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
                loadAdReports();
                loadStats();
            }
        });
    }
}

function warnUser(reportId) {
    document.getElementById('warnReportId').value = reportId;
    new bootstrap.Modal(document.getElementById('warnUserModal')).show();
}

document.getElementById('warnUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const reportId = document.getElementById('warnReportId').value;
    
    fetch(`/admin/reports/${reportId}/warn-user`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            warning_message: document.getElementById('warningMessage').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('warnUserModal')).hide();
            loadAdReports();
            loadStats();
        }
    });
});

function banUser(reportId) {
    document.getElementById('banReportId').value = reportId;
    new bootstrap.Modal(document.getElementById('banUserModal')).show();
}

document.getElementById('banDuration').addEventListener('change', function() {
    document.getElementById('banUntilContainer').style.display = 
        this.value === 'temporary' ? 'block' : 'none';
});

document.getElementById('banUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const reportId = document.getElementById('banReportId').value;
    
    const data = {
        ban_reason: document.getElementById('banReason').value,
        ban_duration: document.getElementById('banDuration').value
    };
    
    if (data.ban_duration === 'temporary') {
        data.ban_until = document.getElementById('banUntil').value;
    }
    
    fetch(`/admin/reports/${reportId}/ban-user`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('banUserModal')).hide();
            loadAdReports();
            loadUserReports();
            loadStats();
        }
    });
});

function suspendUser(reportId) {
    document.getElementById('suspendReportId').value = reportId;
    new bootstrap.Modal(document.getElementById('suspendUserModal')).show();
}

document.getElementById('suspendUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const reportId = document.getElementById('suspendReportId').value;
    
    fetch(`/admin/reports/${reportId}/suspend-user`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            suspension_days: document.getElementById('suspensionDays').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('suspendUserModal')).hide();
            loadUserReports();
            loadStats();
        }
    });
});

function dismissReport(reportId) {
    if (confirm('Dismiss this report?')) {
        fetch(`/admin/reports/${reportId}/dismiss`, {
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
                loadAdReports();
                loadUserReports();
                loadStats();
            }
        });
    }
}

function exportReports() {
    window.open('/admin/reports/export?format=csv', '_blank');
}

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadAdReports();
    loadUserReports();
    
    // Reload data when switching tabs
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            if (e.target.getAttribute('href') === '#adReportsTab') {
                loadAdReports();
            } else if (e.target.getAttribute('href') === '#userReportsTab') {
                loadUserReports();
            }
        });
    });
});
</script>
@endsection
