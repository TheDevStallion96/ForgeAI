<?php

namespace App\Domain\Agent\Services;

use App\Domain\Agent\Enums\MessageRole;
use App\Domain\Agent\Models\Agent;
use App\Domain\AIEngine\Contracts\AIEngine;
use App\Domain\AIEngine\Data\CompletionRequest;
use App\Domain\AuthTenant\Models\User;
use Laravel\Ai\Responses\StreamableAgentResponse;

class AgentOrchestrator
{
    public function __construct(
        private readonly AIEngine $engine,
        private readonly SessionManager $sessions,
    ) {}

    public function execute(Agent $agent, User $user, string $prompt): StreamableAgentResponse
    {
        $session = $this->sessions->startOrResume($agent->id, $user);

        $this->sessions->appendMessage($session, MessageRole::User, $prompt);

        $history = $this->sessions->buildConversationHistory($session);

        $request = new CompletionRequest(
            prompt: $prompt,
            systemInstruction: $agent->system_instruction,
            provider: $this->resolveProviderCascade($agent),
            model: $agent->primary_model,
        );

        $stream = $this->engine->stream($request);

        $stream->then(function ($response) use ($session): void {
            $this->sessions->appendMessage(
                $session,
                MessageRole::Assistant,
                $response->text ?? '',
            );

            $this->sessions->complete($session);
        });

        return $stream;
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
