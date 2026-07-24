<?php

use App\Domain\Agent\Models\Agent;
use App\Domain\AuthTenant\Models\Organization;
use App\Domain\AuthTenant\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('redirects guests to login', function () {
    auth()->logout();

    $response = $this->get(route('agents.index'));

    $response->assertRedirect(route('login'));
});

it('lists agents', function () {
    Agent::factory()->count(3)->create([
        'organization_id' => $this->user->organization_id,
    ]);

    $response = $this->get(route('agents.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('agents/Index')
        ->has('agents', 3),
    );
});

it('shows the create page', function () {
    $response = $this->get(route('agents.create'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('agents/Create')
        ->has('providers'),
    );
});

it('creates an agent', function () {
    $response = $this->post(route('agents.store'), [
        'name' => 'Test Agent',
        'primary_model' => 'openai:gpt-4o-mini',
        'system_instruction' => 'You are a test assistant.',
        'temperature' => 0.5,
        'is_active' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('agents', [
        'name' => 'Test Agent',
        'organization_id' => $this->user->organization_id,
    ]);
});

it('validates required fields when creating', function () {
    $response = $this->post(route('agents.store'), []);

    $response->assertInvalid(['name', 'primary_model']);
});

it('shows the edit page', function () {
    $agent = Agent::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);

    $response = $this->get(route('agents.edit', $agent));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('agents/Edit')
        ->has('agent')
        ->has('providers'),
    );
});

it('updates an agent', function () {
    $agent = Agent::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);

    $response = $this->patch(route('agents.update', $agent), [
        'name' => 'Updated Agent',
        'primary_model' => 'anthropic:claude-sonnet-4-20251022',
        'temperature' => 0.8,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('agents', [
        'id' => $agent->id,
        'name' => 'Updated Agent',
        'temperature' => 0.80,
    ]);
});

it('prevents editing agents from other organizations', function () {
    $otherOrg = Organization::factory()->create();
    $agent = Agent::factory()->create([
        'organization_id' => $otherOrg->id,
    ]);

    $response = $this->get(route('agents.edit', $agent));

    $response->assertForbidden();
});

it('prevents updating agents from other organizations', function () {
    $otherOrg = Organization::factory()->create();
    $agent = Agent::factory()->create([
        'organization_id' => $otherOrg->id,
    ]);

    $response = $this->patch(route('agents.update', $agent), [
        'name' => 'Hacked Name',
        'primary_model' => 'openai:gpt-4o',
    ]);

    $response->assertForbidden();
});

it('deletes an agent', function () {
    $agent = Agent::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);

    $response = $this->delete(route('agents.destroy', $agent));

    $response->assertRedirect();
    $this->assertDatabaseMissing('agents', ['id' => $agent->id]);
});

it('prevents deleting agents from other organizations', function () {
    $otherOrg = Organization::factory()->create();
    $agent = Agent::factory()->create([
        'organization_id' => $otherOrg->id,
    ]);

    $response = $this->delete(route('agents.destroy', $agent));

    $response->assertForbidden();
});
