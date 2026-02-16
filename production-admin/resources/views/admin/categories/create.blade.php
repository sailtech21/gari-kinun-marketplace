@extends('admin.layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Create New Category</h2>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Categories
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="Car" {{ old('type') == 'Car' ? 'selected' : '' }}>Car</option>
                            <option value="Bike" {{ old('type') == 'Bike' ? 'selected' : '' }}>Bike</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="parent_id" class="form-label">Parent Category</label>
                        <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                            <option value="">None (Main Category)</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ (old('parent_id', request('parent_id')) == $parent->id) ? 'selected' : '' }}>
                                    {{ $parent->name }} ({{ $parent->type }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Leave empty to create a main category</small>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="icon" class="form-label">Icon Class</label>
                        <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                               id="icon" name="icon" value="{{ old('icon') }}" 
                               placeholder="e.g., fas fa-car, fas fa-motorcycle">
                        <small class="text-muted">Font Awesome icon class (optional)</small>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    <small class="text-muted">Brief description of the category (optional)</small>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active (Users can see this category)
                    </label>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5><i class="fas fa-info-circle"></i> Icon Examples</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <i class="fas fa-car"></i> <code>fas fa-car</code>
                </div>
                <div class="col-md-3 mb-2">
                    <i class="fas fa-motorcycle"></i> <code>fas fa-motorcycle</code>
                </div>
                <div class="col-md-3 mb-2">
                    <i class="fas fa-truck"></i> <code>fas fa-truck</code>
                </div>
                <div class="col-md-3 mb-2">
                    <i class="fas fa-bus"></i> <code>fas fa-bus</code>
                </div>
                <div class="col-md-3 mb-2">
                    <i class="fas fa-bicycle"></i> <code>fas fa-bicycle</code>
                </div>
                <div class="col-md-3 mb-2">
                    <i class="fas fa-taxi"></i> <code>fas fa-taxi</code>
                </div>
                <div class="col-md-3 mb-2">
                    <i class="fas fa-shuttle-van"></i> <code>fas fa-shuttle-van</code>
                </div>
                <div class="col-md-3 mb-2">
                    <i class="fas fa-tractor"></i> <code>fas fa-tractor</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
