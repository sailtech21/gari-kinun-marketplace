<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    public function index()
    {
        $advertisements = Advertisement::orderBy('order')->orderBy('created_at', 'desc')->get();
        
        $stats = [
            'total' => Advertisement::count(),
            'active' => Advertisement::active()->count(),
            'inactive' => Advertisement::where('is_active', false)->count(),
            'expired' => Advertisement::where('end_date', '<', now())->count(),
        ];

        return view('admin.advertisements.index', compact('advertisements', 'stats'));
    }

    public function create()
    {
        return view('admin.advertisements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
            'button_text' => 'nullable|string|max:100',
            'position' => 'required|in:home,listing,category,sidebar',
            'type' => 'required|in:banner,popup,sidebar',
            'order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('advertisements', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        $advertisement = Advertisement::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Advertisement created successfully',
            'advertisement' => $advertisement
        ]);
    }

    public function edit($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        return view('admin.advertisements.edit', compact('advertisement'));
    }

    public function update(Request $request, $id)
    {
        $advertisement = Advertisement::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
            'button_text' => 'nullable|string|max:100',
            'position' => 'required|in:home,listing,category,sidebar',
            'type' => 'required|in:banner,popup,sidebar',
            'order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            // Delete old image
            if ($advertisement->image) {
                Storage::disk('public')->delete($advertisement->image);
            }
            $data['image'] = $request->file('image')->store('advertisements', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        $advertisement->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Advertisement updated successfully',
            'advertisement' => $advertisement->fresh()
        ]);
    }

    public function destroy($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        
        // Delete image
        if ($advertisement->image) {
            Storage::disk('public')->delete($advertisement->image);
        }

        $advertisement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Advertisement deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        $advertisement->is_active = !$advertisement->is_active;
        $advertisement->save();

        return response()->json([
            'success' => true,
            'message' => 'Advertisement status updated successfully',
            'is_active' => $advertisement->is_active
        ]);
    }

    public function all()
    {
        $advertisements = Advertisement::orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json(['advertisements' => $advertisements]);
    }

    public function show($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        return response()->json(['advertisement' => $advertisement]);
    }
}
