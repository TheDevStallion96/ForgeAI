<?php

namespace App\Ai\Agents;

use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\AuthTenant\Models\User;
use App\Domain\Automation\Services\ToolExecutor;
use App\Domain\Automation\Services\ToolRegistry;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

#[MaxSteps(10)]
class ForgeAgent implements Agent, HasTools
{
    use Promptable;

    public function __construct(
        private readonly ToolRegistry $toolRegistry,
        private readonly ToolExecutor $toolExecutor,
        private readonly ExecutionSession $session,
        private readonly User $user,
        private readonly array $tools,
    ) {}

    public function instructions(): Stringable|string
    {
        return '';
    }

    public function messages(): iterable
    {
        return [];
    }

    public function tools(): iterable
    {
        return $this->tools;
    }
}
