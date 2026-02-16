@extends('admin.layouts.app')

@section('title', 'Dealers Management')

@section('styles')
<style>
    * { box-sizing: border-box; }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .stat-card.active {
        transform: translateY(-5px);
        box-shadow: 0 0 0 3px rgba(255,255,255,0.5);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }
    
    .stat-card.blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-card.orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .stat-card.green { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .stat-card.red { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); }
    
    .stat-card .icon {
        font-size: 2.5rem;
        opacity: 0.3;
        margin-bottom: 10px;
    }
    
    .stat-card .number {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 10px 0 5px;
        position: relative;
        z-index: 1;
    }
    
    .stat-card .label {
        font-size: 0.95rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }
    
    .controls-bar {
        background: white;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .search-box {
        position: relative;
    }
    
    .search-box input {
        padding-left: 45px;
        border-radius: 25px;
        border: 2px solid #e9ecef;
        transition: all 0.3s;
    }
    
    .search-box input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
    
    .search-box i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    
    .dealers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .dealer-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
        border: 2px solid transparent;
    }
    
    .dealer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        border-color: #667eea;
    }
    
    .dealer-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .dealer-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8rem;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
        position: relative;
    }
    
    .dealer-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .dealer-avatar .verified-badge {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #43e97b;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid white;
        font-size: 0.7rem;
    }
    
    .dealer-info {
        flex: 1;
        min-width: 0;
    }
    
    .dealer-name {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0 0 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .dealer-business {
        font-size: 0.9rem;
        color: #667eea;
        font-weight: 600;
        margin-bottom: 3px;
    }
    
    .dealer-email {
        font-size: 0.85rem;
        color: #718096;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .dealer-id {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #f0f0f0;
        color: #666;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-badge.active {
        background: #d1f2eb;
        color: #0d6654;
    }
    
    .status-badge.rejected {
        background: #f8d7da;
        color: #721c24;
    }
    
    .dealer-details {
        margin-bottom: 15px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 0.9rem;
    }
    
    .detail-label {
        color: #6c757d;
        font-weight: 500;
    }
    
    .detail-value {
        color: #2d3748;
        font-weight: 600;
        text-align: right;
    }
    
    .dealer-docs {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .doc-badge {
        background: #f8f9fa;
        padding: 8px;
        border-radius: 8px;
        text-align: center;
        font-size: 0.75rem;
        border: 1px solid #e9ecef;
    }
    
    .doc-badge.verified {
        background: #d1f2eb;
        border-color: #43e97b;
        color: #0d6654;
    }
    
    .doc-badge i {
        display: block;
        font-size: 1.2rem;
        margin-bottom: 3px;
    }
    
    .dealer-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    
    .dealer-actions.pending {
        grid-template-columns: 1fr 1fr 1fr;
    }
    
    .btn-action {
        padding: 10px;
        border-radius: 8px;
        border: none;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }
    
    .btn-approve {
        background: #43e97b;
        color: white;
    }
    
    .btn-reject {
        background: #ff6b6b;
        color: white;
    }
    
    .btn-view {
        background: #4facfe;
        color: white;
    }
    
    .btn-delete {
        background: #e74c3c;
        color: white;
    }
    
    .btn-docs {
        background: #ffc107;
        color: white;
    }
    
    .bulk-actions {
        background: #667eea;
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: none;
        align-items: center;
        gap: 15px;
    }
    
    .bulk-actions.show {
        display: flex;
    }
    
    .bulk-actions button {
        background: white;
        color: #667eea;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .bulk-actions button:hover {
        transform: scale(1.05);
    }
    
    .select-checkbox {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        opacity: 0.3;
        margin-bottom: 20px;
    }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.9);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    
    .loading-overlay.show {
        display: flex;
    }
    
    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .dealers-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card blue" data-filter="all">
        <div class="icon"><i class="fas fa-store"></i></div>
        <div class="number" id="totalDealers">{{ $stats['pending'] + $stats['active'] + $stats['rejected'] }}</div>
        <div class="label">Total Dealers</div>
    </div>
    
    <div class="stat-card orange" data-filter="pending">
        <div class="icon"><i class="fas fa-clock"></i></div>
        <div class="number" id="pendingDealers">{{ $stats['pending'] }}</div>
        <div class="label">Pending Approval</div>
    </div>
    
    <div class="stat-card green" data-filter="active">
        <div class="icon"><i class="fas fa-check-circle"></i></div>
        <div class="number" id="activeDealers">{{ $stats['active'] }}</div>
        <div class="label">Active Dealers</div>
    </div>
    
    <div class="stat-card red" data-filter="rejected">
        <div class="icon"><i class="fas fa-times-circle"></i></div>
        <div class="number" id="rejectedDealers">{{ $stats['rejected'] }}</div>
        <div class="label">Rejected</div>
    </div>
</div>

<!-- Controls -->
<div class="controls-bar">
    <div class="row align-items-center">
        <div class="col-md-5 mb-2 mb-md-0">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Search dealers, business, phone...">
            </div>
        </div>
        
        <div class="col-md-2 mb-2 mb-md-0">
            <select id="statusFilter" class="form-select" style="border-radius: 25px; border: 2px solid #e9ecef;">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        
        <div class="col-md-3 mb-2 mb-md-0">
            <select id="sortBy" class="form-select" style="border-radius: 25px; border: 2px solid #e9ecef;">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="name">Name A-Z</option>
                <option value="listings">Most Listings</option>
            </select>
        </div>
        
        <div class="col-md-2">
            <button id="resetBtn" class="btn btn-outline-secondary w-100" style="border-radius: 25px;">
                <i class="fas fa-redo me-1"></i>Reset
            </button>
        </div>
    </div>
</div>

<!-- Bulk Actions -->
<div class="bulk-actions" id="bulkActions">
    <span><strong id="selectedCount">0</strong> dealers selected</span>
    <button onclick="bulkApprove()"><i class="fas fa-check me-1"></i>Approve All</button>
    <button onclick="bulkReject()"><i class="fas fa-times me-1"></i>Reject All</button>
    <button onclick="bulkDelete()"><i class="fas fa-trash me-1"></i>Delete All</button>
    <button onclick="clearSelection()"><i class="fas fa-ban me-1"></i>Clear</button>
</div>

<!-- Dealers Grid -->
<div id="dealersGrid" class="dealers-grid"></div>

<!-- Empty State -->
<div id="emptyState" class="empty-state" style="display: none;">
    <i class="fas fa-store-slash"></i>
    <h4>No dealers found</h4>
    <p>Try adjusting your search or filters</p>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>
@endsection

@section('scripts')
<script>
let allDealers = [];
let filteredDealers = [];
let selectedDealers = new Set();
let currentFilter = 'all';

$(document).ready(function() {
    loadDealers();
    
    // Stat card click to filter
    $('.stat-card').on('click', function() {
        $('.stat-card').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        applyFilters();
    });
    
    // Search
    $('#searchInput').on('input', debounce(function() {
        applyFilters();
    }, 300));
    
    // Filters
    $('#statusFilter, #sortBy').on('change', function() {
        applyFilters();
    });
    
    // Reset
    $('#resetBtn').on('click', function() {
        $('#searchInput').val('');
        $('#statusFilter').val('');
        $('#sortBy').val('newest');
        $('.stat-card').removeClass('active');
        currentFilter = 'all';
        applyFilters();
    });
});

function loadDealers() {
    showLoading();
    
    $.get('/admin/dealers/all', function(response) {
        allDealers = response.dealers;
        updateStats();
        applyFilters();
        hideLoading();
    }).fail(function() {
        hideLoading();
        showToast('Failed to load dealers', 'error');
    });
}

function updateStats() {
    const total = allDealers.length;
    const pending = allDealers.filter(d => d.status === 'pending').length;
    const active = allDealers.filter(d => d.status === 'active').length;
    const rejected = allDealers.filter(d => d.status === 'rejected').length;
    
    $('#totalDealers').text(total);
    $('#pendingDealers').text(pending);
    $('#activeDealers').text(active);
    $('#rejectedDealers').text(rejected);
}

function applyFilters() {
    let dealers = [...allDealers];
    
    // Stat card filter
    if (currentFilter === 'pending') {
        dealers = dealers.filter(d => d.status === 'pending');
    } else if (currentFilter === 'active') {
        dealers = dealers.filter(d => d.status === 'active');
    } else if (currentFilter === 'rejected') {
        dealers = dealers.filter(d => d.status === 'rejected');
    }
    
    // Search filter
    const search = $('#searchInput').val().toLowerCase();
    if (search) {
        dealers = dealers.filter(d => 
            d.name.toLowerCase().includes(search) ||
            (d.business_name && d.business_name.toLowerCase().includes(search)) ||
            (d.email && d.email.toLowerCase().includes(search)) ||
            (d.phone && d.phone.includes(search)) ||
            (d.user && d.user.name.toLowerCase().includes(search))
        );
    }
    
    // Status filter
    const statusFilter = $('#statusFilter').val();
    if (statusFilter) {
        dealers = dealers.filter(d => d.status === statusFilter);
    }
    
    // Sort
    const sortBy = $('#sortBy').val();
    if (sortBy === 'newest') {
        dealers.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    } else if (sortBy === 'oldest') {
        dealers.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
    } else if (sortBy === 'name') {
        dealers.sort((a, b) => a.name.localeCompare(b.name));
    } else if (sortBy === 'listings') {
        dealers.sort((a, b) => b.listings_count - a.listings_count);
    }
    
    filteredDealers = dealers;
    renderDealers();
}

function renderDealers() {
    if (filteredDealers.length === 0) {
        $('#dealersGrid').hide();
        $('#emptyState').show();
        return;
    }
    
    $('#dealersGrid').show();
    $('#emptyState').hide();
    
    const html = filteredDealers.map(dealer => renderDealerCard(dealer)).join('');
    $('#dealersGrid').html(html);
}

function renderDealerCard(dealer) {
    const initial = dealer.name.charAt(0).toUpperCase();
    const statusClass = dealer.status;
    const statusText = dealer.status.charAt(0).toUpperCase() + dealer.status.slice(1);
    
    const joined = new Date(dealer.created_at).toLocaleDateString('en-US', { 
        year: 'numeric', month: 'short', day: 'numeric' 
    });
    
    const verifiedBadge = dealer.is_verified ? '<div class="verified-badge"><i class="fas fa-check"></i></div>' : '';
    
    const docs = {
        license: dealer.business_license ? 'verified' : '',
        nid: (dealer.nid_front && dealer.nid_back) ? 'verified' : '',
        selfie: dealer.selfie_photo ? 'verified' : ''
    };
    
    const isPending = dealer.status === 'pending';
    const actionsClass = isPending ? 'pending' : '';
    
    const approveBtn = isPending ? `<button class="btn-action btn-approve" onclick="approveDealer(${dealer.id})">
        <i class="fas fa-check"></i> Approve
    </button>` : '';
    
    const rejectBtn = isPending ? `<button class="btn-action btn-reject" onclick="rejectDealer(${dealer.id})">
        <i class="fas fa-times"></i> Reject
    </button>` : '';
    
    const isSelected = selectedDealers.has(dealer.id) ? 'selected' : '';
    
    return `
        <div class="dealer-card ${isSelected}" data-dealer-id="${dealer.id}">
            <input type="checkbox" class="select-checkbox" onchange="toggleSelect(${dealer.id})" ${selectedDealers.has(dealer.id) ? 'checked' : ''}>
            <span class="dealer-id">#${dealer.id}</span>
            
            <div class="dealer-header">
                <div class="dealer-avatar">
                    ${dealer.selfie_photo ? `<img src="/storage/${dealer.selfie_photo}" alt="${dealer.name}">` : initial}
                    ${verifiedBadge}
                </div>
                <div class="dealer-info">
                    <div class="dealer-name">${dealer.name}</div>
                    ${dealer.business_name ? `<div class="dealer-business"><i class="fas fa-briefcase"></i> ${dealer.business_name}</div>` : ''}
                    <div class="dealer-email">${dealer.email || (dealer.user ? dealer.user.email : 'N/A')}</div>
                </div>
            </div>
            
            <div class="dealer-details">
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value"><span class="status-badge ${statusClass}">${statusText}</span></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-phone"></i> Phone</span>
                    <span class="detail-value">${dealer.phone || 'N/A'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-list"></i> Listings</span>
                    <span class="detail-value"><strong>${dealer.listings_count || 0}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-calendar"></i> Applied</span>
                    <span class="detail-value">${joined}</span>
                </div>
            </div>
            
            <div class="dealer-docs">
                <div class="doc-badge ${docs.license}">
                    <i class="fas fa-file-contract"></i>
                    <span>License</span>
                </div>
                <div class="doc-badge ${docs.nid}">
                    <i class="fas fa-id-card"></i>
                    <span>NID</span>
                </div>
                <div class="doc-badge ${docs.selfie}">
                    <i class="fas fa-portrait"></i>
                    <span>Selfie</span>
                </div>
            </div>
            
            <div class="dealer-actions ${actionsClass}">
                ${approveBtn}
                ${rejectBtn}
                <button class="btn-action btn-view" onclick="viewDealer(${dealer.id})">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="btn-action btn-docs" onclick="viewDocs(${dealer.id})">
                    <i class="fas fa-folder-open"></i> Docs
                </button>
                <button class="btn-action btn-delete" onclick="deleteDealer(${dealer.id})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    `;
}

function toggleSelect(dealerId) {
    if (selectedDealers.has(dealerId)) {
        selectedDealers.delete(dealerId);
    } else {
        selectedDealers.add(dealerId);
    }
    
    updateBulkActions();
    $(`.dealer-card[data-dealer-id="${dealerId}"]`).toggleClass('selected', selectedDealers.has(dealerId));
}

function clearSelection() {
    selectedDealers.clear();
    $('.select-checkbox').prop('checked', false);
    $('.dealer-card').removeClass('selected');
    updateBulkActions();
}

function updateBulkActions() {
    const count = selectedDealers.size;
    $('#selectedCount').text(count);
    
    if (count > 0) {
        $('#bulkActions').addClass('show');
    } else {
        $('#bulkActions').removeClass('show');
    }
}

function approveDealer(id) {
    if (!confirm('Approve this dealer? They will get verified status.')) return;
    
    showLoading();
    
    $.ajax({
        url: `/admin/dealers/${id}/approve`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            showToast('Dealer approved successfully!', 'success');
            loadDealers();
        },
        error: function() {
            hideLoading();
            showToast('Failed to approve dealer', 'error');
        }
    });
}

function rejectDealer(id) {
    if (!confirm('Reject this dealer application?')) return;
    
    showLoading();
    
    $.ajax({
        url: `/admin/dealers/${id}/reject`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            showToast('Dealer rejected', 'warning');
            loadDealers();
        },
        error: function() {
            hideLoading();
            showToast('Failed to reject dealer', 'error');
        }
    });
}

function deleteDealer(id) {
    if (!confirm('Delete this dealer? This cannot be undone!')) return;
    
    showLoading();
    
    $.ajax({
        url: `/admin/dealers/${id}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            showToast('Dealer deleted successfully', 'success');
            loadDealers();
        },
        error: function() {
            hideLoading();
            showToast('Failed to delete dealer', 'error');
        }
    });
}

function viewDealer(id) {
    const dealer = allDealers.find(d => d.id === id);
    if (!dealer) return;
    
    const statusClass = dealer.status;
    const statusText = dealer.status.charAt(0).toUpperCase() + dealer.status.slice(1);
    
    const modal = `
        <div class="modal fade" id="dealerModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-store me-2"></i>${dealer.name} - Dealer Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Name</label>
                                <p class="fw-bold">${dealer.name}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Email</label>
                                <p class="fw-bold">${dealer.email || 'N/A'}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Phone</label>
                                <p class="fw-bold">${dealer.phone || 'N/A'}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Status</label>
                                <p><span class="status-badge ${statusClass}">${statusText}</span></p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="text-muted small">Business Name</label>
                                <p class="fw-bold">${dealer.business_name || 'N/A'}</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="text-muted small">Business Address</label>
                                <p>${dealer.business_address || 'N/A'}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Business Phone</label>
                                <p class="fw-bold">${dealer.business_phone || 'N/A'}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Total Listings</label>
                                <p class="fw-bold">${dealer.listings_count || 0}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Applied At</label>
                                <p>${new Date(dealer.created_at).toLocaleString()}</p>
                            </div>
                            ${dealer.approved_at ? `
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Approved At</label>
                                <p>${new Date(dealer.approved_at).toLocaleString()}</p>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning" onclick="viewDocs(${dealer.id})">
                            <i class="fas fa-folder-open me-1"></i>View Documents
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const $modal = $(modal);
    $('body').append($modal);
    $modal.modal('show');
    $modal.on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function viewDocs(id) {
    const dealer = allDealers.find(d => d.id === id);
    if (!dealer) return;
    
    $('#dealerModal').modal('hide');
    
    const docs = `
        <div class="modal fade" id="docsModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-folder-open me-2"></i>${dealer.name} - Documents</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row">
                            ${dealer.business_license ? `
                            <div class="col-md-6 mb-3">
                                <h6>Business License</h6>
                                <img src="/storage/${dealer.business_license}" class="img-fluid border rounded" alt="License">
                            </div>
                            ` : '<div class="col-md-6 mb-3"><p class="text-muted">No business license uploaded</p></div>'}
                            
                            ${dealer.nid_front ? `
                            <div class="col-md-6 mb-3">
                                <h6>NID Front</h6>
                                <img src="/storage/${dealer.nid_front}" class="img-fluid border rounded" alt="NID Front">
                            </div>
                            ` : ''}
                            
                            ${dealer.nid_back ? `
                            <div class="col-md-6 mb-3">
                                <h6>NID Back</h6>
                                <img src="/storage/${dealer.nid_back}" class="img-fluid border rounded" alt="NID Back">
                            </div>
                            ` : ''}
                            
                            ${dealer.selfie_photo ? `
                            <div class="col-md-6 mb-3">
                                <h6>Selfie Photo</h6>
                                <img src="/storage/${dealer.selfie_photo}" class="img-fluid border rounded" alt="Selfie">
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        ${dealer.status === 'pending' ? `
                        <button type="button" class="btn btn-success" onclick="$('#docsModal').modal('hide'); approveDealer(${dealer.id})">
                            <i class="fas fa-check me-1"></i>Approve Dealer
                        </button>
                        <button type="button" class="btn btn-danger" onclick="$('#docsModal').modal('hide'); rejectDealer(${dealer.id})">
                            <i class="fas fa-times me-1"></i>Reject
                        </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const $modal = $(docs);
    $('body').append($modal);
    $modal.modal('show');
    $modal.on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function bulkApprove() {
    if (selectedDealers.size === 0) return;
    if (!confirm(`Approve ${selectedDealers.size} dealers?`)) return;
    
    showLoading();
    
    const promises = Array.from(selectedDealers).map(id => 
        $.ajax({
            url: `/admin/dealers/${id}/approve`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' }
        })
    );
    
    Promise.all(promises).then(() => {
        showToast(`${selectedDealers.size} dealers approved`, 'success');
        clearSelection();
        loadDealers();
    }).catch(() => {
        hideLoading();
        showToast('Some operations failed', 'error');
    });
}

function bulkReject() {
    if (selectedDealers.size === 0) return;
    if (!confirm(`Reject ${selectedDealers.size} dealers?`)) return;
    
    showLoading();
    
    const promises = Array.from(selectedDealers).map(id => 
        $.ajax({
            url: `/admin/dealers/${id}/reject`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' }
        })
    );
    
    Promise.all(promises).then(() => {
        showToast(`${selectedDealers.size} dealers rejected`, 'warning');
        clearSelection();
        loadDealers();
    }).catch(() => {
        hideLoading();
        showToast('Some operations failed', 'error');
    });
}

function bulkDelete() {
    if (selectedDealers.size === 0) return;
    if (!confirm(`DELETE ${selectedDealers.size} dealers? This cannot be undone!`)) return;
    
    showLoading();
    
    const promises = Array.from(selectedDealers).map(id => 
        $.ajax({
            url: `/admin/dealers/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' }
        })
    );
    
    Promise.all(promises).then(() => {
        showToast(`${selectedDealers.size} dealers deleted`, 'success');
        clearSelection();
        loadDealers();
    }).catch(() => {
        hideLoading();
        showToast('Some operations failed', 'error');
    });
}

function showLoading() {
    $('#loadingOverlay').addClass('show');
}

function hideLoading() {
    $('#loadingOverlay').removeClass('show');
}

function showToast(message, type = 'info') {
    const bgClass = {
        success: 'bg-success',
        error: 'bg-danger',
        warning: 'bg-warning',
        info: 'bg-info'
    }[type] || 'bg-info';
    
    const icon = {
        success: 'fa-check-circle',
        error: 'fa-times-circle',
        warning: 'fa-exclamation-circle',
        info: 'fa-info-circle'
    }[type] || 'fa-info-circle';
    
    const toast = $(`
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 99999;">
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
    setTimeout(() => toast.fadeOut(() => toast.remove()), 3000);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endsection
