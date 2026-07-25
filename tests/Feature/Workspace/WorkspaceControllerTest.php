<?php

use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\AuthTenant\Models\User;
use App\Domain\Workspace\Models\Workspace;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('redirects guests to login', function () {
    auth()->logout();

    $response = $this->get(route('workspaces.index'));

    $response->assertRedirect(route('login'));
});

it('lists workspaces', function () {
    Workspace::factory()->count(2)->create([
        'organization_id' => $this->user->organization_id,
    ]);

    $response = $this->get(route('workspaces.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('workspaces/Index')
        ->has('workspaces', 2),
    );
});

it('shows a workspace with agents and sessions', function () {
    $workspace = Workspace::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);

    Agent::factory()->count(2)->create([
        'organization_id' => $this->user->organization_id,
        'is_active' => true,
    ]);

    $response = $this->get(route('workspaces.show', $workspace));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('workspaces/Show')
        ->has('workspace')
        ->has('agents', 2)
        ->has('sessions'),
    );
});

it('shows recent sessions in workspace', function () {
    $workspace = Workspace::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);

    $agent = Agent::factory()->create([
        'organization_id' => $this->user->organization_id,
        'is_active' => true,
    ]);

    ExecutionSession::factory()->count(3)->create([
        'agent_id' => $agent->id,
        'organization_id' => $this->user->organization_id,
    ]);

    $response = $this->get(route('workspaces.show', $workspace));

    $response->assertInertia(fn (Assert $page) => $page
        ->has('sessions', 3),
    );
});

it('prevents viewing workspaces from other organizations', function () {
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->create([
        'organization_id' => $otherUser->organization_id,
    ]);

    $response = $this->get(route('workspaces.show', $workspace));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('sessions', 0),
    );
});
