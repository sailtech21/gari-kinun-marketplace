@extends('admin.layouts.app')

@section('title', 'Dealers Management')

@section('styles')
<style>
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
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .stat-card.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-card.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .stat-card.bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-card.bg-gradient-danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .stat-card.bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-card.bg-gradient-secondary { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
    .stat-card.bg-gradient-dark { background: linear-gradient(135deg, #434343 0%, #000000 100%); }
    
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
    
    .filters-card {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }
    
    .table-wrapper {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .dealer-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .dealer-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .badge-bronze { background: #cd7f32; }
    .badge-silver { background: #c0c0c0; color: #333; }
    .badge-gold { background: #ffd700; color: #333; }
    .badge-platinum { background: #e5e4e2; color: #333; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">🏢 Dealers Management</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#upgradeUserModal">
            <i class="fas fa-user-plus"></i> Upgrade User to Dealer
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card bg-gradient-primary" onclick="filterByStatus('all')">
            <div class="icon"><i class="fas fa-store"></i></div>
            <div class="number">{{ $stats['total'] }}</div>
            <div class="label">Total Dealers</div>
        </div>
        <div class="stat-card bg-gradient-warning" onclick="filterByStatus('pending')">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <div class="number">{{ $stats['pending'] }}</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat-card bg-gradient-success" onclick="filterByStatus('active')">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <div class="number">{{ $stats['active'] }}</div>
            <div class="label">Active</div>
        </div>
        <div class="stat-card bg-gradient-danger" onclick="filterByStatus('rejected')">
            <div class="icon"><i class="fas fa-times-circle"></i></div>
            <div class="number">{{ $stats['rejected'] }}</div>
            <div class="label">Rejected</div>
        </div>
        <div class="stat-card bg-gradient-info" onclick="filterByVerified('yes')">
            <div class="icon"><i class="fas fa-shield-alt"></i></div>
            <div class="number">{{ $stats['verified'] }}</div>
            <div class="label">Verified</div>
        </div>
        <div class="stat-card bg-gradient-secondary" onclick="filterByFeatured('yes')">
            <div class="icon"><i class="fas fa-star"></i></div>
            <div class="number">{{ $stats['featured'] }}</div>
            <div class="label">Featured</div>
        </div>
        <div class="stat-card bg-gradient-dark" onclick="filterBySuspended('yes')">
            <div class="icon"><i class="fas fa-ban"></i></div>
            <div class="number">{{ $stats['suspended'] }}</div>
            <div class="label">Suspended</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <form id="filterForm" class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search dealers..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="badge" id="badgeFilter">
                    <option value="">All Badges</option>
                    <option value="bronze" {{ request('badge') == 'bronze' ? 'selected' : '' }}>Bronze</option>
                    <option value="silver" {{ request('badge') == 'silver' ? 'selected' : '' }}>Silver</option>
                    <option value="gold" {{ request('badge') == 'gold' ? 'selected' : '' }}>Gold</option>
                    <option value="platinum" {{ request('badge') == 'platinum' ? 'selected' : '' }}>Platinum</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="verified" id="verifiedFilter">
                    <option value="">All Verification</option>
                    <option value="yes" {{ request('verified') == 'yes' ? 'selected' : '' }}>Verified</option>
                    <option value="no" {{ request('verified') == 'no' ? 'selected' : '' }}>Not Verified</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Clear Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Dealers Table -->
    <div class="table-wrapper">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Dealer</th>
                        <th>Business</th>
                        <th>Badge</th>
                        <th>Ads</th>
                        <th>Status</th>
                        <th>Subscription</th>
                        <th>Applied</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dealers as $dealer)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="dealer-avatar me-2">
                                    @if($dealer->user && $dealer->user->avatar)
                                        @if(str_starts_with($dealer->user->avatar, 'http'))
                                            <img src="{{ $dealer->user->avatar }}" alt="Avatar">
                                        @else
                                            <img src="{{ asset('storage/' . $dealer->user->avatar) }}" alt="Avatar">
                                        @endif
                                    @else
                                        {{ strtoupper(substr($dealer->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $dealer->name }}</div>
                                    <small class="text-muted">{{ $dealer->email }}</small>
                                    @if($dealer->is_featured)
                                        <span class="badge bg-warning text-dark ms-1"><i class="fas fa-star"></i></span>
                                    @endif
                                    @if($dealer->is_suspended)
                                        <span class="badge bg-dark ms-1"><i class="fas fa-ban"></i></span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $dealer->business_name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $dealer->phone }}</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ $dealer->badge }}">
                                {{ ucfirst($dealer->badge) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $dealer->listings->count() }} Ads</span>
                            @if($dealer->listing_limit)
                                <small class="text-muted d-block">Limit: {{ $dealer->listing_limit }}</small>
                            @endif
                        </td>
                        <td>
                            @if($dealer->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($dealer->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                            @if($dealer->is_verified)
                                <i class="fas fa-check-circle text-success ms-1" title="Verified"></i>
                            @endif
                        </td>
                        <td>
                            @if($dealer->subscription_tier)
                                <div>{{ ucfirst($dealer->subscription_tier) }}</div>
                                @if($dealer->subscription_ends_at)
                                    <small class="text-muted">Until: {{ $dealer->subscription_ends_at->format('M d, Y') }}</small>
                                @endif
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $dealer->applied_at ? $dealer->applied_at->format('M d, Y') : 'N/A' }}</small>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewDealer({{ $dealer->id }})">
                                        <i class="fas fa-eye"></i> View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="editDealer({{ $dealer->id }})">
                                        <i class="fas fa-edit"></i> Edit Dealer
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    @if($dealer->status == 'pending')
                                        <li><a class="dropdown-item text-success" href="javascript:void(0)" onclick="approveDealer({{ $dealer->id }})">
                                            <i class="fas fa-check"></i> Approve
                                        </a></li>
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="rejectDealer({{ $dealer->id }})">
                                            <i class="fas fa-times"></i> Reject
                                        </a></li>
                                    @endif
                                    
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="setBadge({{ $dealer->id }}, '{{ $dealer->badge }}')">
                                        <i class="fas fa-medal"></i> Set Badge
                                    </a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="setSubscription({{ $dealer->id }})">
                                        <i class="fas fa-crown"></i> Set Subscription
                                    </a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="limitAds({{ $dealer->id }}, {{ $dealer->listing_limit ?? 0 }})">
                                        <i class="fas fa-list"></i> Limit Ads
                                    </a></li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    @if(!$dealer->is_suspended)
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="suspendDealer({{ $dealer->id }})">
                                            <i class="fas fa-ban"></i> Suspend
                                        </a></li>
                                    @else
                                        <li><a class="dropdown-item text-success" href="javascript:void(0)" onclick="unsuspendDealer({{ $dealer->id }})">
                                            <i class="fas fa-check"></i> Unsuspend
                                        </a></li>
                                    @endif
                                    
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="toggleFeature({{ $dealer->id }}, {{ $dealer->is_featured ? 'true' : 'false' }})">
                                        <i class="fas fa-star"></i> {{ $dealer->is_featured ? 'Unfeature' : 'Feature' }}
                                    </a></li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewDealerAds({{ $dealer->id }})">
                                        <i class="fas fa-ad"></i> View Ads
                                    </a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewRevenue({{ $dealer->id }})">
                                        <i class="fas fa-dollar-sign"></i> View Revenue
                                    </a></li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    <li><a class="dropdown-item text-warning" href="javascript:void(0)" onclick="removeDealerStatus({{ $dealer->id }})">
                                        <i class="fas fa-user-slash"></i> Remove Dealer Status
                                    </a></li>
                                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteDealer({{ $dealer->id }})">
                                        <i class="fas fa-trash"></i> Delete
                                    </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-store fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No dealers found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($dealers->hasPages())
        <div class="p-3 border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $dealers->firstItem() }} to {{ $dealers->lastItem() }} of {{ $dealers->total() }} dealers
                </div>
                <div>
                    {{ $dealers->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- View Dealer Modal -->
<div class="modal fade" id="viewDealerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dealer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="dealerDetails">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Dealer Modal -->
<div class="modal fade" id="editDealerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Dealer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDealerForm">
                <input type="hidden" id="editDealerId">
                <div class="modal-body">
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
                        <input type="text" class="form-control" id="editPhone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Business Name</label>
                        <input type="text" class="form-control" id="editBusinessName">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Business Phone</label>
                        <input type="text" class="form-control" id="editBusinessPhone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Listing Limit</label>
                        <input type="number" class="form-control" id="editListingLimit" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upgrade User to Dealer Modal -->
<div class="modal fade" id="upgradeUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upgrade User to Dealer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upgradeUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">User ID</label>
                        <input type="number" class="form-control" id="upgradeUserId" required>
                        <small class="text-muted">Enter the user ID to upgrade to dealer</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upgrade User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Set Subscription Modal -->
<div class="modal fade" id="subscriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Subscription Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="subscriptionForm">
                <input type="hidden" id="subDealerId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subscription Tier</label>
                        <select class="form-select" id="subTier" required>
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                            <option value="business">Business</option>
                            <option value="enterprise">Enterprise</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (Months)</label>
                        <input type="number" class="form-control" id="subDuration" min="1" max="12" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" class="form-control" id="subPrice" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Set Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Live search with debounce
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

// Filter change listeners
document.getElementById('statusFilter').addEventListener('change', applyFilters);
document.getElementById('badgeFilter').addEventListener('change', applyFilters);
document.getElementById('verifiedFilter').addEventListener('change', applyFilters);

function applyFilters() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    window.location.href = '{{ route("admin.dealers.index") }}?' + params.toString();
}

function filterByStatus(status) {
    document.getElementById('statusFilter').value = status === 'all' ? '' : status;
    applyFilters();
}

function filterByVerified(verified) {
    document.getElementById('verifiedFilter').value = verified;
    applyFilters();
}

function filterByFeatured(featured) {
    const url = new URL(window.location.href);
    url.searchParams.set('featured', featured);
    window.location.href = url.toString();
}

function filterBySuspended(suspended) {
    const url = new URL(window.location.href);
    url.searchParams.set('suspended', suspended);
    window.location.href = url.toString();
}

function clearFilters() {
    window.location.href = '{{ route("admin.dealers.index") }}';
}

function viewDealer(id) {
    fetch(`/admin/dealers/${id}`, {
        headers: {'X-CSRF-TOKEN': csrfToken}
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const dealer = data.dealer;
            document.getElementById('dealerDetails').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> ${dealer.name}</p>
                        <p><strong>Email:</strong> ${dealer.email}</p>
                        <p><strong>Phone:</strong> ${dealer.phone}</p>
                        <p><strong>Business:</strong> ${dealer.business_name || 'N/A'}</p>
                        <p><strong>Business Phone:</strong> ${dealer.business_phone || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span class="badge bg-${dealer.status === 'active' ? 'success' : dealer.status === 'pending' ? 'warning' : 'danger'}">${dealer.status}</span></p>
                        <p><strong>Badge:</strong> <span class="badge badge-${dealer.badge}">${dealer.badge}</span></p>
                        <p><strong>Verified:</strong> ${dealer.is_verified ? '✓ Yes' : '✗ No'}</p>
                        <p><strong>Featured:</strong> ${dealer.is_featured ? '✓ Yes' : '✗ No'}</p>
                        <p><strong>Suspended:</strong> ${dealer.is_suspended ? '✓ Yes' : '✗ No'}</p>
                        <p><strong>Total Ads:</strong> ${dealer.listings?.length || 0}</p>
                        <p><strong>Subscription:</strong> ${dealer.subscription_tier || 'None'}</p>
                    </div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('viewDealerModal')).show();
        }
    });
}

function editDealer(id) {
    fetch(`/admin/dealers/${id}/edit`, {
        headers: {'X-CSRF-TOKEN': csrfToken}
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const dealer = data.dealer;
            document.getElementById('editDealerId').value = dealer.id;
            document.getElementById('editName').value = dealer.name;
            document.getElementById('editEmail').value = dealer.email;
            document.getElementById('editPhone').value = dealer.phone;
            document.getElementById('editBusinessName').value = dealer.business_name || '';
            document.getElementById('editBusinessPhone').value = dealer.business_phone || '';
            document.getElementById('editListingLimit').value = dealer.listing_limit || '';
            new bootstrap.Modal(document.getElementById('editDealerModal')).show();
        }
    });
}

document.getElementById('editDealerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('editDealerId').value;
    const formData = {
        name: document.getElementById('editName').value,
        email: document.getElementById('editEmail').value,
        phone: document.getElementById('editPhone').value,
        business_name: document.getElementById('editBusinessName').value,
        business_phone: document.getElementById('editBusinessPhone').value,
        listing_limit: document.getElementById('editListingLimit').value,
    };
    
    fetch(`/admin/dealers/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        }
    });
});

function approveDealer(id) {
    if (confirm('Approve this dealer?')) {
        fetch(`/admin/dealers/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        });
    }
}

function rejectDealer(id) {
    if (confirm('Reject this dealer?')) {
        fetch(`/admin/dealers/${id}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        });
    }
}

document.getElementById('upgradeUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const userId = document.getElementById('upgradeUserId').value;
    
    fetch('/admin/dealers/upgrade-user', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({user_id: userId})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    });
});

function removeDealerStatus(id) {
    if (confirm('Remove dealer status from this user? This action cannot be undone.')) {
        fetch(`/admin/dealers/${id}/remove-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
}

function setBadge(id, currentBadge) {
    const badge = prompt('Enter badge (bronze, silver, gold, platinum):', currentBadge);
    if (badge && ['bronze', 'silver', 'gold', 'platinum'].includes(badge.toLowerCase())) {
        fetch(`/admin/dealers/${id}/set-badge`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({badge: badge.toLowerCase()})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        });
    }
}

function setSubscription(id) {
    document.getElementById('subDealerId').value = id;
    new bootstrap.Modal(document.getElementById('subscriptionModal')).show();
}

document.getElementById('subscriptionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('subDealerId').value;
    const formData = {
        subscription_tier: document.getElementById('subTier').value,
        duration_months: document.getElementById('subDuration').value,
        price: document.getElementById('subPrice').value,
    };
    
    fetch(`/admin/dealers/${id}/set-subscription`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        }
    });
});

function limitAds(id, currentLimit) {
    const limit = prompt('Enter ads limit (0 for unlimited):', currentLimit);
    if (limit !== null) {
        fetch(`/admin/dealers/${id}/limit-ads`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({listing_limit: parseInt(limit)})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        });
    }
}

function suspendDealer(id) {
    if (confirm('Suspend this dealer?')) {
        fetch(`/admin/dealers/${id}/suspend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        });
    }
}

function unsuspendDealer(id) {
    if (confirm('Unsuspend this dealer?')) {
        fetch(`/admin/dealers/${id}/unsuspend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        });
    }
}

function toggleFeature(id, isFeatured) {
    const action = isFeatured === 'true' ? 'unfeature' : 'feature';
    if (confirm(`${action.charAt(0).toUpperCase() + action.slice(1)} this dealer?`)) {
        fetch(`/admin/dealers/${id}/toggle-feature`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        });
    }
}

function viewDealerAds(id) {
    window.location.href = `/admin/listings?dealer_id=${id}`;
}

function viewRevenue(id) {
    fetch(`/admin/dealers/${id}/revenue`, {
        headers: {'X-CSRF-TOKEN': csrfToken}
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const revenue = data.revenue;
            alert(`Total Revenue: $${revenue.total}\nThis Month: $${revenue.this_month}\nLast Month: $${revenue.last_month}\nSubscriptions: $${revenue.subscriptions}`);
        }
    });
}

function deleteDealer(id) {
    if (confirm('Delete this dealer? This action cannot be undone.')) {
        fetch(`/admin/dealers/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
}
</script>
@endsection
