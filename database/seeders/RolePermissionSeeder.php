<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Создание разрешений
        $permissions = [
            'view-clients',
            'create-clients',
            'edit-clients',
            'delete-clients',
            'add-balance',
            'view-client-by-phone',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Создание ролей
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);

        $adminRole->givePermissionTo($permissions);

        $managerRole->givePermissionTo([
            'view-clients',
            'create-clients',
            'add-balance',
            'view-client-by-phone',
        ]);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('admin');
    }
}