<?php

namespace App\Domain\Automation\Tools;

use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;

class ListDirectoryTool implements ToolInterface
{
    public function name(): string
    {
        return 'list_directory';
    }

    public function description(): string
    {
        return 'List contents of a directory';
    }

    public function parameterSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'path' => [
                    'type' => 'string',
                    'description' => 'Path to the directory',
                ],
            ],
            'required' => ['path'],
        ];
    }

    public function requiresHumanApproval(): bool
    {
        return false;
    }

    public function execute(array $parameters): ToolResult
    {
        $path = $parameters['path'] ?? '';

        if (empty($path)) {
            return ToolResult::failure('Path is required');
        }

        $realPath = realpath($path);
        if (! $realPath || ! str_starts_with($realPath, base_path())) {
            return ToolResult::failure('Access denied: path outside workspace');
        }

        if (! is_dir($realPath)) {
            return ToolResult::failure('Not a directory');
        }

        $files = scandir($realPath);
        $result = array_map(function ($file) use ($realPath) {
            $fullPath = $realPath.'/'.$file;

            return [
                'name' => $file,
                'type' => is_dir($fullPath) ? 'directory' : 'file',
                'size' => is_file($fullPath) ? filesize($fullPath) : null,
            ];
        }, array_filter($files, fn ($f) => $f !== '.' && $f !== '..'));

        return ToolResult::success(array_values($result));
    }
}
