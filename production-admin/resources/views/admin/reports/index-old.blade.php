@extends('admin.layouts.app')

@section('title', 'Reports Management')

@section('styles')
<style>
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
    .stat-box.yellow {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stat-box.green {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .stat-box.orange {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
    #reportsTable thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        border: none;
    }
    #reportsTable tbody tr {
        transition: all 0.2s ease;
    }
    #reportsTable tbody tr:hover {
        background-color: #f0f8ff !important;
        transform: scale(1.01);
    }
    .action-buttons .btn {
        margin: 0 2px;
    }
    .report-modal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .reason-badge {
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
</style>
@endsection

@section('content')
<!-- Statistics Row -->
<div class="row stats-row">
    <div class="col-md-3">
        <div class="stat-box blue" onclick="loadReports('all')">
            <i class="fas fa-flag"></i>
            <h3 id="totalCount">{{ $stats['total'] }}</h3>
            <p>Total Reports</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box yellow" onclick="loadReports('pending')">
            <i class="fas fa-clock"></i>
            <h3 id="pendingCount">{{ $stats['pending'] }}</h3>
            <p>Pending Review</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box green" onclick="loadReports('resolved')">
            <i class="fas fa-check-circle"></i>
            <h3 id="resolvedCount">{{ $stats['resolved'] }}</h3>
            <p>Resolved</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box orange" onclick="loadReports('reviewed')">
            <i class="fas fa-eye"></i>
            <h3 id="reviewedCount">{{ $stats['reviewed'] ?? 0 }}</h3>
            <p>Under Review</p>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="card mb-4">
    <div class="card-body filter-section">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-filter me-2"></i>Status</label>
                <select id="statusFilter" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="reviewed">Reviewed</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-tag me-2"></i>Reason</label>
                <select id="reasonFilter" class="form-select">
                    <option value="">All Reasons</option>
                    <option value="spam">Spam</option>
                    <option value="inappropriate">Inappropriate</option>
                    <option value="fraud">Fraud</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-calendar me-2"></i>Date Range</label>
                <select id="dateFilter" class="form-select">
                    <option value="">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button id="resetFilters" class="btn btn-secondary w-100">
                    <i class="fas fa-redo me-2"></i>Reset Filters
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reports Table -->
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Reports List</h5>
            <button class="btn btn-primary btn-sm" onclick="location.reload()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="reportsTable" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag me-1"></i>ID</th>
                    <th><i class="fas fa-user me-1"></i>Reporter</th>
                    <th><i class="fas fa-box me-1"></i>Listing</th>
                    <th><i class="fas fa-exclamation-triangle me-1"></i>Reason</th>
                    <th><i class="fas fa-align-left me-1"></i>Description</th>
                    <th><i class="fas fa-info-circle me-1"></i>Status</th>
                    <th><i class="fas fa-calendar me-1"></i>Date</th>
                    <th><i class="fas fa-cogs me-1"></i>Actions</th>
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
        table = $('#reportsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.reports.index') }}',
                data: function(d) {
                    d.status = $('#statusFilter').val();
                    d.reason = $('#reasonFilter').val();
                    d.date = $('#dateFilter').val();
                }
            },
            columns: [
                { data: 'id', width: '50px' },
                { 
                    data: 'user',
                    render: (data) => data ? `<strong>${data.name}</strong><br><small class="text-muted">${data.email}</small>` : 'N/A'
                },
                { 
                    data: 'listing',
                    render: (data) => data ? `<a href="/admin/listings/${data.id}" class="text-primary">${data.title}</a>` : '<span class="text-muted">Deleted</span>'
                },
                { 
                    data: 'reason',
                    render: (data) => {
                        const colors = {
                            'spam': 'warning',
                            'inappropriate': 'danger',
                            'fraud': 'dark',
                            'other': 'secondary'
                        };
                        return `<span class="badge bg-${colors[data] || 'secondary'} reason-badge">${data}</span>`;
                    }
                },
                { 
                    data: 'description',
                    render: (data) => data ? (data.length > 50 ? data.substring(0, 50) + '...' : data) : '<span class="text-muted">No description</span>'
                },
                { data: 'status' },
                { 
                    data: 'created_at', 
                    render: (data) => new Date(data).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                },
                { data: 'action', orderable: false, width: '200px' }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            language: {
                processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading reports...'
            }
        });

        // Filter change handlers
        $('#statusFilter, #reasonFilter, #dateFilter').on('change', function() {
            table.ajax.reload();
        });

        // Reset filters
        $('#resetFilters').on('click', function() {
            $('#statusFilter, #reasonFilter, #dateFilter').val('');
            table.ajax.reload();
        });

        // View Report Details
        $(document).on('click', '.view-btn', function() {
            const id = $(this).data('id');
            
            $.get(`/admin/reports/${id}`, function(report) {
                let modal = `
                    <div class="modal fade report-modal" id="reportModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-flag me-2"></i>Report Details #${report.id}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Reporter</label>
                                            <p class="fw-bold">${report.user ? report.user.name : 'N/A'}</p>
                                            <small class="text-muted">${report.user ? report.user.email : ''}</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Listing</label>
                                            <p class="fw-bold">${report.listing ? report.listing.title : 'Deleted'}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Reason</label>
                                            <p><span class="badge bg-warning">${report.reason}</span></p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Status</label>
                                            <p>${report.status === 'resolved' ? 
                                                '<span class="badge bg-success">Resolved</span>' : 
                                                report.status === 'reviewed' ?
                                                '<span class="badge bg-info">Reviewed</span>' :
                                                '<span class="badge bg-warning">Pending</span>'}
                                            </p>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="text-muted small">Description</label>
                                            <p>${report.description || 'No description provided'}</p>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="text-muted small">Reported Date</label>
                                            <p>${new Date(report.created_at).toLocaleString()}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    ${report.status !== 'resolved' ? `
                                    <button type="button" class="btn btn-success" onclick="updateReportStatus(${report.id}, 'resolved')">
                                        <i class="fas fa-check me-2"></i>Mark as Resolved
                                    </button>` : ''}
                                    ${report.status === 'pending' ? `
                                    <button type="button" class="btn btn-info" onclick="updateReportStatus(${report.id}, 'reviewed')">
                                        <i class="fas fa-eye me-2"></i>Mark as Reviewed
                                    </button>` : ''}
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('body').append(modal);
                $('#reportModal').modal('show');
                $('#reportModal').on('hidden.bs.modal', function() {
                    $(this).remove();
                });
            });
        });

        // Load stats
        function loadStats() {
            $.get('{{ route('admin.reports.stats') }}', function(data) {
                $('#totalCount').text(data.total);
                $('#pendingCount').text(data.pending);
                $('#resolvedCount').text(data.resolved);
                $('#reviewedCount').text(data.reviewed);
            });
        }
    });


        // Handle resolve button click from table
        $(document).on('click', '.resolve-btn', function() {
            const id = $(this).data('id');
            const status = $(this).data('status');
            if(confirm('Are you sure you want to mark this report as resolved?')) {
                updateReportStatus(id, status);
            }
        });

        // Handle review button click from table
        $(document).on('click', '.review-btn', function() {
            const id = $(this).data('id');
            const status = $(this).data('status');
            if(confirm('Mark this report as under review?')) {
                updateReportStatus(id, status);
            }
        });
    // Load reports by status
    function loadReports(status) {
        if (status === 'all') {
            $('#statusFilter').val('');
        } else {
            $('#statusFilter').val(status);
        }
        table.ajax.reload();
    }

    // Update report status
    function updateReportStatus(id, status) {
        $.ajax({
            url: `/admin/reports/${id}/status`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                alert(response.message);
                $('#reportModal').modal('hide');
                table.ajax.reload();
                $.get('{{ route('admin.reports.stats') }}', function(data) {
                    $('#totalCount').text(data.total);
                    $('#pendingCount').text(data.pending);
                    $('#resolvedCount').text(data.resolved);
                    $('#reviewedCount').text(data.reviewed);
                });
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
            }
        });
    }
</script>
@endsection
