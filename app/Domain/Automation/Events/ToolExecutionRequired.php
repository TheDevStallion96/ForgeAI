<?php

namespace App\Domain\Automation\Events;

use App\Domain\Automation\Models\ToolExecution;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ToolExecutionRequired
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ToolExecution $execution,
        public readonly array $parameters,
    ) {}
}
