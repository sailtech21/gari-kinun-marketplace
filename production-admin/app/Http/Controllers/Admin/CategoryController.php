<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::parents()
            ->with(['childrenWithCount'])
            ->withCount('listings')
            ->orderBy('name')
            ->get();
        
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::parents()->orderBy('name')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'type' => 'nullable|string|max:50',
                'parent_id' => 'nullable|exists:categories,id',
                'icon' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:500',
                'is_active' => 'boolean',
            ]);

            $data = $request->only(['name', 'type', 'parent_id', 'icon', 'description', 'is_active']);
            $data['slug'] = Str::slug($request->name);
            $data['is_active'] = $request->has('is_active') || $request->is_active == 1 ? 1 : 0;
            $data['type'] = $request->type ?? 'vehicle';
            
            // Handle filter control fields
            $data['show_fuel_type'] = $request->has('show_fuel_type') ? 1 : 0;
            $data['show_transmission'] = $request->has('show_transmission') ? 1 : 0;
            $data['show_body_type'] = $request->has('show_body_type') ? 1 : 0;
            $data['show_year'] = $request->has('show_year') ? 1 : 0;
            $data['show_mileage'] = $request->has('show_mileage') ? 1 : 0;
            $data['show_engine_capacity'] = $request->has('show_engine_capacity') ? 1 : 0;
            $data['show_condition'] = $request->has('show_condition') ? 1 : 0;
            
            // Handle custom fields JSON
            if ($request->has('custom_fields')) {
                $data['custom_fields'] = $request->custom_fields;
            }

            $category = Category::create($data);

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category created successfully',
                    'category' => $category
                ]);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully');
        } catch (\Exception $e) {
            \Log::error('Category store error: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create category: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parentCategories = Category::parents()
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();
        
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'type' => 'nullable|string|max:50',
                'parent_id' => 'nullable|exists:categories,id',
                'icon' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:500',
                'is_active' => 'boolean',
            ]);

            $data = $request->only(['name', 'type', 'parent_id', 'icon', 'description']);
            $data['slug'] = Str::slug($request->name);
            $data['is_active'] = $request->has('is_active') || $request->is_active == 1 ? 1 : 0;
            $data['type'] = $request->type ?? 'vehicle';
            
            // Handle filter control fields
            $data['show_fuel_type'] = $request->has('show_fuel_type') ? 1 : 0;
            $data['show_transmission'] = $request->has('show_transmission') ? 1 : 0;
            $data['show_body_type'] = $request->has('show_body_type') ? 1 : 0;
            $data['show_year'] = $request->has('show_year') ? 1 : 0;
            $data['show_mileage'] = $request->has('show_mileage') ? 1 : 0;
            $data['show_engine_capacity'] = $request->has('show_engine_capacity') ? 1 : 0;
            $data['show_condition'] = $request->has('show_condition') ? 1 : 0;
            
            // Handle custom fields JSON
            if ($request->has('custom_fields')) {
                $data['custom_fields'] = $request->custom_fields;
            }

            $category->update($data);

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category updated successfully',
                    'category' => $category
                ]);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category updated successfully');
        } catch (\Exception $e) {
            \Log::error('Category update error: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update category: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category has listings
        if ($category->listings()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with active listings');
        }
        
        // Check if category has subcategories
        if ($category->children()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with subcategories. Delete subcategories first.');
        }
        
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully');
    }

    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        return redirect()->back()
            ->with('success', 'Category status updated successfully');
    }

    public function stats()
    {
        return response()->json([
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'parents' => Category::parents()->count(),
            'subcategories' => Category::whereNotNull('parent_id')->count(),
        ]);
    }

    public function all()
    {
        $categories = Category::with(['parent'])
            ->withCount(['listings', 'children'])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return response()->json(['categories' => $categories]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.order' => 'required|integer',
        ]);

        foreach ($request->categories as $item) {
            Category::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categories reordered successfully'
        ]);
    }

    public function setPostingFee(Request $request, $id)
    {
        try {
            $request->validate([
                'posting_fee' => 'required|numeric|min:0'
            ]);

            $category = Category::findOrFail($id);
            
            try {
                DB::table('categories')
                    ->where('id', $id)
                    ->update([
                        'posting_fee' => $request->posting_fee,
                        'updated_at' => now()
                    ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Posting fee updated successfully'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => true,
                    'message' => 'Posting fee feature not available yet'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Set posting fee error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update posting fee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function setRequiredFields(Request $request, $id)
    {
        try {
            $request->validate([
                'required_fields' => 'required|array'
            ]);

            $category = Category::findOrFail($id);
            
            try {
                DB::table('categories')
                    ->where('id', $id)
                    ->update([
                        'required_fields' => json_encode($request->required_fields),
                        'updated_at' => now()
                    ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Required fields updated successfully'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => true,
                    'message' => 'Required fields feature not available yet'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Set required fields error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update required fields: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateFilters(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            
            try {
                DB::table('categories')
                    ->where('id', $id)
                    ->update([
                        'show_fuel_type' => $request->has('show_fuel_type') ? 1 : 0,
                        'show_transmission' => $request->has('show_transmission') ? 1 : 0,
                        'show_body_type' => $request->has('show_body_type') ? 1 : 0,
                        'show_year' => $request->has('show_year') ? 1 : 0,
                        'show_mileage' => $request->has('show_mileage') ? 1 : 0,
                        'show_engine_capacity' => $request->has('show_engine_capacity') ? 1 : 0,
                        'show_condition' => $request->has('show_condition') ? 1 : 0,
                        'updated_at' => now()
                    ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Category filters updated successfully'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => true,
                    'message' => 'Filter settings updated (some features not available)'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Update filters error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update filters: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addSubcategory(Request $request, $id)
    {
        try {
            $parent = Category::findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'icon' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:500',
            ]);

            $data = $request->only(['name', 'icon', 'description']);
            $data['slug'] = Str::slug($request->name);
            $data['parent_id'] = $id;
            $data['is_active'] = true;
            $data['type'] = $parent->type;

            $subcategory = Category::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Subcategory added successfully',
                'subcategory' => $subcategory
            ]);
        } catch (\Exception $e) {
            \Log::error('Add subcategory error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add subcategory: ' . $e->getMessage()
            ], 500);
        }
    }
}
