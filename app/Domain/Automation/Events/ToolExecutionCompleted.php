<?php

namespace App\Domain\Automation\Events;

use App\Domain\Automation\Contracts\ToolResult;
use App\Domain\Automation\Models\ToolExecution;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ToolExecutionCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ToolExecution $execution,
        public readonly ToolResult $result,
    ) {}
}
