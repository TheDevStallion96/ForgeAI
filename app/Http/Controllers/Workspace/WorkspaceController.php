<?php

namespace App\Http\Controllers\Workspace;

use App\Domain\Workspace\Models\Workspace;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkspaceController extends Controller
{
    public function index(Request $request): Response
    {
        $workspaces = Workspace::query()
            ->where('organization_id', $request->user()->organization_id)
            ->latest()
            ->get()
            ->map(fn (Workspace $workspace) => [
                'id' => $workspace->id,
                'name' => $workspace->name,
                'slug' => $workspace->slug,
                'description' => $workspace->description,
                'status' => $workspace->status,
                'created_at' => $workspace->created_at->diffForHumans(),
            ]);

        return Inertia::render('workspaces/Index', [
            'workspaces' => $workspaces,
        ]);
    }

    public function show(Request $request, Workspace $workspace): Response
    {
        return Inertia::render('workspaces/Show', [
            'workspace' => [
                'id' => $workspace->id,
                'name' => $workspace->name,
                'slug' => $workspace->slug,
                'description' => $workspace->description,
                'status' => $workspace->status,
            ],
        ]);
    }
}
