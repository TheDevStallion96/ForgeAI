<?php

namespace Database\Factories\Domain\Agent\Models;

use App\Domain\Agent\Enums\SessionStatus;
use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\AuthTenant\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExecutionSessionFactory extends Factory
{
    protected $model = ExecutionSession::class;

    public function definition(): array
    {
        return [
            'agent_id' => Agent::factory(),
            'user_id' => User::factory(),
            'status' => SessionStatus::Active,
            'context_tokens_accumulated' => 0,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SessionStatus::Completed,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SessionStatus::Failed,
        ]);
    }
}
