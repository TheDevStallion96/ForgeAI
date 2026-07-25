<?php

namespace App\Http\Controllers\Deployments;

use App\Domain\Deployment\Models\DeploymentPipeline;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeploymentController extends Controller
{
    public function index(Request $request): Response
    {
        $orgId = $request->user()->organization_id;

        $pipelines = DeploymentPipeline::query()
            ->where('organization_id', $orgId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (DeploymentPipeline $pipeline) => [
                'id' => $pipeline->id,
                'name' => $pipeline->name,
                'environment' => $pipeline->environment,
                'status' => $pipeline->status,
                'version' => $pipeline->version,
                'started_at' => $pipeline->started_at?->diffForHumans() ?? '--',
                'completed_at' => $pipeline->completed_at?->toISOString(),
            ]);

        $stats = [
            'total' => $pipelines->count(),
            'production_healthy' => $pipelines->where('environment', 'production')->whereIn('status', ['success', 'running'])->count(),
            'staging_active' => $pipelines->where('environment', 'staging')->where('status', '!=', 'failed')->count(),
            'failed' => $pipelines->where('status', 'failed')->count(),
        ];

        return Inertia::render('deployments/Index', [
            'pipelines' => $pipelines,
            'stats' => $stats,
        ]);
    }
}
