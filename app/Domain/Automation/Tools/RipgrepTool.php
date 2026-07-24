<?php

namespace App\Domain\Automation\Tools;

use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;
use Illuminate\Support\Facades\Process;

class RipgrepTool implements ToolInterface
{
    public function name(): string
    {
        return 'ripgrep';
    }

    public function description(): string
    {
        return 'Search code with ripgrep (fast text search)';
    }

    public function parameterSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'pattern' => [
                    'type' => 'string',
                    'description' => 'Regex pattern to search for',
                ],
                'path' => [
                    'type' => 'string',
                    'description' => 'Path to search in',
                    'default' => '.',
                ],
                'type' => [
                    'type' => 'string',
                    'description' => 'File type filter (e.g., php, js, ts)',
                ],
            ],
            'required' => ['pattern'],
        ];
    }

    public function requiresHumanApproval(): bool
    {
        return false;
    }

    public function execute(array $parameters): ToolResult
    {
        $pattern = $parameters['pattern'] ?? '';
        $path = $parameters['path'] ?? '.';
        $type = $parameters['type'] ?? null;

        $args = ['rg', '--json'];
        if ($type) {
            $args[] = '--type';
            $args[] = $type;
        }
        $args[] = $pattern;
        $args[] = $path;

        try {
            $result = Process::run($args, $path, timeout: 10);

            return ToolResult::success($result->output());
        } catch (\Throwable $e) {
            return ToolResult::failure($e->getMessage());
        }
    }
}
