<?php

namespace App\Domain\Marketplace\Services;

use App\Domain\Marketplace\Models\Plugin;
use App\Domain\Marketplace\Models\PluginInstallation;
use Illuminate\Database\Eloquent\Collection;

class PluginRegistry
{
    public function all(?string $category = null, ?string $search = null): Collection
    {
        $query = Plugin::query()->enabled();

        if ($category !== null && $category !== 'all') {
            $query->byCategory($category);
        }

        if ($search !== null) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('is_official', 'desc')
            ->orderBy('name')
            ->get();
    }

    public function official(): Collection
    {
        return Plugin::query()->enabled()->official()->orderBy('name')->get();
    }

    public function find(string $slug): ?Plugin
    {
        return Plugin::query()->enabled()->where('slug', $slug)->first();
    }

    public function install(int $organizationId, int $pluginId, ?int $userId = null): PluginInstallation
    {
        $existing = PluginInstallation::query()
            ->where('organization_id', $organizationId)
            ->where('plugin_id', $pluginId)
            ->first();

        if ($existing !== null) {
            $existing->update([
                'is_active' => true,
                'installed_by' => $userId,
            ]);

            return $existing;
        }

        return PluginInstallation::query()->create([
            'organization_id' => $organizationId,
            'plugin_id' => $pluginId,
            'is_active' => true,
            'installed_by' => $userId,
        ]);
    }

    public function uninstall(int $organizationId, int $pluginId): void
    {
        PluginInstallation::query()
            ->where('organization_id', $organizationId)
            ->where('plugin_id', $pluginId)
            ->update(['is_active' => false]);
    }

    public function installedPlugins(int $organizationId): Collection
    {
        return Plugin::query()
            ->whereIn('id', PluginInstallation::query()
                ->where('organization_id', $organizationId)
                ->where('is_active', true)
                ->select('plugin_id')
            )
            ->get();
    }

    public function isInstalled(int $organizationId, int $pluginId): bool
    {
        return PluginInstallation::query()
            ->where('organization_id', $organizationId)
            ->where('plugin_id', $pluginId)
            ->where('is_active', true)
            ->exists();
    }
}
