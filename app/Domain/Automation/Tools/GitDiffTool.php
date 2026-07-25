<?php

namespace App\Domain\Automation\Tools;

use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;

class GitDiffTool implements ToolInterface
{
    public function name(): string
    {
        return 'git_diff';
    }

    public function description(): string
    {
        return 'Show Git diff';
    }

    public function parameterSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'staged' => [
                    'type' => 'boolean',
                    'description' => 'Show staged changes',
                ],
                'cwd' => [
                    'type' => 'string',
                    'description' => 'Working directory',
                ],
            ],
        ];
    }

    public function requiresHumanApproval(): bool
    {
        return false;
    }

    public function execute(array $parameters): ToolResult
    {
        $staged = $parameters['staged'] ?? false;
        $cwd = $parameters['cwd'] ?? base_path();

        $flag = $staged ? '--staged' : '';
        $command = "git diff {$flag}";

        $output = shell_exec("cd {$cwd} && {$command} 2>&1");

        return ToolResult::success($output ?? '');
    }
}
