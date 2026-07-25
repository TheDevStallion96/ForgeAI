<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Marketplace\Models\Plugin;
use App\Domain\Marketplace\Models\PluginInstallation;
use App\Domain\Marketplace\Services\PluginRegistry;

beforeEach(function () {
    $this->registry = app(PluginRegistry::class);
    $this->organization = Organization::factory()->create();
});

it('lists all enabled plugins', function () {
    Plugin::factory()->create(['is_enabled' => true, 'category' => 'tool']);
    Plugin::factory()->create(['is_enabled' => true, 'category' => 'template']);
    Plugin::factory()->create(['is_enabled' => false]);

    $plugins = $this->registry->all();

    expect($plugins)->toHaveCount(2);
});

it('filters plugins by category', function () {
    Plugin::factory()->create(['is_enabled' => true, 'category' => 'tool']);
    Plugin::factory()->create(['is_enabled' => true, 'category' => 'template']);

    $plugins = $this->registry->all(category: 'tool');

    expect($plugins)->toHaveCount(1);
    expect($plugins->first()->category)->toBe('tool');
});

it('searches plugins by name', function () {
    Plugin::factory()->create(['is_enabled' => true, 'name' => 'GitHub Tool']);
    Plugin::factory()->create(['is_enabled' => true, 'name' => 'Slack Bot']);

    $plugins = $this->registry->all(search: 'GitHub');

    expect($plugins)->toHaveCount(1);
    expect($plugins->first()->name)->toBe('GitHub Tool');
});

it('finds plugin by slug', function () {
    Plugin::factory()->create(['slug' => 'my-plugin', 'is_enabled' => true]);

    $plugin = $this->registry->find('my-plugin');

    expect($plugin)->not->toBeNull();
    expect($plugin->slug)->toBe('my-plugin');
});

it('returns null for disabled plugin by slug', function () {
    Plugin::factory()->create(['slug' => 'disabled-plugin', 'is_enabled' => false]);

    $plugin = $this->registry->find('disabled-plugin');

    expect($plugin)->toBeNull();
});

it('installs a plugin for organization', function () {
    $plugin = Plugin::factory()->create();

    $installation = $this->registry->install($this->organization->id, $plugin->id);

    expect($installation)->toBeInstanceOf(PluginInstallation::class);
    expect($installation->is_active)->toBeTrue();
});

it('returns existing installation on reinstall', function () {
    $plugin = Plugin::factory()->create();

    $first = $this->registry->install($this->organization->id, $plugin->id);
    $second = $this->registry->install($this->organization->id, $plugin->id);

    expect($first->id)->toBe($second->id);
});

it('uninstalls a plugin', function () {
    $plugin = Plugin::factory()->create();
    $this->registry->install($this->organization->id, $plugin->id);
    $this->registry->uninstall($this->organization->id, $plugin->id);

    $installed = $this->registry->installedPlugins($this->organization->id);
    expect($installed)->toHaveCount(0);
});

it('checks if plugin is installed', function () {
    $plugin = Plugin::factory()->create();
    $this->registry->install($this->organization->id, $plugin->id);

    expect($this->registry->isInstalled($this->organization->id, $plugin->id))->toBeTrue();
    expect($this->registry->isInstalled($this->organization->id, 999))->toBeFalse();
});
