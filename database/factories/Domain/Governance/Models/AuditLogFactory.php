<?php

namespace Database\Factories\Domain\Governance\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\AuthTenant\Models\User;
use App\Domain\Governance\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        $payload = ['message' => fake()->sentence()];

        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'event_type' => fake()->randomElement(['agent.executed', 'tool.completed', 'token.recorded']),
            'payload' => $payload,
            'payload_hash' => AuditLog::computeHash($payload),
            'previous_hash' => null,
            'metadata' => ['ip' => fake()->ipv4()],
        ];
    }
}
