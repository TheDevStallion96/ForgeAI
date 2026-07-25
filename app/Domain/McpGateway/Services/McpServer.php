<?php

namespace App\Domain\McpGateway\Services;

use App\Domain\Automation\Contracts\ToolResult;
use App\Domain\Automation\Services\ToolRegistry;
use App\Domain\McpGateway\Contracts\McpServerInterface;

class McpServer implements McpServerInterface
{
    private string $serverName;

    private string $serverVersion;

    public function __construct(
        private readonly ToolRegistry $toolRegistry,
    ) {
        $this->serverName = config('mcp.server_name', 'forgeai-mcp');
        $this->serverVersion = config('mcp.server_version', '1.0.0');
    }

    public function handleInitialize(array $params): array
    {
        return [
            'protocolVersion' => $params['protocolVersion'] ?? '2025-03-26',
            'capabilities' => [
                'tools' => new \stdClass,
                'resources' => new \stdClass,
            ],
            'serverInfo' => [
                'name' => $this->serverName,
                'version' => $this->serverVersion,
            ],
        ];
    }

    public function handleToolsList(): array
    {
        $definitions = $this->toolRegistry->getDefinitions();
        $tools = [];

        foreach ($definitions as $definition) {
            $tools[] = [
                'name' => $definition['name'],
                'description' => $definition['description'],
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => $definition['parameters'],
                ],
            ];
        }

        return $tools;
    }

    public function handleToolsCall(string $name, array $arguments): ToolResult
    {
        $tool = $this->toolRegistry->get($name);

        if ($tool === null) {
            return ToolResult::failure("Unknown tool: {$name}");
        }

        if ($tool->requiresHumanApproval()) {
            return ToolResult::failure("Tool {$name} requires HITL approval and cannot be invoked via MCP directly");
        }

        return $tool->execute($arguments);
    }

    public function handleResourcesList(): array
    {
        return [
            [
                'uri' => 'forgeai://graph/nodes',
                'name' => 'Engineering Graph Nodes',
                'description' => 'Query engineering graph nodes by type',
                'mimeType' => 'application/json',
            ],
            [
                'uri' => 'forgeai://tools',
                'name' => 'Available Tools',
                'description' => 'List all registered tools and their schemas',
                'mimeType' => 'application/json',
            ],
        ];
    }

    public function handleResourcesRead(string $uri): string
    {
        return match ($uri) {
            'forgeai://tools' => json_encode($this->handleToolsList()),
            default => throw new \InvalidArgumentException("Unknown resource URI: {$uri}"),
        };
    }

    public function handleJsonRpcRequest(array $request): array
    {
        $id = $request['id'] ?? null;
        $method = $request['method'] ?? '';
        $params = $request['params'] ?? [];

        try {
            $result = match ($method) {
                'initialize' => $this->handleInitialize($params),
                'tools/list' => $this->handleToolsList(),
                'tools/call' => $this->handleToolsCall($params['name'] ?? '', $params['arguments'] ?? []),
                'resources/list' => $this->handleResourcesList(),
                'resources/read' => $this->handleResourcesRead($params['uri'] ?? ''),
                'notifications/initialized' => null,
                default => throw new \InvalidArgumentException("Method not found: {$method}"),
            };

            if ($result === null) {
                return [];
            }

            if ($result instanceof ToolResult) {
                return [
                    'jsonrpc' => '2.0',
                    'id' => $id,
                    'result' => [
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $result->isSuccess()
                                    ? (is_string($result->output) ? $result->output : json_encode($result->output))
                                    : $result->error,
                            ],
                        ],
                        'isError' => ! $result->isSuccess(),
                    ],
                ];
            }

            return [
                'jsonrpc' => '2.0',
                'id' => $id,
                'result' => $result,
            ];
        } catch (\Throwable $e) {
            return [
                'jsonrpc' => '2.0',
                'id' => $id,
                'error' => [
                    'code' => -32603,
                    'message' => $e->getMessage(),
                ],
            ];
        }
    }
}
