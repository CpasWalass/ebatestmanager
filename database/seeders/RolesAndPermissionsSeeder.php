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
            'respond_to_reports',  // ← Nouveau : pour les développeurs
            'manage clients',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

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
                'respond_to_reports',  // ← Développeur peut maintenant répondre aux rapports
            ],
            'client' => [
                'view reports',
                'manage testcases',    // ← Client peut ajouter/modifier ses propres cas de test UAT
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
            ]);
            $user->assignRole('developer');
        }
    }
}
