<?php

namespace Database\Factories\Domain\Governance\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Governance\Models\TokenLedger;
use Illuminate\Database\Eloquent\Factories\Factory;

class TokenLedgerFactory extends Factory
{
    protected $model = TokenLedger::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'session_id' => null,
            'agent_id' => null,
            'provider' => 'openai',
            'model' => 'gpt-4o-mini',
            'prompt_tokens' => fake()->numberBetween(50, 500),
            'completion_tokens' => fake()->numberBetween(50, 500),
            'estimated_cost_usd' => fake()->randomFloat(6, 0.0001, 0.01),
        ];
    }

    public function anthropic(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'anthropic',
            'model' => 'claude-3-5-sonnet',
        ]);
    }
}
