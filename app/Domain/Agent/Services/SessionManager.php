<?php

namespace App\Domain\Agent\Services;

use App\Domain\Agent\Enums\MessageRole;
use App\Domain\Agent\Enums\SessionStatus;
use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\Agent\Models\SessionMessage;
use App\Domain\AuthTenant\Models\User;
use Laravel\Ai\Messages\Message;

class SessionManager
{
    public function startOrResume(int $agentId, User $user): ExecutionSession
    {
        $session = ExecutionSession::query()
            ->where('agent_id', $agentId)
            ->where('user_id', $user->id)
            ->where('status', SessionStatus::Active)
            ->latest()
            ->first();

        if ($session) {
            return $session;
        }

        return ExecutionSession::query()->create([
            'agent_id' => $agentId,
            'user_id' => $user->id,
            'status' => SessionStatus::Active,
            'context_tokens_accumulated' => 0,
        ]);
    }

    public function appendMessage(
        ExecutionSession $session,
        MessageRole $role,
        ?string $content = null,
        ?array $toolCalls = null,
    ): SessionMessage {
        return $session->messages()->create([
            'role' => $role,
            'content' => $content,
            'tool_calls' => $toolCalls,
        ]);
    }

    public function buildConversationHistory(ExecutionSession $session): array
    {
        return $session->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn (SessionMessage $msg) => new Message(
                $msg->role->value,
                $msg->content ?? '',
            ))
            ->all();
    }

    public function complete(ExecutionSession $session): void
    {
        $session->update(['status' => SessionStatus::Completed]);
    }

    public function fail(ExecutionSession $session): void
    {
        $session->update(['status' => SessionStatus::Failed]);
    }
}
