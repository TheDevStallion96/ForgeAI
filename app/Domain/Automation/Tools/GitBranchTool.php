<?php

namespace App\Domain\Automation\Tools;

use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;

class GitBranchTool implements ToolInterface
{
    public function name(): string
    {
        return 'git_branch';
    }

    public function description(): string
    {
        return 'Create, list, or switch Git branches';
    }

    public function parameterSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'action' => [
                    'type' => 'string',
                    'enum' => ['list', 'create', 'switch', 'delete'],
                    'description' => 'Action to perform',
                ],
                'branch_name' => [
                    'type' => 'string',
                    'description' => 'Branch name (for create/switch/delete)',
                ],
                'cwd' => [
                    'type' => 'string',
                    'description' => 'Working directory',
                ],
            ],
            'required' => ['action'],
        ];
    }

    public function requiresHumanApproval(): bool
    {
        return true;
    }

    public function execute(array $parameters): ToolResult
    {
        $action = $parameters['action'] ?? '';
        $branch = $parameters['branch_name'] ?? '';
        $cwd = $parameters['cwd'] ?? base_path();

        $command = match ($action) {
            'list' => 'git branch',
            'create' => "git branch {$branch}",
            'switch' => "git checkout {$branch}",
            'delete' => "git branch -D {$branch}",
            default => '',
        };

        if (empty($command)) {
            return ToolResult::failure('Invalid action');
        }

        $output = shell_exec("cd {$cwd} && {$command} 2>&1");

        return ToolResult::success($output ?? '');
    }
}
