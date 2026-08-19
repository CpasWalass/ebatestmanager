<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'created_by' => User::factory(),
            'title' => fake()->sentence(4),
            'type' => 'executive',
            'tested_version' => '1.0.0',
            'test_date' => fake()->dateTimeThisMonth(),
            'status' => 'draft',
            'tenant_id' => 'eba',
        ];
    }
}
