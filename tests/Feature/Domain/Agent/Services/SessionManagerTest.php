<?php

use App\Domain\Agent\Enums\MessageRole;
use App\Domain\Agent\Enums\SessionStatus;
use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\Agent\Services\SessionManager;
use App\Domain\AuthTenant\Models\User;
use Laravel\Ai\Messages\Message;

beforeEach(function () {
    $this->manager = app(SessionManager::class);
    $this->user = User::factory()->create();
    $this->agent = Agent::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);
});

it('creates a new session when none exists', function () {
    $session = $this->manager->startOrResume($this->agent->id, $this->user);

    expect($session)->toBeInstanceOf(ExecutionSession::class);
    expect($session->status)->toBe(SessionStatus::Active);
    expect($session->agent_id)->toBe($this->agent->id);
    expect($session->user_id)->toBe($this->user->id);
});

it('resumes the latest active session', function () {
    $first = $this->manager->startOrResume($this->agent->id, $this->user);
    $second = $this->manager->startOrResume($this->agent->id, $this->user);

    expect($second->id)->toBe($first->id);
});

it('creates a new session when previous is completed', function () {
    $completed = $this->manager->startOrResume($this->agent->id, $this->user);
    $completed->update(['status' => SessionStatus::Completed]);

    $new = $this->manager->startOrResume($this->agent->id, $this->user);

    expect($new->id)->not->toBe($completed->id);
    expect($new->status)->toBe(SessionStatus::Active);
});

it('appends a message to a session', function () {
    $session = $this->manager->startOrResume($this->agent->id, $this->user);

    $message = $this->manager->appendMessage(
        $session,
        MessageRole::User,
        'Hello',
    );

    expect($message->session_id)->toBe($session->id);
    expect($message->role)->toBe(MessageRole::User);
    expect($message->content)->toBe('Hello');
});

it('builds conversation history as Message objects', function () {
    $session = $this->manager->startOrResume($this->agent->id, $this->user);
    $this->manager->appendMessage($session, MessageRole::User, 'Hi');
    $this->manager->appendMessage($session, MessageRole::Assistant, 'Hello!');

    $history = $this->manager->buildConversationHistory($session);

    expect($history)->toHaveCount(2);
    expect($history[0])->toBeInstanceOf(Message::class);
    expect($history[0]->content)->toBe('Hi');
    expect($history[1]->content)->toBe('Hello!');
});

it('marks session as completed', function () {
    $session = $this->manager->startOrResume($this->agent->id, $this->user);

    $this->manager->complete($session);

    expect($session->fresh()->status)->toBe(SessionStatus::Completed);
});

it('marks session as failed', function () {
    $session = $this->manager->startOrResume($this->agent->id, $this->user);

    $this->manager->fail($session);

    expect($session->fresh()->status)->toBe(SessionStatus::Failed);
});
