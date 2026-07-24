<?php

namespace App\Http\Controllers\Agents;

use App\Domain\Agent\Models\Agent;
use App\Domain\AIEngine\Enums\AIProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    public function index(Request $request): Response
    {
        $agents = Agent::query()
            ->where('organization_id', $request->user()->organization_id)
            ->orderBy('name')
            ->get()
            ->map(fn (Agent $agent) => [
                'id' => $agent->id,
                'name' => $agent->name,
                'primary_model' => $agent->primary_model,
                'is_active' => $agent->is_active,
                'session_count' => $agent->executionSessions()->count(),
                'created_at' => $agent->created_at->toISOString(),
            ]);

        return Inertia::render('agents/Index', [
            'agents' => $agents,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('agents/Create', [
            'providers' => AIProvider::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'primary_model' => ['required', 'string', 'max:255'],
            'fallback_model' => ['nullable', 'string', 'max:255'],
            'system_instruction' => ['nullable', 'string'],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:2'],
            'is_active' => ['boolean'],
        ]);

        $data['organization_id'] = $request->user()->organization_id;

        $agent = Agent::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Agent created.')]);

        return to_route('agents.edit', $agent->id);
    }

    public function edit(Request $request, Agent $agent): Response
    {
        abort_unless($agent->organization_id === $request->user()->organization_id, 403);

        return Inertia::render('agents/Edit', [
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'primary_model' => $agent->primary_model,
                'fallback_model' => $agent->fallback_model,
                'system_instruction' => $agent->system_instruction,
                'temperature' => (float) $agent->temperature,
                'is_active' => $agent->is_active,
            ],
            'providers' => AIProvider::cases(),
        ]);
    }

    public function update(Request $request, Agent $agent): RedirectResponse
    {
        abort_unless($agent->organization_id === $request->user()->organization_id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'primary_model' => ['required', 'string', 'max:255'],
            'fallback_model' => ['nullable', 'string', 'max:255'],
            'system_instruction' => ['nullable', 'string'],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:2'],
            'is_active' => ['boolean'],
        ]);

        $agent->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Agent updated.')]);

        return to_route('agents.edit', $agent->id);
    }

    public function destroy(Request $request, Agent $agent): RedirectResponse
    {
        abort_unless($agent->organization_id === $request->user()->organization_id, 403);

        $agent->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Agent deleted.')]);

        return to_route('agents.index');
    }
}
