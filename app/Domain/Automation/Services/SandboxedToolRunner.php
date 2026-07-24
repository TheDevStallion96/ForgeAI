<?php

namespace App\Domain\Automation\Services;

use App\Domain\Automation\Contracts\ToolResult;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SandboxedToolRunner
{
    public function __construct(
        protected int $memoryLimitMb = 128,
        protected int $timeoutSeconds = 10,
    ) {}

    public function run(
        string|array $command,
        array $env = [],
        ?string $cwd = null,
    ): ToolResult {
        $startTime = microtime(true);

        $processCommand = is_string($command)
            ? explode(' ', $command)
            : $command;

        $process = new Process($processCommand, $cwd, $env, null, $this->timeoutSeconds);
        $process->setTimeout($this->timeoutSeconds);
        $process->setIdleTimeout($this->timeoutSeconds);

        try {
            $process->run();

            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            if ($process->isSuccessful()) {
                return ToolResult::success($process->getOutput(), $durationMs);
            }

            return ToolResult::failure(
                $process->getErrorOutput(),
                $durationMs,
                $process->getExitCode(),
            );
        } catch (ProcessFailedException $e) {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            return ToolResult::failure($e->getMessage(), $durationMs, $e->getExitCode());
        } catch (\Throwable $e) {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            return ToolResult::failure($e->getMessage(), $durationMs);
        }
    }

    public function runAsync(
        string|array $command,
        array $env = [],
        ?string $cwd = null,
        ?callable $callback = null,
    ): void {
        $processCommand = is_string($command)
            ? explode(' ', $command)
            : $command;

        $process = new Process($processCommand, $cwd, $env, null, $this->timeoutSeconds);
        $process->setTimeout($this->timeoutSeconds);

        $process->start();

        while ($process->isRunning()) {
            usleep(100000);
        }

        $durationMs = (int) ((microtime(true) - microtime(true)) * 1000);

        $result = $process->isSuccessful()
            ? ToolResult::success($process->getOutput(), $durationMs)
            : ToolResult::failure($process->getErrorOutput(), $durationMs, $process->getExitCode());

        if ($callback) {
            $callback($result);
        }
    }
}
