<?php

use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\AuthTenant\Models\User;
use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;
use App\Domain\Automation\Models\ToolExecution;
use App\Domain\Automation\Services\ToolExecutor;
use App\Domain\Automation\Services\ToolPermissionChecker;
use App\Domain\Automation\Services\ToolRegistry;

beforeEach(function () {
    $this->registry = new ToolRegistry;
    $this->permissionChecker = new ToolPermissionChecker;
    $this->executor = new ToolExecutor($this->registry, $this->permissionChecker);

    $this->user = User::factory()->create();
    $this->agent = Agent::factory()->create([
        'organization_id' => $this->user->organization_id,
    ]);
    $this->session = ExecutionSession::factory()->create([
        'agent_id' => $this->agent->id,
        'user_id' => $this->user->id,
    ]);
});

it('executes a registered tool successfully', function () {
    $tool = new class implements ToolInterface
    {
        public function name(): string
        {
            return 'test_tool';
        }

        public function description(): string
        {
            return 'Test';
        }

        public function parameterSchema(): array
        {
            return [];
        }

        public function requiresHumanApproval(): bool
        {
            return false;
        }

        public function execute(array $parameters): ToolResult
        {
            return ToolResult::success('executed');
        }
    };

    $this->registry->register($tool);
    $this->permissionChecker->allowTool('test_tool');

    $result = $this->executor->execute('test_tool', [], $this->session, $this->user);

    expect($result->isSuccess())->toBeTrue();
    expect($result->output)->toBe('executed');
});

it('returns failure for unregistered tools', function () {
    $result = $this->executor->execute('nonexistent', [], $this->session, $this->user);

    expect($result->isSuccess())->toBeFalse();
    expect($result->error)->toContain('not found');
});

it('returns failure for permission denied tools', function () {
    $tool = new class implements ToolInterface
    {
        public function name(): string
        {
            return 'restricted_tool';
        }

        public function description(): string
        {
            return 'Restricted';
        }

        public function parameterSchema(): array
        {
            return [];
        }

        public function requiresHumanApproval(): bool
        {
            return false;
        }

        public function execute(array $parameters): ToolResult
        {
            return ToolResult::success('ok');
        }
    };

    $this->registry->register($tool);

    $result = $this->executor->execute('restricted_tool', [], $this->session, $this->user);

    expect($result->isSuccess())->toBeFalse();
    expect($result->error)->toContain('Permission denied');
});

it('returns awaiting approval for HITL tools', function () {
    $tool = new class implements ToolInterface
    {
        public function name(): string
        {
            return 'hitl_tool';
        }

        public function description(): string
        {
            return 'HITL';
        }

        public function parameterSchema(): array
        {
            return [];
        }

        public function requiresHumanApproval(): bool
        {
            return true;
        }

        public function execute(array $parameters): ToolResult
        {
            return ToolResult::success('ok');
        }
    };

    $this->registry->register($tool);
    $this->permissionChecker->allowTool('hitl_tool');

    $result = $this->executor->execute('hitl_tool', [], $this->session, $this->user);

    expect($result->isSuccess())->toBeFalse();
    expect($result->error)->toContain('requires human approval');
});

it('persists tool execution record', function () {
    $tool = new class implements ToolInterface
    {
        public function name(): string
        {
            return 'persist_tool';
        }

        public function description(): string
        {
            return 'Persist';
        }

        public function parameterSchema(): array
        {
            return [];
        }

        public function requiresHumanApproval(): bool
        {
            return false;
        }

        public function execute(array $parameters): ToolResult
        {
            return ToolResult::success('done');
        }
    };

    $this->registry->register($tool);
    $this->permissionChecker->allowTool('persist_tool');

    $this->executor->execute('persist_tool', ['key' => 'value'], $this->session, $this->user);

    $execution = ToolExecution::where('tool_name', 'persist_tool')->first();
    expect($execution)->not->toBeNull();
    expect($execution->status)->toBe('completed');
    expect($execution->parameters)->toBe(['key' => 'value']);
});

it('records failed tool executions', function () {
    $tool = new class implements ToolInterface
    {
        public function name(): string
        {
            return 'fail_tool';
        }

        public function description(): string
        {
            return 'Fail';
        }

        public function parameterSchema(): array
        {
            return [];
        }

        public function requiresHumanApproval(): bool
        {
            return false;
        }

        public function execute(array $parameters): ToolResult
        {
            throw new RuntimeException('Something broke');
        }
    };

    $this->registry->register($tool);
    $this->permissionChecker->allowTool('fail_tool');

    $result = $this->executor->execute('fail_tool', [], $this->session, $this->user);

    expect($result->isSuccess())->toBeFalse();
    expect($result->error)->toContain('Something broke');

    $execution = ToolExecution::where('tool_name', 'fail_tool')->first();
    expect($execution->status)->toBe('failed');
});
