<?php

namespace Database\Factories\Domain\McpGateway\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\McpGateway\Models\McpConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

class McpConnectionFactory extends Factory
{
    protected $model = McpConnection::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->words(2, true) . ' MCP',
            'transport_type' => fake()->randomElement(['stdio', 'sse']),
            'command' => 'node ' . fake()->filePath(),
            'is_enabled' => true,
        ];
    }
}
