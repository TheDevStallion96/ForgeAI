<?php

namespace App\Domain\Automation\Tools;

use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;

class GitCommitTool implements ToolInterface
{
    public function name(): string
    {
        return 'git_commit';
    }

    public function description(): string
    {
        return 'Stage and commit changes';
    }

    public function parameterSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'message' => [
                    'type' => 'string',
                    'description' => 'Commit message',
                ],
                'files' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'Files to stage (optional, defaults to all)',
                ],
                'cwd' => [
                    'type' => 'string',
                    'description' => 'Working directory',
                ],
            ],
            'required' => ['message'],
        ];
    }

    public function requiresHumanApproval(): bool
    {
        return true;
    }

    public function execute(array $parameters): ToolResult
    {
        $message = $parameters['message'] ?? '';
        $files = $parameters['files'] ?? [];
        $cwd = $parameters['cwd'] ?? base_path();

        if (empty($message)) {
            return ToolResult::failure('Commit message is required');
        }

        $filesArg = ! empty($files) ? implode(' ', array_map('escapeshellarg', $files)) : '.';
        $command = "git add {$filesArg} && git commit -m ".escapeshellarg($message);

        $output = shell_exec("cd {$cwd} && {$command} 2>&1");

        return ToolResult::success($output ?? '');
    }
}
