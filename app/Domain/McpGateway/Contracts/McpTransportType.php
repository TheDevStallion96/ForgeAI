<?php

namespace App\Domain\McpGateway\Contracts;

enum McpTransportType: string
{
    case Stdio = 'stdio';
    case Sse = 'sse';
}
