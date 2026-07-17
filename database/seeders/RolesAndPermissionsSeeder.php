<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Tenant;

class RolesAndPermissionsSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Reset du cache Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Toutes les permissions
        $permissions = [
            'manage projects',
            'manage testcases',
            'assign tests',
            'view reports',
            'manage users',
            'respond_to_reports',  
            'manage clients',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $tenant = Tenant::firstOrCreate(
            ['id' => 'eba_togo'],
            [
                'name' => 'EBA_TOGO',
                'data' => [
                    'domain' => 'ebatogo.ebatest.local'
                ]
            ]
        );

        // Rôles et leurs permissions
        $roles = [
            'chef_project' => [
                'manage projects',
                'manage testcases',
                'assign tests',
                'view reports',
                'manage users',
                'respond_to_reports',
                'manage clients',
            ],
            'tester' => [
                'manage testcases',
                'view reports',
            ],
            'developer' => [
                'view reports',
                'respond_to_reports',  
            ],
            'client' => [
                'view reports',
                'manage testcases',    
            ],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }

        // Créer le chef de projet par défaut s'il n'existe pas
        if (!User::where('email', 'chef@ebatest.local')->exists()) {
            $user = User::create([
                'name'              => 'Chef de Projet',
                'email'             => 'chef@ebatest.local',
                'email_verified_at' => now(),
                'password'          => bcrypt('password'),
                'tenant_id'        => $tenant->id,
            ]);
            $user->assignRole('chef_project');
        }

        // Créer un testeur de démo
        if (!User::where('email', 'testeur@ebatest.local')->exists()) {
            $user = User::create([
                'name'              => 'Jean Testeur',
                'email'             => 'testeur@ebatest.local',
                'email_verified_at' => now(),
                'password'          => bcrypt('password'),
                'tenant_id'        => $tenant->id,
            ]);
            $user->assignRole('tester');
        }

        // Créer un développeur de démo
        if (!User::where('email', 'dev@ebatest.local')->exists()) {
            $user = User::create([
                'name'              => 'Marie Développeur',
                'email'             => 'dev@ebatest.local',
                'email_verified_at' => now(),
                'password'          => bcrypt('password'),
                'tenant_id'        => $tenant->id,
            ]);
            $user->assignRole('developer');
        }
    }
}
