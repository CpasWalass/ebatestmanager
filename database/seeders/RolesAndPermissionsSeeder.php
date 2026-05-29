<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run()
    {
        // Reset cached roles and permissions
        if (app()->bound('cache')) {
            app()['cache']->forget('spatie.permission.cache');
        }

        // Permissions (minimal set to start)
        $permissions = [
            'manage projects',
            'manage testcases',
            'assign tests',
            'view reports',
            'manage users',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Roles
        $roles = [
            'chef_project' => ['manage projects', 'manage testcases', 'assign tests', 'view reports', 'manage users'],
            'tester' => ['manage testcases', 'view reports'],
            'developer' => ['view reports'],
            'client' => ['view reports'],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($perms);
        }

        // Create default chef de projet user if none exists
        if (!User::where('email', 'chef@ebatest.local')->exists()) {
            $user = User::create([
                'name' => 'Chef de Projet',
                'email' => 'chef@ebatest.local',
                'email_verified_at' => now(),
                'password' => bcrypt('password'), // change in production
            ]);

            $user->assignRole('chef_project');
        }
    }
}
