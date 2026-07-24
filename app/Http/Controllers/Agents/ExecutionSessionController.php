<?php

namespace App\Http\Controllers\Agents;

use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\Agent\Services\SessionManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExecutionSessionController extends Controller
{
    public function __construct(
        private readonly SessionManager $sessionManager,
    ) {}

    public function index(Request $request, ?Agent $agent = null): Response
    {
        $query = ExecutionSession::query()
            ->where('organization_id', $request->user()->organization_id)
            ->with('agent:id,name');

        if ($agent && $agent->organization_id === $request->user()->organization_id) {
            $query->where('agent_id', $agent->id);
        }

        $sessions = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->through(fn (ExecutionSession $session) => [
                'id' => $session->id,
                'agent_name' => $session->agent->name,
                'status' => $session->status->value,
                'message_count' => $session->messages()->count(),
                'context_tokens' => $session->context_tokens_accumulated,
                'created_at' => $session->created_at->toISOString(),
                'updated_at' => $session->updated_at?->toISOString(),
            ]);

        return Inertia::render('agents/Sessions/Index', [
            'sessions' => $sessions,
            'agent' => $agent ? [
                'id' => $agent->id,
                'name' => $agent->name,
            ] : null,
        ]);
    }

    public function show(Request $request, ExecutionSession $session): Response
    {
        abort_unless($session->organization_id === $request->user()->organization_id, 403);

        $session->load(['agent:id,name', 'messages']);

        return Inertia::render('agents/Sessions/Show', [
            'session' => [
                'id' => $session->id,
                'agent' => [
                    'id' => $session->agent->id,
                    'name' => $session->agent->name,
                ],
                'status' => $session->status->value,
                'context_tokens' => $session->context_tokens_accumulated,
                'created_at' => $session->created_at->toISOString(),
            ],
            'messages' => $session->messages->map(fn ($message) => [
                'id' => $message->id,
                'role' => $message->role->value,
                'content' => $message->content,
                'tool_calls' => $message->tool_calls,
                'created_at' => $message->created_at?->toISOString(),
            ]),
        ]);
    }

    public function destroy(Request $request, ExecutionSession $session): RedirectResponse
    {
        abort_unless($session->organization_id === $request->user()->organization_id, 403);

        $this->sessionManager->fail($session);

        Inertia::flash('toast', ['type' => 'info', 'message' => __('Session ended.')]);

        return to_route('agents.sessions.index');
    }
}
