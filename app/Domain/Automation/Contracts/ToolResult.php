<?php

namespace App\Domain\Automation\Contracts;

class ToolResult
{
    public function __construct(
        public readonly bool $success,
        public readonly mixed $output,
        public readonly ?string $error = null,
        public readonly ?int $exitCode = null,
    ) {}

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'output' => $this->output,
            'error' => $this->error,
            'exit_code' => $this->exitCode,
        ];
    }

    public static function success(mixed $output): self
    {
        return new self(true, $output);
    }

    public static function failure(string $error, ?int $exitCode = null): self
    {
        return new self(false, null, $error, $exitCode);
    }
}
