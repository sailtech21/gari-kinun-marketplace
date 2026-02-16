@extends('admin.layouts.app')

@section('title', 'Categories Management')

@section('styles')
<style>
    .categories-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    .categories-header h2 {
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
    .stats-card.purple .icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .stats-card.blue .icon {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    .stats-card.green .icon {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
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
    .category-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid #667eea;
    }
    .category-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        transform: translateX(5px);
    }
    .category-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    .category-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    .category-info {
        flex: 1;
        margin-left: 20px;
    }
    .category-name {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }
    .category-desc {
        color: #718096;
        font-size: 0.9rem;
        margin: 5px 0 0 0;
    }
    .category-stats {
        display: flex;
        gap: 15px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e2e8f0;
    }
    .stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        color: #4a5568;
    }
    .stat-item i {
        color: #667eea;
    }
    .subcategory-list {
        margin-top: 15px;
        padding-left: 70px;
    }
    .subcategory-item {
        background: #f7fafc;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
    }
    .subcategory-item:hover {
        background: #edf2f7;
        transform: translateX(5px);
    }
    .badge-type {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .badge-car {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    .badge-bike {
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }
    .btn-add-new:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }
    .status-toggle {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }
    .status-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
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
    .empty-state h3 {
        color: #4a5568;
        margin-bottom: 10px;
    }
    .empty-state p {
        color: #a0aec0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="categories-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-tags me-3"></i>Categories Management</h2>
                <p class="mb-0 mt-2" style="opacity: 0.9;">Organize your marketplace with categories and subcategories</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-add-new btn-lg">
                <i class="fas fa-plus me-2"></i>Add New Category
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card purple">
                <div class="icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3>{{ $categories->count() }}</h3>
                <p>Main Categories</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card blue">
                <div class="icon">
                    <i class="fas fa-sitemap"></i>
                </div>
                <h3>{{ $categories->sum(function($cat) { return $cat->children->count(); }) }}</h3>
                <p>Subcategories</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card green">
                <div class="icon">
                    <i class="fas fa-list-alt"></i>
                </div>
                <h3>{{ $categories->sum('listings_count') }}</h3>
                <p>Total Listings</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card orange">
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>{{ $categories->where('is_active', true)->count() }}</h3>
                <p>Active Categories</p>
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

    <!-- Categories List -->
    @forelse($categories as $category)
        <div class="category-card">
            <div class="category-header">
                <div class="d-flex align-items-center" style="flex: 1;">
                    <div class="category-icon">
                        <i class="{{ $category->icon ?: 'fas fa-tag' }}"></i>
                    </div>
                    <div class="category-info">
                        <h3 class="category-name">{{ $category->name }}</h3>
                        @if($category->description)
                            <p class="category-desc">{{ $category->description }}</p>
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-type badge-{{ strtolower($category->type) }}">
                        <i class="fas fa-{{ $category->type === 'Car' ? 'car' : 'motorcycle' }} me-1"></i>
                        {{ $category->type }}
                    </span>
                    <form action="{{ route('admin.categories.toggle', $category->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $category->is_active ? 'btn-success' : 'btn-secondary' }}" style="border-radius: 20px; padding: 6px 16px;">
                            <i class="fas fa-{{ $category->is_active ? 'check' : 'times' }} me-1"></i>
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </form>
                    <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}" 
                       class="btn btn-sm btn-outline-primary" 
                       style="border-radius: 8px; padding: 6px 14px;"
                       title="Add Subcategory">
                        <i class="fas fa-plus me-1"></i>Subcategory
                    </a>
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-action btn-edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" 
                          onsubmit="return confirm('Delete this category?');" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="category-stats">
                <div class="stat-item">
                    <i class="fas fa-list"></i>
                    <span><strong>{{ $category->listings_count }}</strong> Listings</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-sitemap"></i>
                    <span><strong>{{ $category->children->count() }}</strong> Subcategories</span>
                </div>
            </div>

            @if($category->children->count() > 0)
                <div class="subcategory-list">
                    @foreach($category->children as $subcategory)
                        <div class="subcategory-item">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-level-up-alt fa-rotate-90 text-muted"></i>
                                @if($subcategory->icon)
                                    <i class="{{ $subcategory->icon }} text-primary"></i>
                                @endif
                                <div>
                                    <strong>{{ $subcategory->name }}</strong>
                                    @if($subcategory->description)
                                        <br><small class="text-muted">{{ Str::limit($subcategory->description, 60) }}</small>
                                    @endif
                                </div>
                                <span class="badge bg-light text-dark ms-2">
                                    <i class="fas fa-list me-1"></i>{{ $subcategory->listings_count ?? 0 }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <form action="{{ route('admin.categories.toggle', $subcategory->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $subcategory->is_active ? 'btn-success' : 'btn-secondary' }}" style="border-radius: 15px; padding: 4px 12px; font-size: 0.8rem;">
                                        {{ $subcategory->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.categories.edit', $subcategory->id) }}" class="btn-action btn-edit" style="width: 32px; height: 32px;">
                                    <i class="fas fa-edit" style="font-size: 0.85rem;"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $subcategory->id) }}" method="POST"
                                      onsubmit="return confirm('Delete this subcategory?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" style="width: 32px; height: 32px;">
                                        <i class="fas fa-trash" style="font-size: 0.85rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-tags"></i>
            <h3>No Categories Yet</h3>
            <p class="mb-4">Create your first category to organize your marketplace listings</p>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-add-new">
                <i class="fas fa-plus me-2"></i>Create First Category
            </a>
        </div>
    @endforelse
</div>
@endsection
