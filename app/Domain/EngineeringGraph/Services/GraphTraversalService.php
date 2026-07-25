<?php

namespace App\Domain\EngineeringGraph\Services;

use App\Domain\EngineeringGraph\Models\GraphNode;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GraphTraversalService
{
    private const MAX_DEPTH = 4;

    public function findConnectedNodes(int $nodeId, int $organizationId, int $maxDepth = self::MAX_DEPTH): Collection
    {
        $rows = DB::select('
            WITH RECURSIVE graph_traversal AS (
                SELECT id, node_type, title, entity_id, 1 AS depth
                FROM graph_nodes
                WHERE id = ? AND organization_id = ?

                UNION ALL

                SELECT n.id, n.node_type, n.title, n.entity_id, gt.depth + 1
                FROM graph_nodes n
                JOIN graph_edges e ON e.target_node_id = n.id
                JOIN graph_traversal gt ON gt.id = e.source_node_id
                WHERE gt.depth < ?
            )
            SELECT DISTINCT node_type, title, entity_id, depth
            FROM graph_traversal
            ORDER BY depth ASC
        ', [$nodeId, $organizationId, $maxDepth]);

        return collect($rows);
    }

    public function findImpactPath(int $nodeId, int $organizationId, string $direction = 'downstream'): Collection
    {
        $joinCondition = $direction === 'downstream'
            ? 'e.source_node_id = gt.id'
            : 'e.target_node_id = gt.id';

        $nextJoin = $direction === 'downstream'
            ? 'JOIN graph_edges e ON e.source_node_id = gt.id
               JOIN graph_nodes n ON n.id = e.target_node_id'
            : 'JOIN graph_edges e ON e.target_node_id = gt.id
               JOIN graph_nodes n ON n.id = e.source_node_id';

        $rows = DB::select("
            WITH RECURSIVE graph_traversal AS (
                SELECT id, node_type, title, entity_id, 1 AS depth
                FROM graph_nodes
                WHERE id = ? AND organization_id = ?

                UNION ALL

                SELECT n.id, n.node_type, n.title, n.entity_id, gt.depth + 1
                FROM graph_traversal gt
                {$nextJoin}
                WHERE gt.depth < ?
            )
            SELECT DISTINCT node_type, title, entity_id, depth
            FROM graph_traversal
            ORDER BY depth ASC
        ", [$nodeId, $organizationId, self::MAX_DEPTH]);

        return collect($rows);
    }

    public function registerNode(string $nodeType, string $title, ?string $entityType = null, ?int $entityId = null, array $properties = []): GraphNode
    {
        return GraphNode::create([
            'organization_id' => auth()->user()->organization_id,
            'node_type' => $nodeType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'title' => $title,
            'properties' => $properties,
        ]);
    }

    public function connectNodes(int $sourceNodeId, int $targetNodeId, string $relationshipType, float $weight = 1.0, array $properties = []): void
    {
        $sourceNode = GraphNode::findOrFail($sourceNodeId);
        $targetNode = GraphNode::findOrFail($targetNodeId);

        $sourceNode->outgoingEdges()->create([
            'organization_id' => $sourceNode->organization_id,
            'target_node_id' => $targetNodeId,
            'relationship_type' => $relationshipType,
            'weight' => $weight,
            'properties' => $properties,
        ]);
    }
}
