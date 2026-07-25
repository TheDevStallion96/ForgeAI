<?php

namespace App\Domain\McpGateway\Services;

use App\Domain\McpGateway\Contracts\McpTransportInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class McpSseTransport implements McpTransportInterface
{
    private bool $connected = false;

    private array $responseBuffer = [];

    private string $sessionId = '';

    private const SSE_ENDPOINT = '/sse';

    private const MESSAGE_ENDPOINT = '/message';

    public function __construct(
        private readonly ?string $serverUrl = null,
    ) {}

    public function send(array $message): void
    {
        $this->ensureConnected();

        $url = rtrim($this->serverUrl, '/').self::MESSAGE_ENDPOINT;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($url, $message);

        if ($response->failed()) {
            throw new RuntimeException("MCP SSE send failed: {$response->status()}");
        }

        $body = $response->json();
        if ($body !== null) {
            $this->responseBuffer[] = $body;
        }
    }

    public function receive(): ?array
    {
        if (! empty($this->responseBuffer)) {
            return array_shift($this->responseBuffer);
        }

        return null;
    }

    public function isConnected(): bool
    {
        return $this->connected;
    }

    public function close(): void
    {
        $this->connected = false;
        $this->responseBuffer = [];
    }

    private function ensureConnected(): void
    {
        if ($this->connected) {
            return;
        }

        if ($this->serverUrl === null) {
            throw new RuntimeException('No server URL configured for MCP SSE transport');
        }

        $this->connected = true;
    }
}
