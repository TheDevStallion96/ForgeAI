<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\AuthTenant\Models\User;
use App\Domain\Governance\Models\AuditLog;
use App\Domain\Governance\Services\AuditLogger;

beforeEach(function () {
    $this->logger = app(AuditLogger::class);
    $this->org = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
});

it('logs an event', function () {
    $log = $this->logger->log('agent.executed', [
        'session_id' => 1,
        'tokens_used' => 500,
    ], $this->org, $this->user);

    expect($log)->toBeInstanceOf(AuditLog::class);
    expect($log->event_type)->toBe('agent.executed');
    expect($log->organization_id)->toBe($this->org->id);
});

it('computes payload hash', function () {
    $log = $this->logger->log('test.event', ['key' => 'value'], $this->org);

    $expectedHash = AuditLog::computeHash(['key' => 'value']);
    expect($log->payload_hash)->toBe($expectedHash);
});

it('chains hashes correctly', function () {
    $log1 = $this->logger->log('event.1', ['seq' => 1], $this->org);
    $log2 = $this->logger->log('event.2', ['seq' => 2], $this->org);

    expect($log2->previous_hash)->toBe($log1->payload_hash);
});

it('detects tampered logs', function () {
    $log = $this->logger->log('test.event', ['data' => 'original'], $this->org);

    $log->update(['payload' => ['data' => 'tampered']]);

    expect($log->isTampered())->toBeTrue();
});

it('verifies unbroken chain', function () {
    $this->logger->log('event.1', ['seq' => 1], $this->org);
    $this->logger->log('event.2', ['seq' => 2], $this->org);
    $this->logger->log('event.3', ['seq' => 3], $this->org);

    expect($this->logger->verifyChain($this->org->id))->toBeTrue();
});

it('detects broken chain', function () {
    $this->logger->log('event.1', ['seq' => 1], $this->org);

    AuditLog::where('event_type', 'event.1')->update([
        'payload' => ['seq' => 999],
    ]);

    expect($this->logger->verifyChain($this->org->id))->toBeFalse();
});

it('logs critical events with severity', function () {
    $log = $this->logger->logCritical(
        'injection.detected',
        ['risk_score' => 0.95],
        $this->org,
        $this->user,
    );

    expect($log->metadata['severity'])->toBe('critical');
});
