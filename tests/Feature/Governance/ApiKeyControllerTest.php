<?php

use App\Domain\AuthTenant\Models\User;
use App\Domain\Governance\Services\SecretsManager;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('redirects guests to login', function () {
    auth()->logout();

    $response = $this->get(route('governance.api-keys.index'));

    $response->assertRedirect(route('login'));
});

it('lists API keys', function () {
    $manager = app(SecretsManager::class);
    $manager->store($this->user->organization_id, 'openai', 'sk-test-001');
    $manager->store($this->user->organization_id, 'anthropic', 'sk-ant-test-001', 'Production');

    $response = $this->get(route('governance.api-keys.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('governance/ApiKeys/Index')
        ->has('keys', 2),
    );
});

it('stores a new API key', function () {
    $response = $this->post(route('governance.api-keys.store'), [
        'provider' => 'openai',
        'key' => 'sk-test-key-12345',
        'name' => 'Development',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('api_keys', [
        'organization_id' => $this->user->organization_id,
        'provider' => 'openai',
        'name' => 'Development',
    ]);
});

it('validates required fields when storing', function () {
    $response = $this->post(route('governance.api-keys.store'), []);

    $response->assertInvalid(['provider', 'key']);
});

it('revokes an API key', function () {
    $manager = app(SecretsManager::class);
    $key = $manager->store($this->user->organization_id, 'openai', 'sk-test-001');

    $response = $this->delete(route('governance.api-keys.destroy', $key->id));

    $response->assertRedirect();
    expect($key->fresh()->is_active)->toBeFalse();
});
