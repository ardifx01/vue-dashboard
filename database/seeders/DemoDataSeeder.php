<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates demo users with proper roles and permissions following Vuexy standards
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            'view-dashboard',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-permissions',
            'manage-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        
        $managerRole->givePermissionTo([
            'view-dashboard',
            'view-users',
            'create-users',
            'edit-users',
            'view-roles',
        ]);
        
        $userRole->givePermissionTo([
            'view-dashboard',
        ]);

        // Create demo users
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin'),
                'avatar' => '/images/avatars/1.png',
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');

        $managerUser = User::updateOrCreate(
            ['email' => 'manager@demo.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('manager'),
                'avatar' => '/images/avatars/2.png',
                'email_verified_at' => now(),
            ]
        );
        $managerUser->assignRole('manager');

        $clientUser = User::updateOrCreate(
            ['email' => 'client@demo.com'],
            [
                'name' => 'Client User',
                'password' => Hash::make('client'),
                'avatar' => '/images/avatars/3.png',
                'email_verified_at' => now(),
            ]
        );
        $clientUser->assignRole('user');

        $this->command->info('Demo users created successfully!');
        $this->command->info('Admin: admin@demo.com / admin');
        $this->command->info('Manager: manager@demo.com / manager');
        $this->command->info('Client: client@demo.com / client');
    }
}
