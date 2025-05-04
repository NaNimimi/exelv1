<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Faqat admin rol yaratishi mumkin.');
        }

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        if ($request->has('permission_ids') && is_array($request->permission_ids)) {
            $permissions = Permission::whereIn('id', $request->permission_ids)
                ->where('guard_name', 'web')
                ->get();
            $role->givePermissionTo($permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Rol muvaffaqiyatli yaratildi.');
    }

    public function destroy(Role $role)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Faqat admin rolni o\'chirishi mumkin.');
        }

        if ($role->guard_name !== 'web') {
            return response()->json(['message' => 'Faqat web guard ro\'yxati o\'chirilishi mumkin!'], 403);
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol muvaffaqiyatli o\'chirildi.');
    }

    public function assignPermission(Request $request, Role $role)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Faqat admin ruxsat biriktirishi mumkin.');
        }

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
        $role->givePermissionTo($permissions);

        return redirect()->route('roles.index')->with('success', 'Ruxsatlar muvaffaqiyatli biriktirildi.');
    }

    public function revokePermission(Request $request, Role $role)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Faqat admin ruxsat olib tashlashi mumkin.');
        }

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

        return redirect()->route('roles.index')->with('success', 'Ruxsatlar muvaffaqiyatli olib tashlandi.');
    }

    public function assignRoleToUser(Request $request, User $user)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Faqat admin foydalanuvchiga rol biriktirishi mumkin.');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id,guard_name,web',
        ]);

        $role = Role::where('id', $request->role_id)
            ->where('guard_name', 'web')
            ->firstOrFail();
        $user->assignRole($role);

        return redirect()->route('roles.index')->with('success', 'Rol foydalanuvchiga muvaffaqiyatli biriktirildi.');
    }

    public function revokeRoleFromUser(Request $request, User $user)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Faqat admin foydalanuvchidan rol olib tashlashi mumkin.');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id,guard_name,web',
        ]);

        $role = Role::where('id', $request->role_id)
            ->where('guard_name', 'web')
            ->firstOrFail();
        $user->removeRole($role);

        return redirect()->route('roles.index')->with('success', 'Rol foydalanuvchidan muvaffaqiyatli olib tashlandi.');
    }
}