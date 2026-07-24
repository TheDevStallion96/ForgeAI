<?php

namespace App\Domain\Automation\Tools;

use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;
use App\Domain\Automation\Services\SandboxedToolRunner;

class RunCommandTool implements ToolInterface
{
    public function __construct(
        protected SandboxedToolRunner $runner,
    ) {}

    public function name(): string
    {
        return 'run_command';
    }

    public function description(): string
    {
        return 'Execute a shell command in a sandboxed environment';
    }

    public function parameterSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'command' => [
                    'type' => 'string',
                    'description' => 'Command to execute',
                ],
                'cwd' => [
                    'type' => 'string',
                    'description' => 'Working directory (optional)',
                ],
            ],
            'required' => ['command'],
        ];
    }

    public function requiresHumanApproval(): bool
    {
        return true;
    }

    public function execute(array $parameters): ToolResult
    {
        $command = $parameters['command'] ?? '';
        $cwd = $parameters['cwd'] ?? null;

        if (empty($command)) {
            return ToolResult::failure('Command is required');
        }

        return $this->runner->run($command, [], $cwd);
    }
}
