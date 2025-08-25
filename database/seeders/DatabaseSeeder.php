<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call(RolePermissionSeeder::class);
        
        // Create test users
        User::factory(10)->create();

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@vue-dashboard.dev',
        ]);
    }
}
