<?php

namespace App\Domain\Automation\Contracts;

interface ToolInterface
{
    public function name(): string;

    public function description(): string;

    public function parameterSchema(): array;

    public function requiresHumanApproval(): bool;

    public function execute(array $parameters): ToolResult;
}
