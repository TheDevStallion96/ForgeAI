<?php

namespace App\Domain\Automation\Services;

use App\Domain\AuthTenant\Models\User;

class ToolPermissionChecker
{
    protected array $allowedTools = [
        'read_file',
        'write_file',
        'list_directory',
        'run_command',
        'query_database',
        'http_request',
        'search_web',
        'execute_code',
        'git_commit',
        'git_branch',
        'git_diff',
        'vector_search',
        'ripgrep',
    ];

    protected array $hitlTools = [
        'delete_file',
        'run_command',
        'execute_code',
        'query_database',
        'git_commit',
        'git_branch',
    ];

    public function canExecute(User $user, string $toolName): bool
    {
        return in_array($toolName, $this->allowedTools);
    }

    public function requiresHitl(string $toolName): bool
    {
        return in_array($toolName, $this->hitlTools);
    }

    public function allowTool(string $toolName): self
    {
        if (! in_array($toolName, $this->allowedTools)) {
            $this->allowedTools[] = $toolName;
        }

        return $this;
    }

    public function requireHitl(string $toolName): self
    {
        if (! in_array($toolName, $this->hitlTools)) {
            $this->hitlTools[] = $toolName;
        }

        return $this;
    }
}
