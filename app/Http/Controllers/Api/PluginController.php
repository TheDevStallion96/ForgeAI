<?php

namespace App\Http\Controllers\Api;

use App\Domain\Marketplace\Models\Plugin;
use App\Domain\Marketplace\Services\PluginRegistry;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PluginController extends Controller
{
    public function __construct(
        private readonly PluginRegistry $registry,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $plugins = $this->registry->all(
            $request->query('category'),
            $request->query('search'),
        );

        return response()->json(['data' => $plugins]);
    }

    public function install(Request $request, Plugin $plugin): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        if ($orgId === null) {
            return response()->json(['error' => 'No organization selected.'], 400);
        }

        $installation = $this->registry->install($orgId, $plugin->id, $request->user()->id);

        return response()->json(['data' => $installation], 201);
    }

    public function uninstall(Request $request, Plugin $plugin): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        if ($orgId === null) {
            return response()->json(['error' => 'No organization selected.'], 400);
        }

        $this->registry->uninstall($orgId, $plugin->id);

        return response()->json(['message' => 'Uninstalled.']);
    }

    public function installed(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        if ($orgId === null) {
            return response()->json(['data' => []]);
        }

        $plugins = $this->registry->installedPlugins($orgId);

        return response()->json(['data' => $plugins]);
    }
}
