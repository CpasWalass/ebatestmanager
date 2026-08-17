<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage projects',
            'manage testcases',
            'assign tests',
            'view reports',
            'manage users',
            'manage clients',
            'respond to reports',
            // Nouvelle permission dédiée : un client peut valider/rejeter un cas de
            // test UAT (client_status/client_comment) mais ne doit jamais pouvoir
            // éditer son contenu — avant, le rôle "client" avait "manage testcases",
            // ce qui l'autorisait en théorie à tout modifier.
            'validate testcases',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $tenant = Tenant::firstOrCreate(
            ['id' => config('app.default_tenant_id', 'eba')],
            ['name' => 'e-Business Afrique']
        );

        $roles = [
            'chef_project' => [
                'manage projects',
                'manage testcases',
                'assign tests',
                'view reports',
                'manage users',
                'manage clients',
                'respond to reports',
            ],
            'tester' => [
                'manage testcases',
                'view reports',
            ],
            'developer' => [
                'view reports',
                'respond to reports',
            ],
            'client' => [
                'view reports',
                'validate testcases',
            ],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }

        // Garde-fou : ces comptes ont un mot de passe connu et prévisible.
        // Ils ne doivent JAMAIS être créés en production.
        if (app()->isProduction()) {
            return;
        }

        $demoUsers = [
            ['email' => 'chef@ebatest.local', 'name' => 'Chef de Projet', 'role' => 'chef_project'],
            ['email' => 'testeur@ebatest.local', 'name' => 'Jean Testeur', 'role' => 'tester'],
            ['email' => 'dev@ebatest.local', 'name' => 'Marie Développeur', 'role' => 'developer'],
        ];

        foreach ($demoUsers as $demo) {
            $user = User::firstOrCreate(
                ['email' => $demo['email']],
                [
                    'name' => $demo['name'],
                    'email_verified_at' => now(),
                    'password' => bcrypt(config('app.demo_password', 'password')),
                    'tenant_id' => $tenant->id,
                ]
            );

            if (! $user->hasRole($demo['role'])) {
                $user->assignRole($demo['role']);
            }
        }
    }
}
