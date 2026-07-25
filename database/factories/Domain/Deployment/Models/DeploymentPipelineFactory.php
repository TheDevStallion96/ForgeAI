<?php

namespace Database\Factories\Domain\Deployment\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Deployment\Models\DeploymentPipeline;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeploymentPipelineFactory extends Factory
{
    protected $model = DeploymentPipeline::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->word().'-'.fake()->word(),
            'environment' => fake()->randomElement(['development', 'staging', 'production']),
            'status' => fake()->randomElement(['pending', 'running', 'success', 'failed']),
            'version' => 'v'.fake()->numberBetween(1, 5).'.'.fake()->numberBetween(0, 9).'.'.fake()->numberBetween(0, 9),
            'started_at' => fake()->dateTimeThisMonth(),
            'completed_at' => null,
        ];
    }
}
