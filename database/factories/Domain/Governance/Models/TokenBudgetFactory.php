<?php

namespace Database\Factories\Domain\Governance\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Governance\Models\TokenBudget;
use Illuminate\Database\Eloquent\Factories\Factory;

class TokenBudgetFactory extends Factory
{
    protected $model = TokenBudget::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'monthly_limit' => 1_000_000,
            'current_usage' => 0,
            'reset_at' => now()->addMonth(),
        ];
    }

    public function exhausted(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_usage' => $attributes['monthly_limit'],
        ]);
    }
}
