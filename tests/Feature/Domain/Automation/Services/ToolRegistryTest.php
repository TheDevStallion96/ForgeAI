<?php

use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;
use App\Domain\Automation\Services\ToolRegistry;

beforeEach(function () {
    $this->registry = new ToolRegistry;
});

it('registers and retrieves a tool', function () {
    $tool = new class implements ToolInterface
    {
        public function name(): string
        {
            return 'test_tool';
        }

        public function description(): string
        {
            return 'A test tool';
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

    expect($this->registry->get('test_tool'))->toBe($tool);
    expect($this->registry->has('test_tool'))->toBeTrue();
});

it('returns null for unregistered tools', function () {
    expect($this->registry->get('nonexistent'))->toBeNull();
    expect($this->registry->has('nonexistent'))->toBeFalse();
});

it('returns all registered tools', function () {
    $tool1 = new class implements ToolInterface
    {
        public function name(): string
        {
            return 'tool_a';
        }

        public function description(): string
        {
            return 'Tool A';
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

    $tool2 = new class implements ToolInterface
    {
        public function name(): string
        {
            return 'tool_b';
        }

        public function description(): string
        {
            return 'Tool B';
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

    $this->registry->register($tool1);
    $this->registry->register($tool2);

    expect($this->registry->all())->toHaveCount(2);
});

it('returns tool definitions', function () {
    $tool = new class implements ToolInterface
    {
        public function name(): string
        {
            return 'test_tool';
        }

        public function description(): string
        {
            return 'A test tool';
        }

        public function parameterSchema(): array
        {
            return ['type' => 'object'];
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
    $definitions = $this->registry->getDefinitions();

    expect($definitions)->toHaveCount(1);
    expect($definitions[0]['name'])->toBe('test_tool');
    expect($definitions[0]['requires_hitl'])->toBeTrue();
});

it('chains register calls', function () {
    $tool = new class implements ToolInterface
    {
        public function name(): string
        {
            return 'chain_tool';
        }

        public function description(): string
        {
            return 'Chain tool';
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

    expect($this->registry->has('chain_tool'))->toBeTrue();
});
