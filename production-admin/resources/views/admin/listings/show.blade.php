@extends('admin.layouts.app')

@section('title', 'Listing Details')

@section('styles')
<style>
    .detail-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .detail-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .detail-value {
        font-size: 1.1rem;
        color: #212529;
        margin-top: 5px;
    }
    .image-gallery img {
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .image-gallery img:hover {
        transform: scale(1.05);
    }
    .status-badge {
        padding: 8px 16px;
        font-size: 0.95rem;
        border-radius: 20px;
    }
    .info-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.listings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Listings
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Information -->
    <div class="col-md-8">
        <div class="card detail-card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Listing Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="detail-label">Title</div>
                        <div class="detail-value">
                            {{ $listing->title }}
                            @if($listing->is_featured)
                                <span class="badge bg-warning ms-2">
                                    <i class="fas fa-star me-1"></i>Featured
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-12 mb-4">
                        <div class="detail-label">Description</div>
                        <div class="detail-value">{{ $listing->description ?: 'No description provided' }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="detail-label">Price</div>
                        <div class="detail-value text-success">
                            <i class="fas fa-dollar-sign"></i>{{ number_format($listing->price, 2) }}
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="detail-label">Location</div>
                        <div class="detail-value">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>{{ $listing->location }}
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="detail-label">Category</div>
                        <div class="detail-value">
                            <span class="badge bg-secondary">{{ $listing->category->name }}</span>
                            <small class="text-muted ms-2">({{ $listing->category->type }})</small>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            @if($listing->status == 'active')
                                <span class="badge bg-success status-badge">
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </span>
                            @elseif($listing->status == 'pending')
                                <span class="badge bg-warning status-badge">
                                    <i class="fas fa-clock me-1"></i>Pending
                                </span>
                            @elseif($listing->status == 'sold')
                                <span class="badge bg-info status-badge">
                                    <i class="fas fa-handshake me-1"></i>Sold
                                </span>
                            @else
                                <span class="badge bg-danger status-badge">
                                    <i class="fas fa-times-circle me-1"></i>Rejected
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="detail-label">Views</div>
                        <div class="detail-value">
                            <i class="fas fa-eye text-info me-2"></i>{{ number_format($listing->views) }}
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="detail-label">Created</div>
                        <div class="detail-value">
                            <i class="fas fa-calendar text-primary me-2"></i>{{ $listing->created_at->format('M d, Y H:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Images Gallery -->
        @if(is_array($listing->images) && count($listing->images) > 0)
        <div class="card detail-card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-images me-2"></i>Image Gallery</h5>
            </div>
            <div class="card-body">
                <div class="row image-gallery">
                    @foreach($listing->images as $image)
                    <div class="col-md-4 mb-3">
                        <img src="{{ $image }}" alt="Listing Image" class="img-fluid" 
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22200%22%3E%3Crect fill=%22%23e9ecef%22 width=%22300%22 height=%22200%22/%3E%3Ctext fill=%22%23adb5bd%22 font-family=%22Arial%22 font-size=%2216%22 x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- User Information -->
        <div class="card detail-card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Listed By</h5>
            </div>
            <div class="card-body">
                <div class="info-section">
                    <div class="detail-label">Name</div>
                    <div class="detail-value">{{ $listing->user->name }}</div>
                </div>
                <div class="info-section">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">
                        <a href="mailto:{{ $listing->user->email }}">{{ $listing->user->email }}</a>
                    </div>
                </div>
                <div class="info-section">
                    <div class="detail-label">Member Since</div>
                    <div class="detail-value">{{ $listing->user->created_at->format('M Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Dealer Information -->
        @if($listing->dealer)
        <div class="card detail-card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-store me-2"></i>Dealer</h5>
            </div>
            <div class="card-body">
                <div class="info-section">
                    <div class="detail-label">Name</div>
                    <div class="detail-value">{{ $listing->dealer->name }}</div>
                </div>
                <div class="info-section">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="badge bg-{{ $listing->dealer->status == 'active' ? 'success' : 'warning' }}">
                            {{ ucfirst($listing->dealer->status) }}
                        </span>
                    </div>
                </div>
                @if($listing->dealer->phone)
                <div class="info-section">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value">
                        <a href="tel:{{ $listing->dealer->phone }}">{{ $listing->dealer->phone }}</a>
                    </div>
                </div>
                @endif
                @if($listing->dealer->address)
                <div class="info-section">
                    <div class="detail-label">Address</div>
                    <div class="detail-value">{{ $listing->dealer->address }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="card detail-card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h5>
            </div>
            <div class="card-body">
                <form id="statusForm" class="mb-3">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label detail-label">Change Status</label>
                        <select name="status" class="form-select" id="statusSelect">
                            <option value="pending" {{ $listing->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ $listing->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="sold" {{ $listing->status == 'sold' ? 'selected' : '' }}>Sold</option>
                            <option value="rejected" {{ $listing->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Update Status
                    </button>
                </form>

                <form id="deleteForm" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-trash me-2"></i>Delete Listing
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Update Status
    $('#statusForm').submit(function(e) {
        e.preventDefault();
        
        const status = $('#statusSelect').val();
        
        $.ajax({
            url: '{{ route("admin.listings.status", $listing->id) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                alert('Status updated successfully!');
                location.reload();
            },
            error: function(xhr) {
                alert('Error updating status. Please try again.');
            }
        });
    });

    // Delete Listing
    $('#deleteForm').submit(function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to delete this listing? This action cannot be undone.')) {
            return;
        }
        
        $.ajax({
            url: '{{ route("admin.listings.destroy", $listing->id) }}',
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert('Listing deleted successfully!');
                window.location.href = '{{ route("admin.listings.index") }}';
            },
            error: function(xhr) {
                alert('Error deleting listing. Please try again.');
            }
        });
    });
</script>
@endsection
