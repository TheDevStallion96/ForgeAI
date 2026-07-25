<?php

namespace App\Domain\McpGateway\Services;

use App\Domain\McpGateway\Contracts\McpTransportInterface;
use JsonException;
use RuntimeException;

class McpStdioTransport implements McpTransportInterface
{
    private $process = null;

    private array $pipes = [];

    public function __construct(
        private readonly ?string $command = null,
        private readonly ?array $env = null,
    ) {}

    public function send(array $message): void
    {
        $this->ensureProcess();

        $encoded = json_encode($message, JSON_THROW_ON_ERROR)."\n";

        $written = @fwrite($this->pipes[0], $encoded);
        if ($written === false) {
            throw new RuntimeException('Failed to write to MCP process stdin');
        }

        fflush($this->pipes[0]);
    }

    public function receive(): ?array
    {
        $this->ensureProcess();

        $line = fgets($this->pipes[1]);

        if ($line === false || $line === '') {
            return null;
        }

        $line = trim($line);
        if (empty($line)) {
            return null;
        }

        try {
            return json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }
    }

    public function isConnected(): bool
    {
        return $this->process !== null && $this->process !== false;
    }

    public function close(): void
    {
        if (is_resource($this->pipes[0] ?? null)) {
            @fclose($this->pipes[0]);
        }
        if (is_resource($this->pipes[1] ?? null)) {
            @fclose($this->pipes[1]);
        }
        if (is_resource($this->process)) {
            @proc_close($this->process);
        }
        $this->process = null;
        $this->pipes = [];
    }

    private function ensureProcess(): void
    {
        if ($this->isConnected()) {
            return;
        }

        if ($this->command === null) {
            throw new RuntimeException('No command configured for MCP stdio transport');
        }

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $this->process = @proc_open($this->command, $descriptorSpec, $this->pipes, null, $this->env);

        if (! is_resource($this->process)) {
            throw new RuntimeException("Failed to start MCP process: {$this->command}");
        }

        stream_set_blocking($this->pipes[0], false);
        stream_set_blocking($this->pipes[1], false);
    }
}
