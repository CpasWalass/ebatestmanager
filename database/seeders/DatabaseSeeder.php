<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call RolesAndPermissionsSeeder first (creates roles, permissions, and default chef user)
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
