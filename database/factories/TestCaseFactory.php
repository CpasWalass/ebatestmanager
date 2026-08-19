<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestCaseTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestCaseFactory extends Factory
{
    protected $model = TestCase::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'template_id' => TestCaseTemplate::factory(),
            'type' => 'iat',
            'progress' => 'a_faire',
            'verdict' => null,
            'client_status' => 'pending',
            'source' => 'manual',
            'data' => ['cas_test' => 'Test case '.fake()->uuid()],
            'tenant_id' => 'eba',
        ];
    }

    public function executed(): static
    {
        return $this->state(fn () => ['progress' => 'termine']);
    }

    public function blocked(): static
    {
        return $this->state(fn () => ['progress' => 'bloque']);
    }

    public function withVerdict(string $verdict): static
    {
        return $this->state(fn () => [
            'progress' => 'termine',
            'verdict' => $verdict,
        ]);
    }

    public function uat(): static
    {
        return $this->state(fn () => ['type' => 'uat']);
    }
}
