<?php

namespace App\Domain\AIEngine\Contracts;

use App\Domain\AIEngine\Data\CompletionRequest;
use App\Domain\AIEngine\Data\CompletionResponse;
use Laravel\Ai\Responses\StreamableAgentResponse;

interface AIEngine
{
    public function prompt(CompletionRequest $request): CompletionResponse;

    public function stream(CompletionRequest $request): StreamableAgentResponse;
}
