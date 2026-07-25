<?php

namespace Database\Factories\Domain\Automation\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Automation\Models\GraphNode;
use App\Domain\Automation\Models\Workflow;
use Illuminate\Database\Eloquent\Factories\Factory;

class GraphNodeFactory extends Factory
{
    protected $model = GraphNode::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'workflow_id' => Workflow::factory(),
            'node_type' => 'tool',
            'entity_type' => 'tool',
            'entity_id' => null,
            'title' => fake()->words(2, true),
            'properties' => ['tool' => 'read_file', 'parameters' => []],
        ];
    }
}
