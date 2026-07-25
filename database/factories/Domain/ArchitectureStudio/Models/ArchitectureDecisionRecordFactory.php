<?php

namespace Database\Factories\Domain\ArchitectureStudio\Models;

use App\Domain\ArchitectureStudio\Models\ArchitectureDecisionRecord;
use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArchitectureDecisionRecordFactory extends Factory
{
    protected $model = ArchitectureDecisionRecord::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'title' => 'ADR-'.fake()->unique()->numberBetween(1, 999).': '.fake()->sentence(4),
            'adr_number' => fake()->unique()->numberBetween(1, 999),
            'status' => fake()->randomElement(['draft', 'proposed', 'accepted', 'deprecated']),
            'context' => fake()->paragraph(),
            'decision' => fake()->paragraph(),
            'consequences' => fake()->paragraph(),
        ];
    }
}
