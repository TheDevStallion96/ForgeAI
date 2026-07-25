<?php

namespace App\Http\Controllers\Marketplace;

use App\Domain\Marketplace\Models\Plugin;
use App\Domain\Marketplace\Services\PluginRegistry;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketplaceController extends Controller
{
    public function __construct(
        private readonly PluginRegistry $registry,
    ) {}

    public function index(Request $request): Response
    {
        $orgId = $request->user()->organization_id;
        $category = $request->query('category', 'all');
        $search = $request->query('search');

        $plugins = $this->registry->all($category, $search);

        $installedIds = collect();
        if ($orgId !== null) {
            $installedIds = $this->registry->installedPlugins($orgId)->pluck('id');
        }

        return Inertia::render('marketplace/Index', [
            'plugins' => $plugins->map(fn (Plugin $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'description' => $p->description,
                'version' => $p->version,
                'author' => $p->author,
                'category' => $p->category,
                'icon' => $p->icon,
                'is_official' => $p->is_official,
                'is_installed' => $installedIds->contains($p->id),
                'tags' => $p->tags ?? [],
            ]),
            'categories' => [
                ['value' => 'all', 'label' => 'All'],
                ['value' => 'tool', 'label' => 'Tools'],
                ['value' => 'template', 'label' => 'Templates'],
                ['value' => 'extension', 'label' => 'Extensions'],
            ],
            'currentCategory' => $category,
        ]);
    }

    public function install(Request $request, Plugin $plugin): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        if ($orgId === null) {
            return back()->with('error', 'No organization selected.');
        }

        $this->registry->install($orgId, $plugin->id, $request->user()->id);

        return back()->with('success', "Installed {$plugin->name}.");
    }

    public function uninstall(Request $request, Plugin $plugin): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        if ($orgId === null) {
            return back()->with('error', 'No organization selected.');
        }

        $this->registry->uninstall($orgId, $plugin->id);

        return back()->with('success', "Uninstalled {$plugin->name}.");
    }
}
