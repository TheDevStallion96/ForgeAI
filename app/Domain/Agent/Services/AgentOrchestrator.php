<?php

namespace App\Domain\Agent\Services;

use App\Ai\Tools\ForgeToolAdapter;
use App\Domain\Agent\Enums\MessageRole;
use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\AIEngine\Contracts\AIEngine;
use App\Domain\AIEngine\Data\CompletionRequest;
use App\Domain\AuthTenant\Models\User;
use App\Domain\Automation\Services\ToolExecutor;
use App\Domain\Automation\Services\ToolRegistry;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Providers\Tools\FileSearch;
use Laravel\Ai\Responses\StreamableAgentResponse;

class AgentOrchestrator
{
    public function __construct(
        private readonly AIEngine $engine,
        private readonly SessionManager $sessions,
        private readonly ToolRegistry $toolRegistry,
        private readonly ToolExecutor $toolExecutor,
    ) {}

    public function execute(Agent $agent, User $user, string $prompt, array $attachments = []): StreamableAgentResponse
    {
        $session = $this->sessions->startOrResume($agent->id, $user);

        $this->sessions->appendMessage($session, MessageRole::User, $prompt);

        $history = $this->sessions->buildConversationHistory($session);

        $request = new CompletionRequest(
            prompt: $prompt,
            systemInstruction: $agent->system_instruction,
            provider: $this->resolveProviderCascade($agent),
            model: $agent->primary_model,
            attachments: $attachments,
            tools: $this->buildTools($session, $user),
            maxSteps: 10,
        );

        $stream = $this->engine->stream($request);

        $stream->then(function ($response) use ($session): void {
            $text = $response->text ?? '';

            if ($text !== '') {
                $this->sessions->appendMessage(
                    $session,
                    MessageRole::Assistant,
                    $text,
                );
            }

            $this->sessions->complete($session);
        });

        return $stream;
    }

    private function buildTools(ExecutionSession $session, User $user): array
    {
        $tools = $this->toolRegistry->all()->map(
            fn ($tool) => $this->wrapTool($tool, $session, $user),
        )->all();

        $storeId = $user->organization?->vector_store_id;

        if ($storeId) {
            $tools[] = new FileSearch(stores: [$storeId]);
        }

        return $tools;
    }

    private function wrapTool($tool, ExecutionSession $session, User $user): Tool
    {
        return new ForgeToolAdapter($tool, $this->toolExecutor, $session, $user);
    }

    private function resolveProviderCascade(Agent $agent): array
    {
        $providers = [$agent->primary_model];

        if ($agent->fallback_model) {
            $providers[] = $agent->fallback_model;
        }

        return $providers;
    }
}
