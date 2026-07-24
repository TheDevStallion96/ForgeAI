<?php

namespace Database\Factories\Domain\Automation\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Automation\Models\GraphEdge;
use App\Domain\Automation\Models\GraphNode;
use App\Domain\Automation\Models\Workflow;
use Illuminate\Database\Eloquent\Factories\Factory;

class GraphEdgeFactory extends Factory
{
    protected $model = GraphEdge::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'workflow_id' => Workflow::factory(),
            'source_node_id' => GraphNode::factory(),
            'target_node_id' => GraphNode::factory(),
            'relationship_type' => 'next',
            'weight' => 1.0,
            'properties' => [],
        ];
    }
}
