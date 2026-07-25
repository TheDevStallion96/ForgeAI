<?php

namespace Database\Factories\Domain\Automation\Models;

use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Automation\Models\ToolExecution;
use Illuminate\Database\Eloquent\Factories\Factory;

class ToolExecutionFactory extends Factory
{
    protected $model = ToolExecution::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'session_id' => ExecutionSession::factory(),
            'tool_name' => 'read_file',
            'parameters' => ['path' => 'test.txt'],
            'status' => 'pending',
            'requires_hitl' => false,
        ];
    }

    public function running(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'result' => ['success' => true, 'output' => 'File content'],
            'started_at' => now()->subMinute(),
            'completed_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'result' => ['success' => false, 'error' => 'File not found'],
            'started_at' => now()->subMinute(),
            'completed_at' => now(),
        ]);
    }

    public function awaitingApproval(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'awaiting_approval',
            'requires_hitl' => true,
        ]);
    }
}
