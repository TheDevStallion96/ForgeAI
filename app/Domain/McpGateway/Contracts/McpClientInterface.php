<?php

namespace App\Domain\McpGateway\Contracts;

interface McpClientInterface
{
    public function connect(): void;

    public function disconnect(): void;

    public function sendRequest(string $method, array $params = []): array;

    public function listTools(): array;

    public function callTool(string $name, array $arguments): array;
}
