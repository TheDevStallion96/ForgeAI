<?php

namespace App\Domain\AIEngine\Data;

class CompletionResponse
{
    public function __construct(
        public readonly string $text,
        public readonly array $usage = [],
    ) {}
}
