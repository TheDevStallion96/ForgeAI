<?php

namespace App\Domain\Automation\Tools;

use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;

class WriteFileTool implements ToolInterface
{
    public function name(): string
    {
        return 'write_file';
    }

    public function description(): string
    {
        return 'Write content to a file';
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
                'content' => [
                    'type' => 'string',
                    'description' => 'Content to write',
                ],
            ],
            'required' => ['path', 'content'],
        ];
    }

    public function requiresHumanApproval(): bool
    {
        return false;
    }

    public function execute(array $parameters): ToolResult
    {
        $path = $parameters['path'] ?? '';
        $content = $parameters['content'] ?? '';

        if (empty($path)) {
            return ToolResult::failure('Path is required');
        }

        // Security: prevent directory traversal
        $realPath = realpath($path);
        $basePath = base_path();

        // If file doesn't exist, check parent directory
        if (! $realPath) {
            $dir = dirname($path);
            $realDir = realpath($dir);
            if (! $realDir || ! str_starts_with($realDir, $basePath)) {
                return ToolResult::failure('Access denied: path outside workspace');
            }
        } elseif (! str_starts_with($realPath, $basePath)) {
            return ToolResult::failure('Access denied: path outside workspace');
        }

        // Ensure directory exists
        $dir = dirname($path);
        if (! is_dir($dir)) {
            if (! mkdir($dir, 0755, true)) {
                return ToolResult::failure('Failed to create directory');
            }
        }

        if (file_put_contents($path, $content) === false) {
            return ToolResult::failure('Failed to write file');
        }

        return ToolResult::success("File written: {$path}");
    }
}
