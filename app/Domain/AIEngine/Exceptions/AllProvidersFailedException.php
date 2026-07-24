<?php

namespace App\Domain\AIEngine\Exceptions;

class AllProvidersFailedException extends AIEngineException
{
    public function __construct(
        public readonly array $failedProviders = [],
        ?string $message = null,
    ) {
        parent::__construct(
            $message ?? sprintf(
                'All AI providers failed: %s',
                implode(', ', $failedProviders),
            ),
        );
    }
}
