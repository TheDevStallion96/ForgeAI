<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Governance\Models\TokenBudget;
use App\Domain\Governance\Services\BudgetEnforcer;

beforeEach(function () {
    $this->enforcer = app(BudgetEnforcer::class);
    $this->org = Organization::factory()->create();
});

it('authorizes organizations within budget', function () {
    TokenBudget::factory()->create([
        'organization_id' => $this->org->id,
        'monthly_limit' => 1_000_000,
        'current_usage' => 1000,
    ]);

    expect($this->enforcer->authorize($this->org))->toBeTrue();
});

it('blocks organizations with exhausted budget', function () {
    TokenBudget::factory()->exhausted()->create([
        'organization_id' => $this->org->id,
    ]);

    expect($this->enforcer->authorize($this->org))->toBeFalse();
});

it('creates budget record if none exists', function () {
    expect(TokenBudget::where('organization_id', $this->org->id)->exists())->toBeFalse();

    $this->enforcer->authorize($this->org);

    expect(TokenBudget::where('organization_id', $this->org->id)->exists())->toBeTrue();
});

it('authorizes after budget reset', function () {
    TokenBudget::factory()->create([
        'organization_id' => $this->org->id,
        'monthly_limit' => 1_000_000,
        'current_usage' => 999_999,
    ]);

    expect($this->enforcer->authorize($this->org))->toBeTrue();
});
