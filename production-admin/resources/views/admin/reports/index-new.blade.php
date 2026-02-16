@extends('admin.layouts.app')

@section('title', 'Reports Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-flag text-danger me-2"></i>Reports Management</h2>
            <p class="text-muted mb-0">Manage user-reported listings</p>
        </div>
        <div>
            <button class="btn btn-outline-primary me-2" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
            <button class="btn btn-success" onclick="exportReports()">
                <i class="fas fa-download me-1"></i>Export CSV
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card total-card" onclick="filterByStatus('all')">
                <div class="stat-icon">
                    <i class="fas fa-flag"></i>
                </div>
                <div class="stat-content">
                    <h3 id="totalCount">0</h3>
                    <p>Total Reports</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card pending-card" onclick="filterByStatus('pending')">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3 id="pendingCount">0</h3>
                    <p>Pending Review</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card reviewed-card" onclick="filterByStatus('reviewed')">
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-content">
                    <h3 id="reviewedCount">0</h3>
                    <p>Under Review</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card resolved-card" onclick="filterByStatus('resolved')">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 id="resolvedCount">0</h3>
                    <p>Resolved</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-search me-1"></i>Search</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search listing title, reporter name...">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-filter me-1"></i>Reason</label>
                    <select class="form-select" id="reasonFilter">
                        <option value="">All Reasons</option>
                        <option value="spam">Spam</option>
                        <option value="inappropriate">Inappropriate Content</option>
                        <option value="fraud">Fraud/Scam</option>
                        <option value="duplicate">Duplicate Listing</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-list me-1"></i>Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <button class="btn btn-secondary w-100" onclick="clearFilters()">
                        <i class="fas fa-times me-1"></i>Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div id="reportsContainer" class="row g-3">
        <!-- Reports will be loaded here -->
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center py-5" style="display: none;">
        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Reports Found</h4>
        <p class="text-muted">There are no reports matching your criteria.</p>
    </div>
</div>

<!-- Report Details Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-flag me-2"></i>Report Details</h5>
                <button type="button" class="btn-close" onclick="closeReportModal()"></button>
            </div>
            <div class="modal-body" id="reportModalBody">
                <!-- Content will be loaded here -->
            </div>
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
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 2px solid transparent;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.stat-card.active {
    border-color: #007bff;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-card.active .stat-content h3,
.stat-card.active .stat-content p {
    color: white;
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
.pending-card { border-left: 4px solid #f39c12; }
.reviewed-card { border-left: 4px solid #9b59b6; }
.resolved-card { border-left: 4px solid #27ae60; }

.total-card .stat-icon { color: #3498db; }
.pending-card .stat-icon { color: #f39c12; }
.reviewed-card .stat-icon { color: #9b59b6; }
.resolved-card .stat-icon { color: #27ae60; }

/* Report Card */
.report-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 4px solid #e74c3c;
}

.report-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 12px;
}

.listing-title {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.report-meta {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 12px;
    font-size: 13px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #7f8c8d;
}

.meta-item i {
    width: 14px;
    text-align: center;
}

.reason-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.reason-spam { background: #fee; color: #e74c3c; }
.reason-inappropriate { background: #fef3e8; color: #f39c12; }
.reason-fraud { background: #ffe6f0; color: #e91e63; }
.reason-duplicate { background: #e8f3fe; color: #2196f3; }
.reason-other { background: #f0f0f0; color: #666; }

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-reviewed { background: #cfe2ff; color: #084298; }
.status-resolved { background: #d1e7dd; color: #0a3622; }

.report-description {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    font-size: 14px;
    color: #495057;
    margin: 12px 0;
    border-left: 3px solid #dee2e6;
    line-height: 1.6;
}

.report-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
    border-radius: 6px;
    font-weight: 500;
}

/* Modal */
.modal-header {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
}

.modal-body .info-row {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.modal-body .info-row:last-child {
    border-bottom: none;
}

.modal-body .info-label {
    font-weight: 600;
    color: #7f8c8d;
    width: 140px;
    flex-shrink: 0;
}

.modal-body .info-value {
    color: #2c3e50;
    flex: 1;
}

/* Form Controls */
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 8px 12px;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.report-card {
    animation: fadeIn 0.3s ease;
}
</style>

<script>
let allReports = [];
let filteredReports = [];
let currentFilter = 'all';

// Load reports on page load
document.addEventListener('DOMContentLoaded', function() {
    loadReports();
    
    // Search with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 300);
    });
    
    // Filter listeners
    document.getElementById('reasonFilter').addEventListener('change', applyFilters);
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
});

function loadReports() {
    fetch('/admin/reports/all')
        .then(response => response.json())
        .then(data => {
            allReports = data.reports;
            filteredReports = allReports;
            updateStats();
            renderReports();
        })
        .catch(error => {
            console.error('Error loading reports:', error);
            showError('Failed to load reports');
        });
}

function updateStats() {
    const total = allReports.length;
    const pending = allReports.filter(r => r.status === 'pending').length;
    const reviewed = allReports.filter(r => r.status === 'reviewed').length;
    const resolved = allReports.filter(r => r.status === 'resolved').length;
    
    document.getElementById('totalCount').textContent = total;
    document.getElementById('pendingCount').textContent = pending;
    document.getElementById('reviewedCount').textContent = reviewed;
    document.getElementById('resolvedCount').textContent = resolved;
}

function filterByStatus(status) {
    currentFilter = status;
    
    // Update active card
    document.querySelectorAll('.stat-card').forEach(card => card.classList.remove('active'));
    if (status === 'all') {
        document.querySelector('.total-card').classList.add('active');
    } else if (status === 'pending') {
        document.querySelector('.pending-card').classList.add('active');
    } else if (status === 'reviewed') {
        document.querySelector('.reviewed-card').classList.add('active');
    } else if (status === 'resolved') {
        document.querySelector('.resolved-card').classList.add('active');
    }
    
    document.getElementById('statusFilter').value = status === 'all' ? '' : status;
    applyFilters();
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const reasonFilter = document.getElementById('reasonFilter').value;
    const statusFilter = document.getElementById('statusFilter').value || currentFilter;
    
    filteredReports = allReports.filter(report => {
        // Search filter
        const listingTitle = report.listing?.title?.toLowerCase() || '';
        const reporterName = report.user?.name?.toLowerCase() || '';
        const listingOwner = report.listing?.user?.name?.toLowerCase() || '';
        const searchMatch = !searchTerm || 
            listingTitle.includes(searchTerm) || 
            reporterName.includes(searchTerm) ||
            listingOwner.includes(searchTerm);
        
        // Reason filter
        const reasonMatch = !reasonFilter || report.reason === reasonFilter;
        
        // Status filter
        const statusMatch = !statusFilter || statusFilter === 'all' || report.status === statusFilter;
        
        return searchMatch && reasonMatch && statusMatch;
    });
    
    renderReports();
}

function renderReports() {
    const container = document.getElementById('reportsContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (filteredReports.length === 0) {
        container.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }
    
    container.style.display = 'flex';
    emptyState.style.display = 'none';
    
    container.innerHTML = filteredReports.map(report => renderReportCard(report)).join('');
}

function renderReportCard(report) {
    const listingTitle = report.listing?.title || 'Deleted Listing';
    const reporterName = report.user?.name || 'Unknown User';
    const ownerName = report.listing?.user?.name || 'Unknown Owner';
    const reasonText = formatReason(report.reason);
    const statusClass = `status-${report.status}`;
    const reasonClass = `reason-${report.reason}`;
    const date = new Date(report.created_at).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric'
    });
    
    return `
        <div class="col-md-6 col-lg-4">
            <div class="report-card">
                <div class="report-header">
                    <div class="flex-grow-1">
                        <div class="listing-title">${listingTitle}</div>
                        <span class="reason-badge ${reasonClass}">${reasonText}</span>
                    </div>
                    <span class="status-badge ${statusClass}">${report.status}</span>
                </div>
                
                <div class="report-meta">
                    <div class="meta-item">
                        <i class="fas fa-user"></i>
                        <span>Reporter: ${reporterName}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-user-tie"></i>
                        <span>Owner: ${ownerName}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>${date}</span>
                    </div>
                </div>
                
                <div class="report-description">
                    "${report.description}"
                </div>
                
                <div class="report-actions">
                    <button class="btn btn-sm btn-primary" onclick="viewReport(${report.id})">
                        <i class="fas fa-eye me-1"></i>View Details
                    </button>
                    ${report.status === 'pending' ? `
                        <button class="btn btn-sm btn-info" onclick="updateReportStatus(${report.id}, 'reviewed')">
                            <i class="fas fa-search me-1"></i>Review
                        </button>
                    ` : ''}
                    ${report.status !== 'resolved' ? `
                        <button class="btn btn-sm btn-success" onclick="updateReportStatus(${report.id}, 'resolved')">
                            <i class="fas fa-check me-1"></i>Resolve
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

function formatReason(reason) {
    const reasons = {
        'spam': 'Spam',
        'inappropriate': 'Inappropriate',
        'fraud': 'Fraud/Scam',
        'duplicate': 'Duplicate',
        'other': 'Other'
    };
    return reasons[reason] || reason;
}

function viewReport(id) {
    const report = allReports.find(r => r.id === id);
    if (!report) return;
    
    const modalBody = document.getElementById('reportModalBody');
    const listingTitle = report.listing?.title || 'Deleted Listing';
    const reporterName = report.user?.name || 'Unknown User';
    const reporterEmail = report.user?.email || 'N/A';
    const ownerName = report.listing?.user?.name || 'Unknown Owner';
    const ownerEmail = report.listing?.user?.email || 'N/A';
    const reasonText = formatReason(report.reason);
    const date = new Date(report.created_at).toLocaleString();
    
    modalBody.innerHTML = `
        <div class="info-row">
            <div class="info-label">Report ID:</div>
            <div class="info-value">#${report.id}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Listing:</div>
            <div class="info-value">
                <strong>${listingTitle}</strong>
                ${report.listing ? `<br><small class="text-muted">Listing ID: ${report.listing.id}</small>` : ''}
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Listing Owner:</div>
            <div class="info-value">
                ${ownerName}<br>
                <small class="text-muted">${ownerEmail}</small>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Reported By:</div>
            <div class="info-value">
                ${reporterName}<br>
                <small class="text-muted">${reporterEmail}</small>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Reason:</div>
            <div class="info-value"><span class="reason-badge reason-${report.reason}">${reasonText}</span></div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value"><span class="status-badge status-${report.status}">${report.status}</span></div>
        </div>
        <div class="info-row">
            <div class="info-label">Description:</div>
            <div class="info-value">${report.description}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Reported At:</div>
            <div class="info-value">${date}</div>
        </div>
        <div class="mt-3">
            ${report.status === 'pending' ? `
                <button class="btn btn-info" onclick="closeReportModalAndUpdate(${report.id}, 'reviewed')">
                    <i class="fas fa-search me-1"></i>Mark as Reviewed
                </button>
            ` : ''}
            ${report.status !== 'resolved' ? `
                <button class="btn btn-success" onclick="closeReportModalAndUpdate(${report.id}, 'resolved')">
                    <i class="fas fa-check me-1"></i>Mark as Resolved
                </button>
            ` : ''}
            ${report.listing ? `
                <a href="/admin/listings/${report.listing.id}" class="btn btn-primary" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i>View Listing
                </a>
            ` : ''}
        </div>
    `;
    
    $('#reportModal').modal('show');
}

function closeReportModal() {
    document.activeElement.blur();
    $('#reportModal').modal('hide');
}

function closeReportModalAndUpdate(id, status) {
    document.activeElement.blur();
    $('#reportModal').one('hidden.bs.modal', function() {
        updateReportStatus(id, status);
    });
    $('#reportModal').modal('hide');
}

function updateReportStatus(id, status) {
    if (!confirm(`Are you sure you want to mark this report as ${status}?`)) {
        return;
    }
    
    fetch(`/admin/reports/${id}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(`Report marked as ${status}`);
            loadReports();
        } else {
            showError('Failed to update report status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to update report status');
    });
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('reasonFilter').value = '';
    document.getElementById('statusFilter').value = '';
    currentFilter = 'all';
    
    document.querySelectorAll('.stat-card').forEach(card => card.classList.remove('active'));
    document.querySelector('.total-card').classList.add('active');
    
    applyFilters();
}

function exportReports() {
    window.location.href = '/admin/reports/export?format=csv';
}

function showSuccess(message) {
    // You can use your preferred notification library
    alert(message);
}

function showError(message) {
    // You can use your preferred notification library
    alert(message);
}
</script>
@endsection
