@extends('admin.layouts.app')

@section('title', 'Advertisements')

@section('styles')
<style>
    .ads-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(240, 147, 251, 0.3);
    }
    .ads-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 2rem;
    }
    .stats-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: none;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .stats-card .icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }
    .stats-card.pink .icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    .stats-card.green .icon {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }
    .stats-card.red .icon {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
    }
    .stats-card.orange .icon {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }
    .stats-card h3 {
        font-size: 2rem;
        font-weight: 700;
        margin: 10px 0;
        color: #2d3748;
    }
    .stats-card p {
        color: #718096;
        margin: 0;
        font-size: 0.95rem;
    }
    .ad-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid #f093fb;
    }
    .ad-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        transform: translateX(5px);
    }
    .ad-image {
        width: 150px;
        height: 100px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .ad-info {
        flex: 1;
        margin-left: 25px;
    }
    .ad-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0 0 10px 0;
    }
    .ad-desc {
        color: #718096;
        font-size: 0.9rem;
        margin: 5px 0;
    }
    .ad-meta {
        display: flex;
        gap: 15px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e2e8f0;
    }
    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #4a5568;
    }
    .meta-item i {
        color: #f093fb;
    }
    .badge-position {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    .badge-type {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }
    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s ease;
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .btn-edit {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    .btn-delete {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
    }
    .btn-add-new {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }
    .btn-add-new:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(240, 147, 251, 0.4);
        color: white;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .empty-state i {
        font-size: 80px;
        color: #cbd5e0;
        margin-bottom: 20px;
    }
    .date-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        background: #f7fafc;
        color: #4a5568;
    }
    .expired-badge {
        background: #fee;
        color: #c53030;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="ads-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-bullhorn me-3"></i>Advertisements Management</h2>
                <p class="mb-0 mt-2" style="opacity: 0.9;">Manage all promotional advertisements across your platform</p>
            </div>
            <a href="{{ route('admin.advertisements.create') }}" class="btn btn-add-new btn-lg">
                <i class="fas fa-plus me-2"></i>Add New Ad
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card pink">
                <div class="icon">
                    <i class="fas fa-ad"></i>
                </div>
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Ads</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card green">
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>{{ $stats['active'] }}</h3>
                <p>Active Ads</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card red">
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h3>{{ $stats['inactive'] }}</h3>
                <p>Inactive Ads</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card orange">
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>{{ $stats['expired'] }}</h3>
                <p>Expired Ads</p>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 10px;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Advertisements List -->
    @forelse($advertisements as $ad)
        <div class="ad-card">
            <div class="d-flex">
                <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" class="ad-image">
                <div class="ad-info">
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="flex: 1;">
                            <h3 class="ad-title">{{ $ad->title }}</h3>
                            @if($ad->description)
                                <p class="ad-desc">{{ Str::limit($ad->description, 120) }}</p>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-position">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ ucfirst($ad->position) }}
                            </span>
                            <span class="badge-type">
                                <i class="fas fa-{{ $ad->type == 'banner' ? 'image' : ($ad->type == 'popup' ? 'window-maximize' : 'columns') }} me-1"></i>
                                {{ ucfirst($ad->type) }}
                            </span>
                            <form action="{{ route('admin.advertisements.toggle', $ad->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $ad->is_active ? 'btn-success' : 'btn-secondary' }}" style="border-radius: 20px; padding: 6px 16px;">
                                    <i class="fas fa-{{ $ad->is_active ? 'check' : 'times' }} me-1"></i>
                                    {{ $ad->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.advertisements.edit', $ad->id) }}" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.advertisements.destroy', $ad->id) }}" method="POST" 
                                  onsubmit="return confirm('Delete this advertisement?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="ad-meta">
                        @if($ad->link)
                            <div class="meta-item">
                                <i class="fas fa-link"></i>
                                <a href="{{ $ad->link }}" target="_blank" class="text-decoration-none">{{ Str::limit($ad->link, 40) }}</a>
                            </div>
                        @endif
                        @if($ad->button_text)
                            <div class="meta-item">
                                <i class="fas fa-mouse-pointer"></i>
                                <span>{{ $ad->button_text }}</span>
                            </div>
                        @endif
                        <div class="meta-item">
                            <i class="fas fa-sort"></i>
                            <span>Order: {{ $ad->order }}</span>
                        </div>
                        @if($ad->start_date)
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span class="date-badge {{ $ad->end_date && $ad->end_date < now() ? 'expired-badge' : '' }}">
                                    {{ $ad->start_date->format('M d, Y') }} - {{ $ad->end_date ? $ad->end_date->format('M d, Y') : 'No end' }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-bullhorn"></i>
            <h3>No Advertisements Yet</h3>
            <p class="mb-4">Create your first advertisement to promote across your platform</p>
            <a href="{{ route('admin.advertisements.create') }}" class="btn btn-add-new">
                <i class="fas fa-plus me-2"></i>Create First Advertisement
            </a>
        </div>
    @endforelse
</div>
@endsection
