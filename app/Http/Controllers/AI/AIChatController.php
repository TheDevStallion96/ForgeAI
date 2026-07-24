<?php

namespace App\Http\Controllers\AI;

use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Services\AgentOrchestrator;
use App\Domain\AIEngine\Enums\AIProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AIChatController extends Controller
{
    public function __construct(
        private readonly AgentOrchestrator $orchestrator,
    ) {}

    public function index(): Response
    {
        $agents = Agent::query()
            ->where('organization_id', request()->user()->organization_id)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return Inertia::render('AI/Chat', [
            'agents' => $agents,
        ]);
    }

    public function chat(Request $request)
    {
        $data = $request->validate([
            'prompt' => ['required', 'string', 'max:2000'],
            'agent_id' => ['nullable', 'integer', 'exists:agents,id'],
        ]);

        $agent = $data['agent_id']
            ? Agent::findOrFail($data['agent_id'])
            : $this->ensureDefaultAgent($request->user()->organization_id);

        return $this->orchestrator->execute(
            $agent,
            $request->user(),
            $data['prompt'],
        );
    }

    private function ensureDefaultAgent(int $organizationId): Agent
    {
        return Agent::query()->firstOrCreate(
            [
                'organization_id' => $organizationId,
                'is_active' => true,
            ],
            [
                'name' => 'Default Assistant',
                'primary_model' => AIProvider::OpenAI->value.':gpt-4o-mini',
                'fallback_model' => AIProvider::Anthropic->value.':claude-haiku-4-5-20251001',
                'system_instruction' => 'You are a helpful assistant.',
                'temperature' => 0.70,
            ],
        );
    }
}
