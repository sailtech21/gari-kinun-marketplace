<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with(['parent', 'districtsWithCount'])
            ->divisions()
            ->orderBy('order')
            ->orderBy('name')
            ->get();
        
        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        Location::create($data);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully');
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        return response()->json([
            'success' => true,
            'location' => $location
        ]);
    }

    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $location->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);

        // Check if it has sub-locations (districts under division)
        if ($location->districts()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete location with sub-locations');
        }

        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $location = Location::findOrFail($id);
        $location->is_active = !$location->is_active;
        $location->save();

        return redirect()->back()
            ->with('success', 'Location status updated successfully');
    }

    public function all()
    {
        $locations = Location::orderBy('order')->orderBy('name')->get();
        return response()->json(['locations' => $locations]);
    }

    public function stats()
    {
        $stats = [
            'total' => Location::count(),
            'divisions' => Location::where('type', 'division')->count(),
            'districts' => Location::where('type', 'district')->count(),
            'active' => Location::where('is_active', true)->count(),
        ];

        return response()->json($stats);
    }

    public function addDivision(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
        ]);

        $location = Location::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => 'division',
            'country' => 'Bangladesh',
            'is_active' => true,
            'order' => Location::where('type', 'division')->max('order') + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Division added successfully',
            'location' => $location
        ]);
    }

    public function addDistrict(Request $request, $divisionId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $division = Location::where('type', 'division')->findOrFail($divisionId);

        $location = Location::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => 'district',
            'parent_id' => $division->id,
            'country' => 'Bangladesh',
            'state' => $division->name,
            'is_active' => true,
            'order' => Location::where('type', 'district')->where('parent_id', $divisionId)->max('order') + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'District added successfully',
            'location' => $location
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'locations' => 'required|array',
            'locations.*.id' => 'required|exists:locations,id',
            'locations.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->locations as $locationData) {
            Location::where('id', $locationData['id'])
                ->update(['order' => $locationData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Locations reordered successfully'
        ]);
    }
}
