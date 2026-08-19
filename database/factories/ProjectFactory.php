<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'type' => 'iat',
            'status' => 'in_progress',
            'client_id' => Client::factory(),
            'created_by' => User::factory(),
            'tenant_id' => 'eba',
        ];
    }

    public function uat(): static
    {
        return $this->state(fn () => ['type' => 'uat']);
    }
}
