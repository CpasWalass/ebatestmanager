<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Client;
use App\Models\User;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the tenant 'bubedra'
        $tenantId = 'bubedra';
        
        $tenant = Tenant::firstOrCreate(
            ['id' => $tenantId],
            [
                'name' => 'BUBEDRA',
                'data' => [
                    'domain' => 'bubedra.ebatest.local'
                ]
            ]
        );

        // 2. Create the Client associated with this tenant
        $client = Client::firstOrCreate(
            ['email' => 'contact@bubedra.bj'],
            [
                'name' => 'Bureau Béninois des Droits d\'Auteur (BUBEDRA)',
                'phone' => '+229 00 00 00 00',
                'address' => 'Cotonou, Bénin',
                'tenant_id' => $tenantId,
            ]
        );

        // 3. Create the User (role: client)
        $user = User::firstOrCreate(
            ['email' => 'client@bubedra.bj'],
            [
                'name' => 'Représentant BUBEDRA',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'tenant_id' => $tenantId,
            ]
        );

        // Assign 'client' role
        if (!$user->hasRole('client')) {
            $user->assignRole('client');
        }

        // 4. Also assign the chef, testeur and developpeur to this tenant
        // so they can interact with bubedra's projects (or we just let them have no tenant_id which might mean they are super-users)
        // Usually, in a multi-tenant app, internal staff might not have a tenant_id but access all tenants.
        // We'll leave them as is.
    }
}
