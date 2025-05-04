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
            // Разрешения для клиентов
            'view-clients',
            'create-clients',
            'edit-clients',
            'delete-clients',
            'add-balance',
            'view-client-by-phone',
            // Разрешения для пользователей
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-user-by-email',
            'export-clients',
            'export-client',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Создание ролей
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);

        // Назначение всех разрешений администратору
        $adminRole->givePermissionTo($permissions);

        // Назначение ограниченных разрешений менеджеру
        $managerRole->givePermissionTo([
            'view-clients',
            'create-clients',
            'add-balance',
            'view-client-by-phone',
            'view-users',
            'view-user-by-email',
        ]);

        // Создание администратора
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('admin');
    }
}