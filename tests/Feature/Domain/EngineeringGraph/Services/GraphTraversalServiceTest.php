<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\AuthTenant\Models\User;
use App\Domain\EngineeringGraph\Models\GraphEdge;
use App\Domain\EngineeringGraph\Models\GraphNode;
use App\Domain\EngineeringGraph\Services\GraphTraversalService;

beforeEach(function () {
    $this->service = app(GraphTraversalService::class);
    $this->organization = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $this->organization->id]);
    $this->actingAs($user);
});

it('can register a graph node', function () {
    $node = $this->service->registerNode('FEATURE', 'User Authentication', 'feature', null, ['priority' => 'high']);

    expect($node)->toBeInstanceOf(GraphNode::class)
        ->and($node->node_type)->toBe('FEATURE')
        ->and($node->title)->toBe('User Authentication');
});

it('can connect two nodes', function () {
    $source = $this->service->registerNode('FEATURE', 'Login Page');
    $target = $this->service->registerNode('ADR', 'Use Fortify for Auth');

    $this->service->connectNodes($source->id, $target->id, 'IMPLEMENTS');

    $edge = GraphEdge::first();
    expect($edge->source_node_id)->toBe($source->id)
        ->and($edge->target_node_id)->toBe($target->id)
        ->and($edge->relationship_type)->toBe('IMPLEMENTS');
});

it('can traverse connected nodes recursively', function () {
    $feature = GraphNode::factory()->create([
        'organization_id' => $this->organization->id,
        'node_type' => 'FEATURE',
        'title' => 'Auth Feature',
    ]);

    $adr = GraphNode::factory()->create([
        'organization_id' => $this->organization->id,
        'node_type' => 'ADR',
        'title' => 'Auth ADR',
    ]);

    $service = GraphNode::factory()->create([
        'organization_id' => $this->organization->id,
        'node_type' => 'SERVICE',
        'title' => 'Auth Service',
    ]);

    GraphEdge::factory()->create([
        'organization_id' => $this->organization->id,
        'source_node_id' => $feature->id,
        'target_node_id' => $adr->id,
        'relationship_type' => 'IMPLEMENTS',
    ]);

    GraphEdge::factory()->create([
        'organization_id' => $this->organization->id,
        'source_node_id' => $adr->id,
        'target_node_id' => $service->id,
        'relationship_type' => 'BOUNDS',
    ]);

    $results = $this->service->findConnectedNodes($feature->id, $this->organization->id);

    expect($results)->toHaveCount(3);
});

it('can find downstream impact path', function () {
    $feature = GraphNode::factory()->create([
        'organization_id' => $this->organization->id,
        'node_type' => 'FEATURE',
        'title' => 'Feature A',
    ]);

    $commit = GraphNode::factory()->create([
        'organization_id' => $this->organization->id,
        'node_type' => 'COMMIT',
        'title' => 'feat: implement feature A',
    ]);

    GraphEdge::factory()->create([
        'organization_id' => $this->organization->id,
        'source_node_id' => $feature->id,
        'target_node_id' => $commit->id,
        'relationship_type' => 'IMPLEMENTS',
    ]);

    $results = $this->service->findImpactPath($feature->id, $this->organization->id, 'downstream');

    expect($results)->toHaveCount(2);
});

it('limits traversal depth', function () {
    $nodes = [];
    for ($i = 0; $i < 6; $i++) {
        $nodes[] = GraphNode::factory()->create([
            'organization_id' => $this->organization->id,
            'node_type' => 'NODE',
            'title' => "Node {$i}",
        ]);
    }

    for ($i = 0; $i < 5; $i++) {
        GraphEdge::factory()->create([
            'organization_id' => $this->organization->id,
            'source_node_id' => $nodes[$i]->id,
            'target_node_id' => $nodes[$i + 1]->id,
            'relationship_type' => 'NEXT',
        ]);
    }

    $results = $this->service->findConnectedNodes($nodes[0]->id, $this->organization->id, 3);

    expect(count($results))->toBeLessThanOrEqual(4);
});
