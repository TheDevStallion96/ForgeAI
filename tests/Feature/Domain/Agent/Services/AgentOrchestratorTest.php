<?php

use App\Domain\Agent\Enums\MessageRole;
use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Services\AgentOrchestrator;
use App\Domain\AuthTenant\Models\User;
use Laravel\Ai\AnonymousAgent;
use Laravel\Ai\Responses\StreamableAgentResponse;

beforeEach(function () {
    $this->orchestrator = app(AgentOrchestrator::class);
    $this->user = User::factory()->create();
    $this->agent = Agent::factory()->create([
        'organization_id' => $this->user->organization_id,
        'system_instruction' => 'You are a test agent.',
        'primary_model' => 'openai:gpt-4o-mini',
    ]);
});

it('executes a prompt and persists messages', function () {
    AnonymousAgent::fake(['Test response']);

    $stream = $this->orchestrator->execute(
        $this->agent,
        $this->user,
        'Say hello',
    );

    expect($stream)->toBeInstanceOf(StreamableAgentResponse::class);

    $messages = $this->agent->executionSessions()
        ->latest()
        ->first()
        ->messages()
        ->orderBy('created_at')
        ->get();

    expect($messages)->toHaveCount(1);
    expect($messages[0]->role)->toBe(MessageRole::User);
    expect($messages[0]->content)->toBe('Say hello');
});
