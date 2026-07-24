<?php

namespace App\Domain\Automation\Tools;

use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;

class DeleteFileTool implements ToolInterface
{
    public function name(): string
    {
        return 'delete_file';
    }

    public function description(): string
    {
        return 'Delete a file';
    }

    public function parameterSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'path' => [
                    'type' => 'string',
                    'description' => 'Path to the file to delete',
                ],
            ],
            'required' => ['path'],
        ];
    }

    public function requiresHumanApproval(): bool
    {
        return true;
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

        if (! file_exists($realPath)) {
            return ToolResult::failure('File not found');
        }

        if (is_dir($realPath)) {
            return ToolResult::failure('Cannot delete directories');
        }

        if (! unlink($realPath)) {
            return ToolResult::failure('Failed to delete file');
        }

        return ToolResult::success("File deleted: {$path}");
    }
}
