<?php

use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\AuthTenant\Models\User;
use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;
use App\Domain\Automation\Models\GraphEdge;
use App\Domain\Automation\Models\GraphNode;
use App\Domain\Automation\Models\Workflow;
use App\Domain\Automation\Services\ToolExecutor;
use App\Domain\Automation\Services\ToolPermissionChecker;
use App\Domain\Automation\Services\ToolRegistry;
use App\Domain\Automation\Services\WorkflowEngine;

beforeEach(function () {
    $registry = new ToolRegistry;
    $permissionChecker = new ToolPermissionChecker;

    $tool = new class implements ToolInterface
    {
        public function name(): string
        {
            return 'test_tool';
        }

        public function description(): string
        {
            return 'Test';
        }

        public function parameterSchema(): array
        {
            return [];
        }

        public function requiresHumanApproval(): bool
        {
            return false;
        }

        public function execute(array $parameters): ToolResult
        {
            return ToolResult::success($parameters['value'] ?? 'default');
        }
    };

    $registry->register($tool);
    $permissionChecker->allowTool('test_tool');

    $executor = new ToolExecutor($registry, $permissionChecker);
    $this->engine = new WorkflowEngine($executor);

    $this->user = User::factory()->create();
    $this->agent = Agent::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);
    $this->session = ExecutionSession::factory()->create([
        'agent_id' => $this->agent->id,
        'user_id' => $this->user->id,
    ]);
});

it('runs topological sort on sequential nodes', function () {
    $workflow = Workflow::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);

    $nodeA = GraphNode::factory()->create([
        'workflow_id' => $workflow->id,
        'organization_id' => $this->user->organization_id,
        'title' => 'Step A',
        'properties' => ['tool' => 'test_tool', 'parameters' => ['value' => 'a']],
    ]);

    $nodeB = GraphNode::factory()->create([
        'workflow_id' => $workflow->id,
        'organization_id' => $this->user->organization_id,
        'title' => 'Step B',
        'properties' => ['tool' => 'test_tool', 'parameters' => ['value' => 'b']],
    ]);

    GraphEdge::factory()->create([
        'workflow_id' => $workflow->id,
        'organization_id' => $this->user->organization_id,
        'source_node_id' => $nodeA->id,
        'target_node_id' => $nodeB->id,
        'relationship_type' => 'next',
    ]);

    $result = $this->engine->execute($workflow, [
        'session' => $this->session,
        'user' => $this->user,
    ]);

    expect($result->isSuccess())->toBeTrue();
});

it('fails with missing context', function () {
    $workflow = Workflow::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);

    GraphNode::factory()->create([
        'workflow_id' => $workflow->id,
        'organization_id' => $this->user->organization_id,
        'title' => 'Lonely Node',
        'properties' => ['tool' => 'test_tool', 'parameters' => []],
    ]);

    $result = $this->engine->execute($workflow, []);

    expect($result->isSuccess())->toBeFalse();
    expect($result->error)->toContain('Session and user context required');
});

it('returns empty when no nodes exist', function () {
    $workflow = Workflow::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);

    $result = $this->engine->execute($workflow, [
        'session' => $this->session,
        'user' => $this->user,
    ]);

    expect($result->isSuccess())->toBeFalse();
    expect($result->error)->toContain('no executable nodes');
});
