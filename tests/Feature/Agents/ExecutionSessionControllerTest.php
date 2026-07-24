<?php

use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\Agent\Models\SessionMessage;
use App\Domain\AuthTenant\Models\Organization;
use App\Domain\AuthTenant\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('redirects guests to login', function () {
    auth()->logout();

    $response = $this->get(route('agents.sessions.index'));

    $response->assertRedirect(route('login'));
});

it('lists all sessions', function () {
    $agent = Agent::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);
    ExecutionSession::factory()->count(3)->create([
        'agent_id' => $agent->id,
        'organization_id' => $this->user->organization_id,
    ]);

    $response = $this->get(route('agents.sessions.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('agents/Sessions/Index')
        ->has('sessions.data', 3),
    );
});

it('lists sessions filtered by agent', function () {
    $agent = Agent::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);
    ExecutionSession::factory()->create([
        'agent_id' => $agent->id,
        'organization_id' => $this->user->organization_id,
    ]);

    $response = $this->get(route('agents.sessions.by-agent', $agent));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('agents/Sessions/Index')
        ->has('agent'),
    );
});

it('shows a session with messages', function () {
    $session = ExecutionSession::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);
    SessionMessage::factory()->count(2)->create([
        'session_id' => $session->id,
    ]);

    $response = $this->get(route('agents.sessions.show', $session));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('agents/Sessions/Show')
        ->has('messages', 2),
    );
});

it('prevents viewing sessions from other organizations', function () {
    $otherOrg = Organization::factory()->create();
    $session = ExecutionSession::factory()->create([
        'organization_id' => $otherOrg->id,
    ]);

    $response = $this->get(route('agents.sessions.show', $session));

    $response->assertForbidden();
});

it('ends a session', function () {
    $session = ExecutionSession::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);

    $response = $this->delete(route('agents.sessions.destroy', $session));

    $response->assertRedirect();
    $this->assertEquals('failed', $session->fresh()->status->value);
});
