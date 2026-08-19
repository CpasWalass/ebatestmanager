<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\TestCaseTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestCaseTemplateFactory extends Factory
{
    protected $model = TestCaseTemplate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'project_id' => Project::factory(),
            'fields' => TestCaseTemplate::defaultFields(),
            'tenant_id' => 'eba',
        ];
    }
}
