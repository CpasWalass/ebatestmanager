<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $tenantId = config('app.default_tenant_id', 'eba');

        $tenant = Tenant::firstOrCreate(
            ['id' => $tenantId],
            ['name' => 'e-Business Afrique']
        );

        $client = Client::firstOrCreate(
            ['email' => 'contact@client-demo.tg'],
            [
                'name' => 'Client de démonstration',
                'phone' => '+228 00 00 00 00',
                'address' => 'Lomé, Togo',
                'tenant_id' => $tenant->id,
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'client@ebatest.local'],
            [
                'name' => 'Représentant Client',
                'password' => bcrypt(config('app.demo_password', 'password')),
                'email_verified_at' => now(),
                'tenant_id' => $tenant->id,
            ]
        );

        if (! $user->hasRole('client')) {
            $user->assignRole('client');
        }
    }
}
