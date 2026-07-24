<?php

namespace App\Domain\AIEngine\Data;

class CompletionRequest
{
    public function __construct(
        public readonly string $prompt,
        public readonly ?string $systemInstruction = null,
        public readonly array $provider = [],
        public readonly ?string $model = null,
    ) {}
}
