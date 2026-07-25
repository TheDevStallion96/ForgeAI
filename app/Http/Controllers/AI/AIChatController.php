<?php

namespace App\Http\Controllers\AI;

use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Models\ExecutionSession;
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
        $orgId = request()->user()->organization_id;

        $agents = Agent::query()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'primary_model', 'fallback_model', 'system_instruction', 'temperature']);

        $sessions = ExecutionSession::query()
            ->where('organization_id', $orgId)
            ->with('agent:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(fn (ExecutionSession $session) => [
                'id' => $session->id,
                'agent_name' => $session->agent->name,
                'status' => $session->status->value,
                'message_count' => $session->messages()->count(),
                'context_tokens' => $session->context_tokens_accumulated,
                'created_at' => $session->created_at->toISOString(),
            ]);

        return Inertia::render('AI/Chat', [
            'agents' => $agents,
            'sessions' => $sessions,
        ]);
    }

    public function chat(Request $request)
    {
        $data = $request->validate([
            'prompt' => ['required', 'string', 'max:4000'],
            'agent_id' => ['nullable', 'integer', 'exists:agents,id'],
            'files.*' => ['nullable', 'file', 'max:10240', 'mimes:txt,pdf,md,csv,json,xml,html,jpg,jpeg,png,gif,webp,doc,docx'],
        ]);

        $agent = isset($data['agent_id']) && $data['agent_id']
            ? Agent::findOrFail($data['agent_id'])
            : $this->ensureDefaultAgent($request->user()->organization_id);

        $files = $request->hasFile('files') ? $request->file('files') : [];

        return $this->orchestrator->execute(
            $agent,
            $request->user(),
            $data['prompt'],
            $files,
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
