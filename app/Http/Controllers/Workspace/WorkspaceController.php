<?php

namespace App\Http\Controllers\Workspace;

use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\Workspace\Models\Workspace;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
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

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $workspace = Workspace::query()->create([
            'organization_id' => $request->user()->organization_id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => 'active',
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Workspace created.')]);

        return to_route('workspaces.show', $workspace);
    }

    public function show(Request $request, Workspace $workspace): Response
    {
        $orgId = $request->user()->organization_id;

        $agents = Agent::query()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'primary_model']);

        $sessions = ExecutionSession::query()
            ->where('organization_id', $orgId)
            ->with('agent:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn (ExecutionSession $session) => [
                'id' => $session->id,
                'agent_name' => $session->agent->name,
                'status' => $session->status->value,
                'message_count' => $session->messages()->count(),
                'created_at' => $session->created_at->toISOString(),
            ]);

        return Inertia::render('workspaces/Show', [
            'workspace' => [
                'id' => $workspace->id,
                'name' => $workspace->name,
                'slug' => $workspace->slug,
                'description' => $workspace->description,
                'status' => $workspace->status,
            ],
            'agents' => $agents,
            'sessions' => $sessions,
        ]);
    }
}
