<?php

namespace Database\Factories\Domain\Agent\Models;

use App\Domain\Agent\Enums\MessageRole;
use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\Agent\Models\SessionMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionMessageFactory extends Factory
{
    protected $model = SessionMessage::class;

    public function definition(): array
    {
        return [
            'session_id' => ExecutionSession::factory(),
            'role' => MessageRole::User,
            'content' => fake()->sentence(),
            'tool_calls' => null,
        ];
    }

    public function assistant(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => MessageRole::Assistant,
            'content' => fake()->paragraph(),
        ]);
    }

    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => MessageRole::System,
            'content' => 'You are a helpful assistant.',
        ]);
    }
}
