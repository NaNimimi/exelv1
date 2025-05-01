<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->where('guard_name', 'web')->get();
        $permissions = Permission::where('guard_name', 'web')->get();
        $users = User::with('roles')->get();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'users' => $users,
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        if ($request->has('permission_ids') && is_array($request->permission_ids)) {
            $permissions = Permission::whereIn('id', $request->permission_ids)
                ->where('guard_name', 'web')
                ->get();
            $role->syncPermissions($permissions);
        }

      
    }

    public function destroy(Role $role)
    {
        // Ensure the role is using the 'web' guard
        if ($role->guard_name !== 'web') {
            return response()->json(['message' => 'Faqat web guard ro\'yxati o\'chirilishi mumkin!'], 403);
        }

        $role->delete();
    }

    public function assignPermission(Request $request, Role $role)
    {
        if ($role->guard_name !== 'web') {
            return response()->json(['message' => 'Faqat web guard ro\'yxatiga ruxsat biriktirilishi mumkin!'], 403);
        }

        $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id,guard_name,web',
        ]);

        $permissions = Permission::whereIn('id', $request->permission_ids)
            ->where('guard_name', 'web')
            ->get();
        $role->syncPermissions($permissions);

  
    }

    public function revokePermission(Request $request, Role $role)
    {
        if ($role->guard_name !== 'web') {
            return response()->json(['message' => 'Faqat web guard ro\'yxatidan ruxsat olib tashlanishi mumkin!'], 403);
        }

        $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id,guard_name,web',
        ]);

        $permissions = Permission::whereIn('id', $request->permission_ids)
            ->where('guard_name', 'web')
            ->get();
        foreach ($permissions as $permission) {
            $role->revokePermissionTo($permission);
        }

    
    }

    public function assignRoleToUser(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id,guard_name,web',
        ]);

        $role = Role::where('id', $request->role_id)
            ->where('guard_name', 'web')
            ->firstOrFail();
        $user->assignRole($role);

      
    }
}