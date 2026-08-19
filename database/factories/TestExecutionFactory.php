<?php

namespace Database\Factories;

use App\Models\TestCase;
use App\Models\TestExecution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestExecutionFactory extends Factory
{
    protected $model = TestExecution::class;

    public function definition(): array
    {
        return [
            'test_case_id' => TestCase::factory(),
            'tester_id' => User::factory(),
            'status' => 'valide',
            'nature' => 'Erreurs Fonctionnelles',
            'tenant_id' => 'eba',
        ];
    }
}
