<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = AdminRole::withCount('users')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:admin_roles,name',
            'slug' => 'nullable|string|max:255|unique:admin_roles,slug',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['permissions'] = $request->input('permissions', []);

        $role = AdminRole::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'role' => $role
        ]);
    }

    public function edit($id)
    {
        $role = AdminRole::withCount('users')->findOrFail($id);
        return response()->json($role);
    }

    public function update(Request $request, $id)
    {
        $role = AdminRole::findOrFail($id);

        if ($role->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'System roles cannot be modified'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:admin_roles,name,' . $id,
            'slug' => 'nullable|string|max:255|unique:admin_roles,slug,' . $id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['permissions'] = $request->input('permissions', []);

        $role->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }

    public function destroy($id)
    {
        $role = AdminRole::withCount('users')->findOrFail($id);

        if ($role->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'System roles cannot be deleted'
            ], 403);
        }

        if ($role->users_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role with assigned users'
            ], 400);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $role = AdminRole::findOrFail($id);

        if ($role->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'System roles cannot be disabled'
            ], 403);
        }

        $role->is_active = !$role->is_active;
        $role->save();

        return response()->json([
            'success' => true,
            'message' => 'Role status updated successfully',
            'is_active' => $role->is_active
        ]);
    }

    public function all()
    {
        $roles = AdminRole::active()->orderBy('name')->get();
        return response()->json($roles);
    }

    public function stats()
    {
        $total = AdminRole::count();
        $active = AdminRole::where('is_active', true)->count();
        $inactive = AdminRole::where('is_active', false)->count();
        $system = AdminRole::where('is_system', true)->count();
        $custom = AdminRole::where('is_system', false)->count();

        return response()->json([
            'total_roles' => $total,
            'active_roles' => $active,
            'inactive_roles' => $inactive,
            'system_roles' => $system,
            'custom_roles' => $custom,
        ]);
    }

    public function getPermissions()
    {
        $permissions = [
            'users' => [
                'users.view' => 'View Users',
                'users.create' => 'Create Users',
                'users.edit' => 'Edit Users',
                'users.delete' => 'Delete Users',
            ],
            'ads' => [
                'ads.view' => 'View Ads',
                'ads.create' => 'Create Ads',
                'ads.edit' => 'Edit Ads',
                'ads.delete' => 'Delete Ads',
                'ads.approve' => 'Approve Ads',
            ],
            'reports' => [
                'reports.view' => 'View Reports',
                'reports.manage' => 'Manage Reports',
                'reports.resolve' => 'Resolve Reports',
            ],
            'payments' => [
                'payments.view' => 'View Payments',
                'payments.manage' => 'Manage Payments',
                'payments.refund' => 'Process Refunds',
            ],
            'settings' => [
                'settings.view' => 'View Settings',
                'settings.edit' => 'Edit Settings',
            ],
            'content' => [
                'content.view' => 'View Content',
                'content.edit' => 'Edit Content',
                'content.delete' => 'Delete Content',
            ],
            'analytics' => [
                'analytics.view' => 'View Analytics',
                'analytics.export' => 'Export Analytics',
            ],
        ];

        return response()->json($permissions);
    }

    public function assignRole(Request $request, $userId)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:admin_roles,id',
        ]);

        $user = \App\Models\User::findOrFail($userId);
        $user->role_id = $validated['role_id'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Role assigned successfully'
        ]);
    }

    public function getUsersByRole($roleId)
    {
        $role = AdminRole::withCount('users')->findOrFail($roleId);
        $users = $role->users()->select('id', 'name', 'email', 'created_at')->get();

        return response()->json([
            'role' => $role,
            'users' => $users
        ]);
    }
}
