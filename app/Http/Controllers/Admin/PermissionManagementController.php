<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionManagementController extends Controller
{
    public function __construct()
    {
        // Use permission-based middleware for granular access control
        // Only users with 'view permissions' can view the list
        // Only super admin (who has all permissions) can create/edit/delete permissions
        $this->middleware(['auth', 'permission:view permissions'])->only(['index']);
        $this->middleware(['auth', 'permission:assign permissions'])->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $permissions = Permission::paginate(10);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        // Protect critical system permissions from deletion
        $criticalPermissions = [
            'view users', 'create users', 'edit users', 'delete users',
            'view roles', 'create roles', 'edit roles', 'delete roles',
            'view permissions', 'assign permissions'
        ];

        if (in_array($permission->name, $criticalPermissions)) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Cannot delete critical system permissions.');
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
