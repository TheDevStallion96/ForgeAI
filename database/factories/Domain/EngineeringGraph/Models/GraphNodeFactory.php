<?php

namespace Database\Factories\Domain\EngineeringGraph\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\EngineeringGraph\Models\GraphNode;
use Illuminate\Database\Eloquent\Factories\Factory;

class GraphNodeFactory extends Factory
{
    protected $model = GraphNode::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'node_type' => fake()->randomElement(['FEATURE', 'ADR', 'COMMIT', 'TEST', 'INCIDENT']),
            'entity_type' => null,
            'entity_id' => null,
            'title' => fake()->sentence(3),
            'properties' => [],
        ];
    }
}
