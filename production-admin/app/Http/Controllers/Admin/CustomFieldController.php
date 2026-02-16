<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomFieldController extends Controller
{
    /**
     * Display a listing of custom fields
     */
    public function index()
    {
        $fields = CustomField::with('categories')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => CustomField::count(),
            'active' => CustomField::where('is_active', true)->count(),
            'inactive' => CustomField::where('is_active', false)->count(),
            'required' => CustomField::where('is_required', true)->count(),
        ];

        $categories = Category::orderBy('name')->get();
        $fieldTypes = CustomField::getFieldTypes();
        $fieldGroups = CustomField::getFieldGroups();

        return view('admin.custom-fields.index', compact('fields', 'stats', 'categories', 'fieldTypes', 'fieldGroups'));
    }

    /**
     * Get all custom fields (API endpoint)
     */
    public function all()
    {
        $fields = CustomField::active()
            ->ordered()
            ->with('categories')
            ->get();

        return response()->json([
            'success' => true,
            'fields' => $fields
        ]);
    }

    /**
     * Get custom fields for a specific category
     */
    public function getFieldsByCategory($categoryId)
    {
        $fields = CustomField::getFieldsForCategory($categoryId);

        return response()->json([
            'success' => true,
            'fields' => $fields
        ]);
    }

    /**
     * Store a newly created custom field
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(CustomField::getFieldTypes())),
            'placeholder' => 'nullable|string|max:255',
            'default_value' => 'nullable|string|max:255',
            'min_value' => 'nullable|string|max:255',
            'max_value' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*' => 'required|string',
            'validation_rules' => 'nullable|array',
            'show_in_add_form' => 'nullable|boolean',
            'show_in_search' => 'nullable|boolean',
            'show_in_details' => 'nullable|boolean',
            'show_on_listing_card' => 'nullable|boolean',
            'is_required' => 'nullable|boolean',
            'is_searchable' => 'nullable|boolean',
            'is_filterable' => 'nullable|boolean',
            'allow_multiple_selection' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'field_group' => 'nullable|string',
            'order' => 'nullable|integer',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        } else {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        // Check for duplicate slug
        $count = 1;
        $originalSlug = $validated['slug'];
        while (CustomField::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        // Set defaults for boolean fields if not provided
        $validated['show_in_add_form'] = $validated['show_in_add_form'] ?? true;
        $validated['show_in_search'] = $validated['show_in_search'] ?? true;
        $validated['show_in_details'] = $validated['show_in_details'] ?? true;
        $validated['show_on_listing_card'] = $validated['show_on_listing_card'] ?? true;
        $validated['is_required'] = $validated['is_required'] ?? false;
        $validated['is_searchable'] = $validated['is_searchable'] ?? true;
        $validated['is_filterable'] = $validated['is_filterable'] ?? true;
        $validated['allow_multiple_selection'] = $validated['allow_multiple_selection'] ?? false;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $field = CustomField::create($validated);

        // Attach categories if provided
        if ($request->has('categories') && is_array($request->categories)) {
            $categoriesData = [];
            foreach ($request->categories as $index => $categoryId) {
                $categoriesData[$categoryId] = [
                    'order' => $index,
                    'is_required' => $request->is_required ?? false,
                ];
            }
            $field->categories()->attach($categoriesData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Custom field created successfully',
            'field' => $field->load('categories')
        ]);
    }

    /**
     * Display the specified custom field
     */
    public function show($id)
    {
        $field = CustomField::with('categories')->findOrFail($id);

        return response()->json([
            'success' => true,
            'field' => $field
        ]);
    }

    /**
     * Update the specified custom field
     */
    public function update(Request $request, $id)
    {
        $field = CustomField::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(CustomField::getFieldTypes())),
            'placeholder' => 'nullable|string|max:255',
            'default_value' => 'nullable|string|max:255',
            'min_value' => 'nullable|string|max:255',
            'max_value' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*' => 'required|string',
            'validation_rules' => 'nullable|array',
            'show_in_add_form' => 'nullable|boolean',
            'show_in_search' => 'nullable|boolean',
            'show_in_details' => 'nullable|boolean',
            'show_on_listing_card' => 'nullable|boolean',
            'is_required' => 'nullable|boolean',
            'is_searchable' => 'nullable|boolean',
            'is_filterable' => 'nullable|boolean',
            'allow_multiple_selection' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'field_group' => 'nullable|string',
            'order' => 'nullable|integer',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        // Update slug if name or slug changed
        if (!empty($validated['slug']) && $validated['slug'] !== $field->slug) {
            $validated['slug'] = Str::slug($validated['slug']);
        } elseif ($validated['name'] !== $field->name && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);

            // Check for duplicate slug
            $count = 1;
            $originalSlug = $validated['slug'];
            while (CustomField::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        } else {
            // Keep existing slug
            unset($validated['slug']);
        }

        // Set defaults for boolean fields if not provided
        $validated['show_in_add_form'] = $validated['show_in_add_form'] ?? $field->show_in_add_form;
        $validated['show_in_search'] = $validated['show_in_search'] ?? $field->show_in_search;
        $validated['show_in_details'] = $validated['show_in_details'] ?? $field->show_in_details;
        $validated['show_on_listing_card'] = $validated['show_on_listing_card'] ?? $field->show_on_listing_card;
        $validated['is_required'] = $validated['is_required'] ?? $field->is_required;
        $validated['is_searchable'] = $validated['is_searchable'] ?? $field->is_searchable;
        $validated['is_filterable'] = $validated['is_filterable'] ?? $field->is_filterable;
        $validated['allow_multiple_selection'] = $validated['allow_multiple_selection'] ?? $field->allow_multiple_selection;
        $validated['is_active'] = $validated['is_active'] ?? $field->is_active;

        $field->update($validated);

        // Sync categories
        if ($request->has('categories')) {
            $categoriesData = [];
            if (is_array($request->categories)) {
                foreach ($request->categories as $index => $categoryId) {
                    $categoriesData[$categoryId] = [
                        'order' => $index,
                        'is_required' => $request->is_required ?? false,
                    ];
                }
            }
            $field->categories()->sync($categoriesData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Custom field updated successfully',
            'field' => $field->fresh('categories')
        ]);
    }

    /**
     * Remove the specified custom field
     */
    public function destroy($id)
    {
        $field = CustomField::findOrFail($id);
        $field->delete();

        return response()->json([
            'success' => true,
            'message' => 'Custom field deleted successfully'
        ]);
    }

    /**
     * Toggle field active status
     */
    public function toggleStatus($id)
    {
        $field = CustomField::findOrFail($id);
        $field->is_active = !$field->is_active;
        $field->save();

        return response()->json([
            'success' => true,
            'message' => 'Field status updated successfully',
            'is_active' => $field->is_active
        ]);
    }

    /**
     * Reorder custom fields
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:custom_fields,id',
            'fields.*.order' => 'required|integer',
        ]);

        foreach ($request->fields as $fieldData) {
            CustomField::where('id', $fieldData['id'])->update(['order' => $fieldData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fields reordered successfully'
        ]);
    }

    /**
     * Duplicate a custom field
     */
    public function duplicate($id)
    {
        $field = CustomField::with('categories')->findOrFail($id);
        
        $newField = $field->replicate();
        $newField->name = $field->name . ' (Copy)';
        $newField->slug = Str::slug($newField->name);
        
        // Ensure unique slug
        $count = 1;
        $originalSlug = $newField->slug;
        while (CustomField::where('slug', $newField->slug)->exists()) {
            $newField->slug = $originalSlug . '-' . $count;
            $count++;
        }
        
        $newField->save();
        
        // Copy category relationships
        $categoryData = [];
        foreach ($field->categories as $category) {
            $categoryData[$category->id] = [
                'order' => $category->pivot->order,
                'is_required' => $category->pivot->is_required,
            ];
        }
        $newField->categories()->attach($categoryData);
        
        return response()->json([
            'success' => true,
            'message' => 'Field duplicated successfully',
            'field' => $newField->load('categories')
        ]);
    }
}
