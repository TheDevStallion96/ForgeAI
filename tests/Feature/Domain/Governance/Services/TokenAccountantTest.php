<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Governance\Models\TokenBudget;
use App\Domain\Governance\Models\TokenLedger;
use App\Domain\Governance\Services\TokenAccountant;

beforeEach(function () {
    $this->accountant = app(TokenAccountant::class);
    $this->org = Organization::factory()->create();
});

it('records a token ledger entry', function () {
    $ledger = $this->accountant->record(
        provider: 'openai',
        model: 'gpt-4o-mini',
        promptTokens: 100,
        completionTokens: 50,
        organizationId: $this->org->id,
    );

    expect($ledger)->toBeInstanceOf(TokenLedger::class);
    expect($ledger->prompt_tokens)->toBe(100);
    expect($ledger->completion_tokens)->toBe(50);
    expect($ledger->provider)->toBe('openai');
    expect($ledger->model)->toBe('gpt-4o-mini');
});

it('calculates cost correctly', function () {
    $cost = $this->accountant->calculateCost('openai', 'gpt-4o', 1_000_000, 1_000_000);

    expect($cost)->toBe(12.5);
});

it('calculates cost for small usage', function () {
    $cost = $this->accountant->calculateCost('openai', 'gpt-4o-mini', 1000, 500);

    expect($cost)->toBeGreaterThan(0);
    expect($cost)->toBeLessThan(0.01);
});

it('returns zero cost for unknown models', function () {
    $cost = $this->accountant->calculateCost('unknown', 'unknown-model', 100, 100);

    expect($cost)->toBe(0.0);
});

it('deducts budget when recording', function () {
    $this->accountant->record(
        provider: 'openai',
        model: 'gpt-4o-mini',
        promptTokens: 500,
        completionTokens: 500,
        organizationId: $this->org->id,
    );

    $budget = TokenBudget::where('organization_id', $this->org->id)->first();
    expect($budget)->not->toBeNull();
    expect($budget->current_usage)->toBe(1000);
});

it('accumulates usage across multiple records', function () {
    $this->accountant->record('openai', 'gpt-4o-mini', 100, 100, organizationId: $this->org->id);
    $this->accountant->record('anthropic', 'claude-3-5-sonnet', 200, 200, organizationId: $this->org->id);

    $usage = $this->accountant->getUsage($this->org->id);
    expect($usage)->toBe(600);
});

it('filters usage by provider', function () {
    $this->accountant->record('openai', 'gpt-4o-mini', 100, 100, organizationId: $this->org->id);
    $this->accountant->record('anthropic', 'claude-3-5-sonnet', 200, 200, organizationId: $this->org->id);

    $openaiUsage = $this->accountant->getUsage($this->org->id, 'openai');
    expect($openaiUsage)->toBe(200);
});
