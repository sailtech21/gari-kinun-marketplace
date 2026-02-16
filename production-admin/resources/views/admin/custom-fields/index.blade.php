@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Custom Fields Management</h1>
            <p class="text-muted small mb-0">Create and manage dynamic form fields for your ad categories</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fieldModal" onclick="openCreateModal()">
            <i class="fas fa-plus"></i> Add Custom Field
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Fields</p>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-th-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Active Fields</p>
                            <h3 class="mb-0 text-success">{{ $stats['active'] }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Inactive Fields</p>
                            <h3 class="mb-0 text-warning">{{ $stats['inactive'] }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-pause-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Required Fields</p>
                            <h3 class="mb-0 text-danger">{{ $stats['required'] }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-asterisk fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Fields Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Custom Fields</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Field Name</th>
                            <th>Field Label</th>
                            <th>Category</th>
                            <th>Field Type</th>
                            <th>Required</th>
                            <th>Show in Filter</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="fieldsTableBody">
                        @forelse($fields as $field)
                        <tr data-field-id="{{ $field->id }}">
                            <td>
                                <code>{{ $field->slug }}</code>
                            </td>
                            <td>
                                <strong>{{ $field->name }}</strong>
                                @if($field->field_group)
                                    <br><small class="badge bg-light text-dark">{{ $fieldGroups[$field->field_group] ?? $field->field_group }}</small>
                                @endif
                            </td>
                            <td>
                                @if($field->categories->count() > 0)
                                    @foreach($field->categories->take(2) as $category)
                                        <span class="badge bg-info">{{ $category->name }}</span>
                                    @endforeach
                                    @if($field->categories->count() > 2)
                                        <span class="badge bg-secondary">+{{ $field->categories->count() - 2 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">All</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $typeIcons = [
                                        'text' => 'fa-font',
                                        'textarea' => 'fa-align-left',
                                        'number' => 'fa-hashtag',
                                        'select' => 'fa-list',
                                        'radio' => 'fa-dot-circle',
                                        'checkbox' => 'fa-check-square',
                                        'date' => 'fa-calendar',
                                        'email' => 'fa-envelope',
                                        'url' => 'fa-link',
                                        'tel' => 'fa-phone',
                                        'file' => 'fa-file-upload',
                                    ];
                                    $icon = $typeIcons[$field->type] ?? 'fa-question';
                                @endphp
                                <i class="fas {{ $icon }}"></i> {{ $fieldTypes[$field->type] ?? $field->type }}
                            </td>
                            <td>
                                @if($field->is_required)
                                    <span class="badge bg-danger">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                @if($field->show_in_search)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-toggle" data-id="{{ $field->id }}" style="cursor: pointer;">
                                    <span class="badge bg-{{ $field->is_active ? 'success' : 'secondary' }}">
                                        {{ $field->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-sm btn-info" onclick="editField({{ $field->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteField({{ $field->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>No custom fields created yet. Click "Add Custom Field" to get started.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Field Modal -->
<div class="modal fade" id="fieldModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fieldModalTitle">Add New Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="fieldForm">
                    <input type="hidden" id="fieldId" value="">
                    
                    <!-- Basic Settings -->
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle"></i> Basic Settings</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Field Label * <small class="text-muted">(Display Name)</small></label>
                                <input type="text" class="form-control" id="fieldName" required placeholder="Example: Engine Capacity">
                                <small class="text-muted">This is what users will see</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Field Name <small class="text-muted">(System Key)</small></label>
                                <input type="text" class="form-control" id="fieldSlug" placeholder="Example: engine_cc">
                                <small class="text-muted">Auto-generated if empty</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Assign Category *</label>
                                <select class="form-select" id="fieldCategories" multiple size="5">
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple. Leave empty for all categories.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Field Group</label>
                                <select class="form-select" id="fieldGroup">
                                    <option value="">None</option>
                                    @foreach($fieldGroups as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Organize fields</small>
                            </div>
                        </div>
                    </div>

                    <!-- Field Type -->
                    <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-sliders-h"></i> Field Type Options</h6>
                    <div class="mb-3">
                        <label class="form-label">Field Type *</label>
                        <select class="form-select" id="fieldType" required onchange="toggleFieldOptions()">
                            <option value="">Select type...</option>
                            @foreach($fieldTypes as $type => $label)
                            <option value="{{ $type }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Options for Dropdown/Radio/Checkbox -->
                    <div class="mb-3" id="optionsSection" style="display: none;">
                        <label class="form-label">Options <small class="text-muted">(for Dropdown/Radio/Checkbox)</small></label>
                        <div id="optionsList" class="mb-2"></div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addOption()">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="reorderOptions()">
                            <i class="fas fa-sort"></i> Reorder
                        </button>
                        <small class="text-muted d-block mt-2">
                            <strong>Example:</strong> Fuel Type: Petrol, Diesel, Hybrid, Electric
                        </small>
                    </div>

                    <!-- Number Min/Max -->
                    <div class="row" id="numberSection" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Minimum Value</label>
                                <input type="text" class="form-control" id="minValue" placeholder="e.g., 0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Maximum Value</label>
                                <input type="text" class="form-control" id="maxValue" placeholder="e.g., 10000">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Placeholder Text</label>
                                <input type="text" class="form-control" id="fieldPlaceholder" placeholder="e.g., Enter engine capacity">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Default Value</label>
                                <input type="text" class="form-control" id="fieldDefaultValue">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Help Text</label>
                        <textarea class="form-control" id="fieldHelpText" rows="2" placeholder="Additional information to help users"></textarea>
                    </div>

                    <!-- Advanced Controls -->
                    <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-cog"></i> Advanced Controls</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>Visibility Settings</strong>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="showInAddForm" checked>
                                        <label class="form-check-label" for="showInAddForm">
                                            <i class="fas fa-plus-square text-primary"></i> Show in Add/Edit Ad Page
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="showInSearch" checked>
                                        <label class="form-check-label" for="showInSearch">
                                            <i class="fas fa-search text-info"></i> Show in Search Filter
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="showInDetails" checked>
                                        <label class="form-check-label" for="showInDetails">
                                            <i class="fas fa-eye text-success"></i> Show on Ad Details Page
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="showOnListingCard" checked>
                                        <label class="form-check-label" for="showOnListingCard">
                                            <i class="fas fa-th text-warning"></i> Show on Listing Card
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>Field Controls</strong>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="isRequired">
                                        <label class="form-check-label" for="isRequired">
                                            <i class="fas fa-asterisk text-danger"></i> <strong>Required Field</strong>
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="isSearchable" checked>
                                        <label class="form-check-label" for="isSearchable">
                                            <i class="fas fa-database text-primary"></i> Searchable (can be queried)
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="isFilterable" checked>
                                        <label class="form-check-label" for="isFilterable">
                                            <i class="fas fa-filter text-info"></i> Filterable (use as filter)
                                        </label>
                                    </div>
                                    <div class="form-check mb-2" id="multipleSelectionCheck" style="display: none;">
                                        <input class="form-check-input" type="checkbox" id="allowMultipleSelection">
                                        <label class="form-check-label" for="allowMultipleSelection">
                                            <i class="fas fa-check-double text-success"></i> Allow Multiple Selection
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isActive" checked>
                                        <label class="form-check-label" for="isActive">
                                            <i class="fas fa-toggle-on text-success"></i> Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Note:</strong> Fields marked as "Show in Search Filter" will automatically appear in the search sidebar with appropriate filter controls.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveField()">
                    <i class="fas fa-save"></i> <span id="saveButtonText">Create Field</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let optionCount = 0;

function openCreateModal() {
    document.getElementById('fieldModalTitle').textContent = 'Add New Field';
    document.getElementById('saveButtonText').textContent = 'Create Field';
    document.getElementById('fieldForm').reset();
    document.getElementById('fieldId').value = '';
    document.getElementById('fieldSlug').value = '';
    document.getElementById('optionsList').innerHTML = '';
    optionCount = 0;
    toggleFieldOptions();
}

function toggleFieldOptions() {
    const fieldType = document.getElementById('fieldType').value;
    const optionsSection = document.getElementById('optionsSection');
    const numberSection = document.getElementById('numberSection');
    const multipleSelectionCheck = document.getElementById('multipleSelectionCheck');
    
    // Show/hide options for dropdown, radio, checkbox
    if (['select', 'radio', 'checkbox'].includes(fieldType)) {
        optionsSection.style.display = 'block';
    } else {
        optionsSection.style.display = 'none';
    }
    
    // Show/hide min/max for number
    if (fieldType === 'number') {
        numberSection.style.display = 'block';
    } else {
        numberSection.style.display = 'none';
    }
    
    // Show/hide multiple selection for select and checkbox
    if (['select', 'checkbox'].includes(fieldType)) {
        multipleSelectionCheck.style.display = 'block';
    } else {
        multipleSelectionCheck.style.display = 'none';
    }
}

function addOption() {
    optionCount++;
    const optionHtml = `
        <div class="input-group mb-2" id="option-${optionCount}" draggable="true" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
            <span class="input-group-text" style="cursor: move;"><i class="fas fa-grip-vertical"></i></span>
            <input type="text" class="form-control option-value" placeholder="Option ${optionCount}" data-option-id="${optionCount}">
            <button class="btn btn-outline-danger" type="button" onclick="removeOption(${optionCount})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    document.getElementById('optionsList').insertAdjacentHTML('beforeend', optionHtml);
}

function removeOption(id) {
    document.getElementById(`option-${id}`).remove();
}

function reorderOptions() {
    alert('Drag and drop options to reorder them!');
}

// Drag and drop for options
let draggedOption = null;

function drag(event) {
    draggedOption = event.target.closest('.input-group');
}

function allowDrop(event) {
    event.preventDefault();
}

function drop(event) {
    event.preventDefault();
    const target = event.target.closest('.input-group');
    if (target && draggedOption && target !== draggedOption) {
        const parent = target.parentNode;
        const draggedIndex = Array.from(parent.children).indexOf(draggedOption);
        const targetIndex = Array.from(parent.children).indexOf(target);
        
        if (draggedIndex < targetIndex) {
            parent.insertBefore(draggedOption, target.nextSibling);
        } else {
            parent.insertBefore(draggedOption, target);
        }
    }
}

// Auto-generate slug from field name
document.getElementById('fieldName').addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[^a-z0-9]+/g, '_')
        .replace(/^_+|_+$/g, '');
    if (!document.getElementById('fieldSlug').value || document.getElementById('fieldId').value === '') {
        document.getElementById('fieldSlug').value = slug;
    }
});

async function saveField() {
    const fieldId = document.getElementById('fieldId').value;
    const isEdit = fieldId !== '';
    
    // Collect options in order
    const optionInputs = document.querySelectorAll('.option-value');
    const options = Array.from(optionInputs)
        .map(input => input.value.trim())
        .filter(value => value !== '');
    
    // Collect selected categories
    const categorySelect = document.getElementById('fieldCategories');
    const categories = Array.from(categorySelect.selectedOptions).map(opt => opt.value);
    
    const data = {
        name: document.getElementById('fieldName').value,
        type: document.getElementById('fieldType').value,
        placeholder: document.getElementById('fieldPlaceholder').value || '',
        default_value: document.getElementById('fieldDefaultValue').value || '',
        help_text: document.getElementById('fieldHelpText').value || '',
        show_in_add_form: document.getElementById('showInAddForm').checked ? 1 : 0,
        show_in_search: document.getElementById('showInSearch').checked ? 1 : 0,
        show_in_details: document.getElementById('showInDetails').checked ? 1 : 0,
        show_on_listing_card: document.getElementById('showOnListingCard').checked ? 1 : 0,
        is_required: document.getElementById('isRequired').checked ? 1 : 0,
        is_searchable: document.getElementById('isSearchable').checked ? 1 : 0,
        is_filterable: document.getElementById('isFilterable').checked ? 1 : 0,
        is_active: document.getElementById('isActive').checked ? 1 : 0,
        order: 0,
        categories: categories,
    };
    
    // Add optional fields only if they have values
    const slug = document.getElementById('fieldSlug').value.trim();
    if (slug) data.slug = slug;
    
    const minValue = document.getElementById('minValue')?.value;
    if (minValue) data.min_value = minValue;
    
    const maxValue = document.getElementById('maxValue')?.value;
    if (maxValue) data.max_value = maxValue;
    
    const fieldGroup = document.getElementById('fieldGroup').value;
    if (fieldGroup) data.field_group = fieldGroup;
    
    if (options.length > 0) data.options = options;
    
    const allowMultiple = document.getElementById('allowMultipleSelection')?.checked;
    if (allowMultiple) data.allow_multiple_selection = 1;
    
    try {
        const url = isEdit 
            ? `/admin/custom-fields/${fieldId}`
            : '/admin/custom-fields';
        
        const method = isEdit ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            const text = await response.text();
            console.error('Server response:', text);
            throw new Error(`Server error: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert('Error: ' + (result.message || 'Something went wrong'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error saving field: ' + error.message);
    }
}

async function editField(id) {
    try {
        const response = await fetch(`/admin/custom-fields/${id}`);
        
        if (!response.ok) {
            const text = await response.text();
            console.error('Server response:', text);
            throw new Error(`Server error: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            const field = result.field;
            
            document.getElementById('fieldModalTitle').textContent = 'Edit Custom Field';
            document.getElementById('saveButtonText').textContent = 'Update Field';
            document.getElementById('fieldId').value = field.id;
            document.getElementById('fieldName').value = field.name;
            document.getElementById('fieldSlug').value = field.slug;
            document.getElementById('fieldType').value = field.type;
            document.getElementById('fieldPlaceholder').value = field.placeholder || '';
            document.getElementById('fieldDefaultValue').value = field.default_value || '';
            document.getElementById('fieldHelpText').value = field.help_text || '';
            document.getElementById('fieldGroup').value = field.field_group || '';
            
            if (document.getElementById('minValue')) {
                document.getElementById('minValue').value = field.min_value || '';
            }
            if (document.getElementById('maxValue')) {
                document.getElementById('maxValue').value = field.max_value || '';
            }
            
            document.getElementById('showInAddForm').checked = field.show_in_add_form;
            document.getElementById('showInSearch').checked = field.show_in_search;
            document.getElementById('showInDetails').checked = field.show_in_details;
            document.getElementById('showOnListingCard').checked = field.show_on_listing_card;
            document.getElementById('isRequired').checked = field.is_required;
            document.getElementById('isSearchable').checked = field.is_searchable;
            document.getElementById('isFilterable').checked = field.is_filterable;
            
            if (document.getElementById('allowMultipleSelection')) {
                document.getElementById('allowMultipleSelection').checked = field.allow_multiple_selection;
            }
            
            document.getElementById('isActive').checked = field.is_active;
            
            // Set categories
            const categorySelect = document.getElementById('fieldCategories');
            const categoryIds = field.categories.map(cat => cat.id.toString());
            Array.from(categorySelect.options).forEach(option => {
                option.selected = categoryIds.includes(option.value);
            });
            
            // Set options if field has them
            document.getElementById('optionsList').innerHTML = '';
            optionCount = 0;
            if (field.options && Array.isArray(field.options)) {
                field.options.forEach(option => {
                    addOption();
                    const inputs = document.querySelectorAll('.option-value');
                    const lastInput = inputs[inputs.length - 1];
                    if (lastInput) lastInput.value = option;
                });
            }
            
            toggleFieldOptions();
            
            const modal = new bootstrap.Modal(document.getElementById('fieldModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error loading field: ' + error.message);
    }
}

async function deleteField(id) {
    if (!confirm('Are you sure you want to delete this custom field? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/admin/custom-fields/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (!response.ok) {
            const text = await response.text();
            console.error('Server response:', text);
            throw new Error(`Server error: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert('Error: ' + (result.message || 'Something went wrong'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting field: ' + error.message);
    }
}

// Toggle field status
document.addEventListener('click', async function(e) {
    if (e.target.closest('.status-toggle')) {
        const statusToggle = e.target.closest('.status-toggle');
        const fieldId = statusToggle.dataset.id;
        
        try {
            const response = await fetch(`/admin/custom-fields/${fieldId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (!response.ok) {
                const text = await response.text();
                console.error('Server response:', text);
                throw new Error(`Server error: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error toggling status: ' + error.message);
        }
    }
});

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection
