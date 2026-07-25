<?php

namespace App\Domain\Automation\Services;

use App\Domain\Automation\Contracts\ToolResult;
use App\Domain\Automation\Models\GraphNode;
use App\Domain\Automation\Models\Workflow;
use Illuminate\Support\Collection;

class WorkflowEngine
{
    public function __construct(
        protected ToolExecutor $executor,
    ) {}

    public function execute(Workflow $workflow, array $initialContext = []): ToolResult
    {
        $nodes = $workflow->nodes()->get()->keyBy('id');
        $edges = $workflow->edges()->get();

        $executionOrder = $this->topologicalSort($nodes, $edges);

        if (empty($executionOrder)) {
            return ToolResult::failure('Workflow has no executable nodes or contains cycles');
        }

        $context = $initialContext;
        $results = [];

        foreach ($executionOrder as $nodeId) {
            $node = $nodes->get($nodeId);
            if (! $node) {
                continue;
            }

            $result = $this->executeNode($node, $context);

            if (! $result->isSuccess()) {
                $results[$nodeId] = $result;

                return ToolResult::failure("Node {$node->title} failed: {$result->error}");
            }

            $results[$nodeId] = $result;
            $context = array_merge($context, [$nodeId => $result->output]);
        }

        return ToolResult::success($results);
    }

    protected function executeNode(GraphNode $node, array $context): ToolResult
    {
        $toolName = $node->properties['tool'] ?? null;
        $parameters = $node->properties['parameters'] ?? [];
        $session = $context['session'] ?? null;
        $user = $context['user'] ?? null;

        if (! $toolName) {
            return ToolResult::failure("Node {$node->id} has no tool defined");
        }

        if (! $session || ! $user) {
            return ToolResult::failure('Session and user context required');
        }

        $resolvedParams = $this->resolveParameters($parameters, $context);

        return $this->executor->execute($toolName, $resolvedParams, $session, $user);
    }

    protected function resolveParameters(array $parameters, array $context): array
    {
        $resolved = [];

        foreach ($parameters as $key => $value) {
            if (is_string($value) && str_starts_with($value, '${') && str_ends_with($value, '}')) {
                $ref = substr($value, 2, -1);
                $resolved[$key] = $context[$ref] ?? $value;
            } else {
                $resolved[$key] = $value;
            }
        }

        return $resolved;
    }

    protected function topologicalSort(Collection $nodes, Collection $edges): array
    {
        $graph = [];
        $inDegree = [];

        foreach ($nodes as $node) {
            $graph[$node->id] = [];
            $inDegree[$node->id] = 0;
        }

        foreach ($edges as $edge) {
            if (isset($graph[$edge->source_node_id])) {
                $graph[$edge->source_node_id][] = $edge->target_node_id;
                $inDegree[$edge->target_node_id] = ($inDegree[$edge->target_node_id] ?? 0) + 1;
            }
        }

        $queue = array_keys(array_filter($inDegree, fn ($deg) => $deg === 0));
        $result = [];

        while (! empty($queue)) {
            $nodeId = array_shift($queue);
            $result[] = $nodeId;

            foreach ($graph[$nodeId] as $neighbor) {
                $inDegree[$neighbor]--;
                if ($inDegree[$neighbor] === 0) {
                    $queue[] = $neighbor;
                }
            }
        }

        return count($result) === count($nodes) ? $result : [];
    }
}
