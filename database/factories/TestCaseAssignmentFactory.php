<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\TestCaseAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestCaseAssignmentFactory extends Factory
{
    protected $model = TestCaseAssignment::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'scope' => 'full_case',
            'status' => 'in_progress',
            'tenant_id' => 'eba',
        ];
    }
}
