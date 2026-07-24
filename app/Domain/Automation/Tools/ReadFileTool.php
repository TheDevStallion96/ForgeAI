<?php

namespace App\Domain\Automation\Tools;

use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;

class ReadFileTool implements ToolInterface
{
    public function name(): string
    {
        return 'read_file';
    }

    public function description(): string
    {
        return 'Read the contents of a file';
    }

    public function parameterSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'path' => [
                    'type' => 'string',
                    'description' => 'Path to the file',
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

        // Security: prevent directory traversal
        $realPath = realpath($path);
        if (! $realPath || ! str_starts_with($realPath, base_path())) {
            return ToolResult::failure('Access denied: path outside workspace');
        }

        if (! file_exists($realPath)) {
            return ToolResult::failure('File not found');
        }

        if (! is_readable($realPath)) {
            return ToolResult::failure('File not readable');
        }

        $content = file_get_contents($realPath);

        return ToolResult::success($content);
    }
}
