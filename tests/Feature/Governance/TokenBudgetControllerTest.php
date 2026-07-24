<?php

use App\Domain\AuthTenant\Models\User;
use App\Domain\Governance\Models\TokenBudget;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('redirects guests to login', function () {
    auth()->logout();

    $response = $this->get(route('governance.budget.show'));

    $response->assertRedirect(route('login'));
});

it('shows the budget page with a new budget', function () {
    $response = $this->get(route('governance.budget.show'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('governance/Budget/Show')
        ->has('budget'),
    );
});

it('shows existing budget usage', function () {
    TokenBudget::factory()->create([
        'organization_id' => $this->user->organization_id,
        'monthly_limit' => 1_000_000,
        'current_usage' => 250_000,
    ]);

    $response = $this->get(route('governance.budget.show'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('governance/Budget/Show')
        ->where('budget.monthly_limit', 1_000_000)
        ->where('budget.current_usage', 250_000)
        ->where('budget.usage_percentage', 0.25),
    );
});

it('updates the monthly budget limit', function () {
    TokenBudget::factory()->create([
        'organization_id' => $this->user->organization_id,
        'monthly_limit' => 1_000_000,
    ]);

    $response = $this->patch(route('governance.budget.update'), [
        'monthly_limit' => 5_000_000,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('token_budgets', [
        'organization_id' => $this->user->organization_id,
        'monthly_limit' => 5_000_000,
    ]);
});

it('validates minimum budget limit', function () {
    $response = $this->patch(route('governance.budget.update'), [
        'monthly_limit' => 500,
    ]);

    $response->assertInvalid(['monthly_limit']);
});
