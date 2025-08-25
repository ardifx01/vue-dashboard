<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'dashboard.view',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign basic permissions to user
        $userRole->givePermissionTo(['dashboard.view']);

        // Create admin user if doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@vue-dashboard.dev'],
            [
                'name' => 'Administrator',
                'email' => 'admin@vue-dashboard.dev',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $adminUser->assignRole('admin');

        // Assign user role to existing users
        User::whereDoesntHave('roles')->each(function ($user) {
            $user->assignRole('user');
        });
    }
}
