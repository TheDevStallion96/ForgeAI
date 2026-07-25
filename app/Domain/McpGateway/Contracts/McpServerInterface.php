<?php

namespace App\Domain\McpGateway\Contracts;

use App\Domain\Automation\Contracts\ToolResult;

interface McpServerInterface
{
    public function handleInitialize(array $params): array;

    public function handleToolsList(): array;

    public function handleToolsCall(string $name, array $arguments): ToolResult;

    public function handleResourcesList(): array;

    public function handleResourcesRead(string $uri): string;
}
