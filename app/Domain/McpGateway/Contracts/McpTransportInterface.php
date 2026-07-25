<?php

namespace App\Domain\McpGateway\Contracts;

enum McpTransportType: string
{
    case Stdio = 'stdio';
    case Sse = 'sse';
}

interface McpTransportInterface
{
    public function send(array $message): void;

    public function receive(): ?array;

    public function isConnected(): bool;

    public function close(): void;
}
