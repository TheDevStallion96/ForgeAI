<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\AuthTenant\Models\User;
use App\Domain\Marketplace\Models\Plugin;
use App\Domain\Marketplace\Models\PluginInstallation;
use App\Domain\McpGateway\Models\McpConnection;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization = Organization::find($this->user->organization_id);
});

it('requires authentication for api routes', function () {
    $response = $this->getJson('/api/v1/tools');

    $response->assertUnauthorized();
});

it('lists tools via api', function () {
    $response = $this->actingAs($this->user)->getJson('/api/v1/tools');

    $response->assertOk();
    expect($response->json('data'))->toBeArray();
});

it('lists marketplace plugins via api', function () {
    Plugin::factory()->count(3)->create(['is_enabled' => true]);

    $response = $this->actingAs($this->user)->getJson('/api/v1/plugins');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(3);
});

it('installs plugin via api', function () {
    $plugin = Plugin::factory()->create();

    $response = $this->actingAs($this->user)->postJson("/api/v1/plugins/{$plugin->id}/install");

    $response->assertCreated();
    expect(PluginInstallation::where('plugin_id', $plugin->id)->exists())->toBeTrue();
});

it('uninstalls plugin via api', function () {
    $plugin = Plugin::factory()->create();
    PluginInstallation::factory()->create([
        'organization_id' => $this->organization->id,
        'plugin_id' => $plugin->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->postJson("/api/v1/plugins/{$plugin->id}/uninstall");

    $response->assertOk();
    expect(PluginInstallation::where('plugin_id', $plugin->id)->first()->is_active)->toBeFalse();
});

it('lists installed plugins via api', function () {
    $plugin = Plugin::factory()->create();
    PluginInstallation::factory()->create([
        'organization_id' => $this->organization->id,
        'plugin_id' => $plugin->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->getJson('/api/v1/plugins/installed');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('manages mcp connections via api', function () {
    $response = $this->actingAs($this->user)->postJson('/api/v1/mcp/connections', [
        'name' => 'Test MCP',
        'transport_type' => 'stdio',
        'command' => 'node server.js',
    ]);

    $response->assertCreated();
    expect(McpConnection::where('name', 'Test MCP')->exists())->toBeTrue();

    $connectionId = $response->json('data.id');

    $listResponse = $this->actingAs($this->user)->getJson('/api/v1/mcp/connections');
    $listResponse->assertOk();
    expect($listResponse->json('data'))->toHaveCount(1);

    $deleteResponse = $this->actingAs($this->user)->deleteJson("/api/v1/mcp/connections/{$connectionId}");
    $deleteResponse->assertOk();
});

it('lists mcp connections filtered by organization', function () {
    $otherOrg = Organization::factory()->create();

    McpConnection::create([
        'organization_id' => $this->organization->id,
        'name' => 'Our MCP',
        'transport_type' => 'stdio',
    ]);
    McpConnection::create([
        'organization_id' => $otherOrg->id,
        'name' => 'Other MCP',
        'transport_type' => 'stdio',
    ]);

    $response = $this->actingAs($this->user)->getJson('/api/v1/mcp/connections');
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data')[0]['name'])->toBe('Our MCP');
});
