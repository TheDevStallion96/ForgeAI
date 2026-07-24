<?php

namespace Database\Factories\Domain\Agent\Models;

use App\Domain\Agent\Models\Agent;
use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentFactory extends Factory
{
    protected $model = Agent::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->words(2, true),
            'primary_model' => 'openai:gpt-4o-mini',
            'fallback_model' => 'anthropic:claude-haiku-4-5-20251001',
            'system_instruction' => 'You are a helpful assistant.',
            'temperature' => 0.70,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
