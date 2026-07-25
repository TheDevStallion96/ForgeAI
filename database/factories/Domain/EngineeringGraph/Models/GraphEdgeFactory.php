<?php

namespace Database\Factories\Domain\EngineeringGraph\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\EngineeringGraph\Models\GraphEdge;
use App\Domain\EngineeringGraph\Models\GraphNode;
use Illuminate\Database\Eloquent\Factories\Factory;

class GraphEdgeFactory extends Factory
{
    protected $model = GraphEdge::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'source_node_id' => GraphNode::factory(),
            'target_node_id' => GraphNode::factory(),
            'relationship_type' => fake()->randomElement(['IMPLEMENTS', 'TESTS', 'CAUSED_BY', 'BOUNDS', 'DEPLOYS']),
            'weight' => fake()->randomFloat(2, 0.1, 1.0),
            'properties' => [],
        ];
    }
}
