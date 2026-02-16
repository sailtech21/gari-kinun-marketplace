@extends('admin.layouts.app')

@section('title', 'Users Management')

@section('styles')
<style>
    * {
        box-sizing: border-box;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
    .stat-card.green { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .stat-card.orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .stat-card.red { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); }
    .stat-card.purple { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; }
    
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
    
    .filter-select {
        border-radius: 25px;
        border: 2px solid #e9ecef;
        padding: 8px 15px;
    }
    
    .users-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .user-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
        border: 2px solid transparent;
    }
    
    .user-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        border-color: #667eea;
    }
    
    .user-card.selected {
        border-color: #667eea;
        background: #f8f9ff;
    }
    
    .user-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
    }
    
    .user-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .user-info {
        flex: 1;
        min-width: 0;
    }
    
    .user-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0 0 5px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .user-email {
        font-size: 0.85rem;
        color: #718096;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .user-id {
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
    
    .user-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .detail-item {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
    }
    
    .detail-label {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 3px;
    }
    
    .detail-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
    }
    
    .detail-value.badge {
        font-size: 0.85rem;
        padding: 5px 12px;
    }
    
    .user-meta {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    
    .meta-item {
        flex: 1;
        min-width: 100px;
        background: #f8f9fa;
        padding: 8px;
        border-radius: 8px;
        font-size: 0.8rem;
    }
    
    .meta-item i {
        color: #667eea;
        margin-right: 5px;
    }
    
    .user-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    
    .btn-action {
        padding: 8px;
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
    
    .btn-verify {
        background: #43e97b;
        color: white;
    }
    
    .btn-unverify {
        background: #ffc107;
        color: white;
    }
    
    .btn-block {
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
    
    .pagination-container {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 30px;
    }
    
    .page-btn {
        padding: 8px 15px;
        border: 2px solid #667eea;
        background: white;
        color: #667eea;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 600;
    }
    
    .page-btn:hover:not(:disabled) {
        background: #667eea;
        color: white;
    }
    
    .page-btn.active {
        background: #667eea;
        color: white;
    }
    
    .page-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .users-grid {
            grid-template-columns: 1fr;
        }
        
        .controls-bar > div {
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('content')
<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card blue" data-filter="all">
        <div class="icon"><i class="fas fa-users"></i></div>
        <div class="number" id="totalUsers">{{ $stats['total'] }}</div>
        <div class="label">Total Users</div>
    </div>
    
    <div class="stat-card green" data-filter="verified">
        <div class="icon"><i class="fas fa-user-check"></i></div>
        <div class="number" id="verifiedUsers">{{ $stats['verified'] }}</div>
        <div class="label">Verified</div>
    </div>
    
    <div class="stat-card orange" data-filter="unverified">
        <div class="icon"><i class="fas fa-user-clock"></i></div>
        <div class="number" id="unverifiedUsers">{{ $stats['total'] - $stats['verified'] }}</div>
        <div class="label">Unverified</div>
    </div>
    
    <div class="stat-card red" data-filter="active_today">
        <div class="icon"><i class="fas fa-fire"></i></div>
        <div class="number" id="activeUsers">{{ $stats['active_today'] }}</div>
        <div class="label">Active Today</div>
    </div>
    
    <div class="stat-card purple" data-filter="has_listings">
        <div class="icon"><i class="fas fa-list-alt"></i></div>
        <div class="number" id="usersWithListings">0</div>
        <div class="label">With Listings</div>
    </div>
</div>

<!-- Controls -->
<div class="controls-bar">
    <div class="row align-items-center">
        <div class="col-md-4 mb-2 mb-md-0">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by name, email, phone...">
            </div>
        </div>
        
        <div class="col-md-2 mb-2 mb-md-0">
            <select id="verificationFilter" class="form-select filter-select">
                <option value="">All Status</option>
                <option value="verified">Verified</option>
                <option value="unverified">Unverified</option>
            </select>
        </div>
        
        <div class="col-md-2 mb-2 mb-md-0">
            <select id="listingsFilter" class="form-select filter-select">
                <option value="">All Users</option>
                <option value="has">Has Listings</option>
                <option value="none">No Listings</option>
            </select>
        </div>
        
        <div class="col-md-2 mb-2 mb-md-0">
            <select id="sortBy" class="form-select filter-select">
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
    <span><strong id="selectedCount">0</strong> users selected</span>
    <button onclick="bulkVerify()"><i class="fas fa-check me-1"></i>Verify All</button>
    <button onclick="bulkUnverify()"><i class="fas fa-times me-1"></i>Unverify All</button>
    <button onclick="bulkDelete()"><i class="fas fa-trash me-1"></i>Delete All</button>
    <button onclick="clearSelection()"><i class="fas fa-ban me-1"></i>Clear</button>
</div>

<!-- Users Grid -->
<div id="usersGrid" class="users-grid"></div>

<!-- Empty State -->
<div id="emptyState" class="empty-state" style="display: none;">
    <i class="fas fa-users-slash"></i>
    <h4>No users found</h4>
    <p>Try adjusting your search or filters</p>
</div>

<!-- Pagination -->
<div class="pagination-container" id="pagination"></div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>
@endsection

@section('scripts')
<script>
let allUsers = [];
let filteredUsers = [];
let selectedUsers = new Set();
let currentPage = 1;
let usersPerPage = 12;
let currentFilter = 'all';

$(document).ready(function() {
    loadUsers();
    
    // Stat card click to filter
    $('.stat-card').on('click', function() {
        $('.stat-card').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        currentPage = 1;
        applyFilters();
    });
    
    // Search
    $('#searchInput').on('input', debounce(function() {
        currentPage = 1;
        applyFilters();
    }, 300));
    
    // Filters
    $('#verificationFilter, #listingsFilter, #sortBy').on('change', function() {
        currentPage = 1;
        applyFilters();
    });
    
    // Reset
    $('#resetBtn').on('click', function() {
        $('#searchInput').val('');
        $('#verificationFilter').val('');
        $('#listingsFilter').val('');
        $('#sortBy').val('newest');
        $('.stat-card').removeClass('active');
        currentFilter = 'all';
        currentPage = 1;
        applyFilters();
    });
});

function loadUsers() {
    showLoading();
    
    $.get('/admin/users/all', function(response) {
        allUsers = response.users;
        updateStats();
        applyFilters();
        hideLoading();
    }).fail(function() {
        hideLoading();
        showToast('Failed to load users', 'error');
    });
}

function updateStats() {
    const total = allUsers.length;
    const verified = allUsers.filter(u => u.email_verified_at).length;
    const activeToday = allUsers.filter(u => {
        const updated = new Date(u.updated_at);
        const today = new Date();
        return updated.toDateString() === today.toDateString();
    }).length;
    const withListings = allUsers.filter(u => u.listings_count > 0).length;
    
    $('#totalUsers').text(total);
    $('#verifiedUsers').text(verified);
    $('#unverifiedUsers').text(total - verified);
    $('#activeUsers').text(activeToday);
    $('#usersWithListings').text(withListings);
}

function applyFilters() {
    let users = [...allUsers];
    
    // Stat card filter
    if (currentFilter === 'verified') {
        users = users.filter(u => u.email_verified_at);
    } else if (currentFilter === 'unverified') {
        users = users.filter(u => !u.email_verified_at);
    } else if (currentFilter === 'active_today') {
        users = users.filter(u => {
            const updated = new Date(u.updated_at);
            const today = new Date();
            return updated.toDateString() === today.toDateString();
        });
    } else if (currentFilter === 'has_listings') {
        users = users.filter(u => u.listings_count > 0);
    }
    
    // Search filter
    const search = $('#searchInput').val().toLowerCase();
    if (search) {
        users = users.filter(u => 
            u.name.toLowerCase().includes(search) ||
            u.email.toLowerCase().includes(search) ||
            (u.phone && u.phone.includes(search))
        );
    }
    
    // Verification filter
    const verificationFilter = $('#verificationFilter').val();
    if (verificationFilter === 'verified') {
        users = users.filter(u => u.email_verified_at);
    } else if (verificationFilter === 'unverified') {
        users = users.filter(u => !u.email_verified_at);
    }
    
    // Listings filter
    const listingsFilter = $('#listingsFilter').val();
    if (listingsFilter === 'has') {
        users = users.filter(u => u.listings_count > 0);
    } else if (listingsFilter === 'none') {
        users = users.filter(u => u.listings_count === 0);
    }
    
    // Sort
    const sortBy = $('#sortBy').val();
    if (sortBy === 'newest') {
        users.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    } else if (sortBy === 'oldest') {
        users.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
    } else if (sortBy === 'name') {
        users.sort((a, b) => a.name.localeCompare(b.name));
    } else if (sortBy === 'listings') {
        users.sort((a, b) => b.listings_count - a.listings_count);
    }
    
    filteredUsers = users;
    renderUsers();
    renderPagination();
}

function renderUsers() {
    const start = (currentPage - 1) * usersPerPage;
    const end = start + usersPerPage;
    const pageUsers = filteredUsers.slice(start, end);
    
    if (pageUsers.length === 0) {
        $('#usersGrid').hide();
        $('#emptyState').show();
        $('#pagination').hide();
        return;
    }
    
    $('#usersGrid').show();
    $('#emptyState').hide();
    $('#pagination').show();
    
    const html = pageUsers.map(user => renderUserCard(user)).join('');
    $('#usersGrid').html(html);
}

function renderUserCard(user) {
    const initial = user.name.charAt(0).toUpperCase();
    const verified = user.email_verified_at ? 'verified' : 'unverified';
    const verifiedBadge = user.email_verified_at 
        ? '<span class="badge bg-success"><i class="fas fa-check"></i> Verified</span>'
        : '<span class="badge bg-warning"><i class="fas fa-clock"></i> Unverified</span>';
    
    const joined = new Date(user.created_at).toLocaleDateString('en-US', { 
        year: 'numeric', month: 'short', day: 'numeric' 
    });
    
    const lastActive = formatTimeAgo(user.updated_at);
    
    const verifyBtn = user.email_verified_at 
        ? `<button class="btn-action btn-unverify" onclick="unverifyUser(${user.id})">
             <i class="fas fa-times"></i> Unverify
           </button>`
        : `<button class="btn-action btn-verify" onclick="verifyUser(${user.id})">
             <i class="fas fa-check"></i> Verify
           </button>`;
    
    const isSelected = selectedUsers.has(user.id) ? 'selected' : '';
    
    return `
        <div class="user-card ${isSelected}" data-user-id="${user.id}">
            <input type="checkbox" class="select-checkbox" onchange="toggleSelect(${user.id})" ${selectedUsers.has(user.id) ? 'checked' : ''}>
            <span class="user-id">#${user.id}</span>
            
            <div class="user-header">
                <div class="user-avatar">
                    ${user.avatar ? `<img src="${user.avatar}" alt="${user.name}">` : initial}
                </div>
                <div class="user-info">
                    <div class="user-name">${user.name}</div>
                    <div class="user-email">${user.email}</div>
                </div>
            </div>
            
            <div class="user-details">
                <div class="detail-item">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">${verifiedBadge}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Listings</div>
                    <div class="detail-value">
                        <span class="badge ${user.listings_count > 0 ? 'bg-primary' : 'bg-secondary'}">
                            ${user.listings_count}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="user-meta">
                ${user.phone ? `
                <div class="meta-item">
                    <i class="fas fa-phone"></i>
                    <span>${user.phone}</span>
                </div>
                ` : ''}
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span>${joined}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <span>${lastActive}</span>
                </div>
                ${user.firebase_uid ? `
                <div class="meta-item" style="grid-column: 1/-1;">
                    <i class="fas fa-fingerprint"></i>
                    <span title="${user.firebase_uid}" style="font-size: 0.7rem; opacity: 0.7;">Firebase: ${user.firebase_uid.substring(0, 15)}...</span>
                </div>
                ` : ''}
            </div>
            
            <div class="user-actions">
                ${verifyBtn}
                <button class="btn-action btn-view" onclick="viewUser(${user.id})">
                    <i class="fas fa-eye"></i> View
                </button>
                ${user.listings_count > 0 ? `
                <button class="btn-action btn-view" onclick="viewUserListings(${user.id})">
                    <i class="fas fa-list"></i> Listings
                </button>
                ` : ''}
                <button class="btn-action btn-delete" onclick="deleteUser(${user.id})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    `;
}

function renderPagination() {
    const totalPages = Math.ceil(filteredUsers.length / usersPerPage);
    
    if (totalPages <= 1) {
        $('#pagination').html('');
        return;
    }
    
    let html = '';
    
    // Previous button
    html += `<button class="page-btn" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
        <i class="fas fa-chevron-left"></i>
    </button>`;
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">
                ${i}
            </button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += `<span style="padding: 8px;">...</span>`;
        }
    }
    
    // Next button
    html += `<button class="page-btn" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
        <i class="fas fa-chevron-right"></i>
    </button>`;
    
    $('#pagination').html(html);
}

function changePage(page) {
    const totalPages = Math.ceil(filteredUsers.length / usersPerPage);
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    renderUsers();
    renderPagination();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function toggleSelect(userId) {
    if (selectedUsers.has(userId)) {
        selectedUsers.delete(userId);
    } else {
        selectedUsers.add(userId);
    }
    
    updateBulkActions();
    
    // Update card appearance
    const card = $(`.user-card[data-user-id="${userId}"]`);
    card.toggleClass('selected', selectedUsers.has(userId));
}

function clearSelection() {
    selectedUsers.clear();
    $('.select-checkbox').prop('checked', false);
    $('.user-card').removeClass('selected');
    updateBulkActions();
}

function updateBulkActions() {
    const count = selectedUsers.size;
    $('#selectedCount').text(count);
    
    if (count > 0) {
        $('#bulkActions').addClass('show');
    } else {
        $('#bulkActions').removeClass('show');
    }
}

function verifyUser(id) {
    showLoading();
    
    $.ajax({
        url: `/admin/users/${id}/verify`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            showToast('User verified successfully', 'success');
            loadUsers();
        },
        error: function() {
            hideLoading();
            showToast('Failed to verify user', 'error');
        }
    });
}

function unverifyUser(id) {
    showLoading();
    
    $.ajax({
        url: `/admin/users/${id}/unverify`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            showToast('User unverified successfully', 'warning');
            loadUsers();
        },
        error: function() {
            hideLoading();
            showToast('Failed to unverify user', 'error');
        }
    });
}

function deleteUser(id) {
    if (!confirm('Are you sure? This will delete the user and all their listings!')) {
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: `/admin/users/${id}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            showToast('User deleted successfully', 'success');
            loadUsers();
        },
        error: function() {
            hideLoading();
            showToast('Failed to delete user', 'error');
        }
    });
}

function viewUser(id) {
    const user = allUsers.find(u => u.id === id);
    if (!user) return;
    
    const verifiedBadge = user.email_verified_at 
        ? '<span class="badge bg-success"><i class="fas fa-check"></i> Verified</span>'
        : '<span class="badge bg-warning"><i class="fas fa-clock"></i> Unverified</span>';
    
    const modal = `
        <div class="modal fade" id="userModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-user me-2"></i>${user.name} - User Details</h5>
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
                                <label class="text-muted small">Phone</label>
                                <p class="fw-bold">${user.phone || 'N/A'}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Status</label>
                                <p>${verifiedBadge}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Total Listings</label>
                                <p class="fw-bold">${user.listings_count}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">User ID</label>
                                <p class="fw-bold">#${user.id}</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="text-muted small">Firebase UID</label>
                                <p class="fw-bold" style="font-size: 0.85rem; word-break: break-all;">${user.firebase_uid || 'N/A'}</p>
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
                        ${user.listings_count > 0 ? `
                        <button type="button" class="btn btn-primary" onclick="viewUserListings(${user.id})">
                            <i class="fas fa-list me-1"></i>View Listings
                        </button>
                        ` : ''}
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

function viewUserListings(userId) {
    window.location.href = `/admin/listings?user_id=${userId}`;
}

function bulkVerify() {
    if (selectedUsers.size === 0) return;
    
    if (!confirm(`Verify ${selectedUsers.size} users?`)) return;
    
    showLoading();
    
    const promises = Array.from(selectedUsers).map(id => 
        $.ajax({
            url: `/admin/users/${id}/verify`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' }
        })
    );
    
    Promise.all(promises).then(() => {
        showToast(`${selectedUsers.size} users verified`, 'success');
        clearSelection();
        loadUsers();
    }).catch(() => {
        hideLoading();
        showToast('Some operations failed', 'error');
    });
}

function bulkUnverify() {
    if (selectedUsers.size === 0) return;
    
    if (!confirm(`Unverify ${selectedUsers.size} users?`)) return;
    
    showLoading();
    
    const promises = Array.from(selectedUsers).map(id => 
        $.ajax({
            url: `/admin/users/${id}/unverify`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' }
        })
    );
    
    Promise.all(promises).then(() => {
        showToast(`${selectedUsers.size} users unverified`, 'warning');
        clearSelection();
        loadUsers();
    }).catch(() => {
        hideLoading();
        showToast('Some operations failed', 'error');
    });
}

function bulkDelete() {
    if (selectedUsers.size === 0) return;
    
    if (!confirm(`DELETE ${selectedUsers.size} users? This cannot be undone!`)) return;
    
    showLoading();
    
    const promises = Array.from(selectedUsers).map(id => 
        $.ajax({
            url: `/admin/users/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' }
        })
    );
    
    Promise.all(promises).then(() => {
        showToast(`${selectedUsers.size} users deleted`, 'success');
        clearSelection();
        loadUsers();
    }).catch(() => {
        hideLoading();
        showToast('Some operations failed', 'error');
    });
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // seconds
    
    if (diff < 60) return 'Just now';
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;
    
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
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
