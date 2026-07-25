<?php

namespace App\Domain\McpGateway\Services;

use App\Domain\McpGateway\Contracts\McpClientInterface;
use App\Domain\McpGateway\Contracts\McpTransportInterface;
use App\Domain\McpGateway\Contracts\McpTransportType;

class McpClient implements McpClientInterface
{
    private ?McpTransportInterface $transport = null;

    private int $requestId = 0;

    public function __construct(
        private readonly McpTransportType $transportType = McpTransportType::Stdio,
        private readonly ?string $command = null,
        private readonly ?string $serverUrl = null,
        private readonly ?array $env = null,
    ) {}

    public function connect(): void
    {
        if ($this->transportType === McpTransportType::Stdio) {
            $this->transport = new McpStdioTransport($this->command, $this->env);
        } else {
            $this->transport = new McpSseTransport($this->serverUrl);
        }

        $response = $this->sendRequest('initialize', [
            'protocolVersion' => '2025-03-26',
            'capabilities' => new \stdClass,
            'clientInfo' => [
                'name' => 'forgeai-mcp-client',
                'version' => '1.0.0',
            ],
        ]);

        $this->sendNotification('notifications/initialized');
    }

    public function disconnect(): void
    {
        $this->transport?->close();
        $this->transport = null;
    }

    public function sendRequest(string $method, array $params = []): array
    {
        $this->requestId++;

        $this->transport->send([
            'jsonrpc' => '2.0',
            'id' => $this->requestId,
            'method' => $method,
            'params' => $params,
        ]);

        $response = $this->transport->receive();

        if (isset($response['error'])) {
            throw new \RuntimeException(
                "MCP request failed: {$response['error']['message']}",
                $response['error']['code']
            );
        }

        return $response['result'] ?? [];
    }

    public function sendNotification(string $method, array $params = []): void
    {
        $this->transport->send([
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
        ]);
    }

    public function listTools(): array
    {
        return $this->sendRequest('tools/list');
    }

    public function callTool(string $name, array $arguments): array
    {
        return $this->sendRequest('tools/call', [
            'name' => $name,
            'arguments' => $arguments,
        ]);
    }
}
