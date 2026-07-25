<?php

namespace Database\Factories\Domain\Marketplace\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Marketplace\Models\Plugin;
use App\Domain\Marketplace\Models\PluginInstallation;
use Illuminate\Database\Eloquent\Factories\Factory;

class PluginInstallationFactory extends Factory
{
    protected $model = PluginInstallation::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'plugin_id' => Plugin::factory(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
