<?php

namespace App\Domain\AIEngine\Services;

use App\Domain\AIEngine\Contracts\AIEngine;
use App\Domain\AIEngine\Data\CompletionRequest;
use App\Domain\AIEngine\Data\CompletionResponse;
use App\Domain\AIEngine\Exceptions\AllProvidersFailedException;
use Laravel\Ai\Exceptions\AiException;
use Laravel\Ai\Responses\Data\Usage;
use Laravel\Ai\Responses\StreamableAgentResponse;

use function Laravel\Ai\agent;

class AIEngineService implements AIEngine
{
    public function __construct(
        private readonly ProviderRouter $router,
    ) {}

    public function prompt(CompletionRequest $request): CompletionResponse
    {
        $provider = $this->resolveProvider($request->provider);

        try {
            $response = agent(
                instructions: $request->systemInstruction ?? 'You are a helpful assistant.',
            )->prompt(
                prompt: $request->prompt,
                provider: $provider,
                model: $request->model,
            );

            return new CompletionResponse(
                text: $response->text,
                usage: $response->usage instanceof Usage
                    ? [
                        'prompt_tokens' => $response->usage->promptTokens,
                        'completion_tokens' => $response->usage->completionTokens,
                    ]
                    : [],
            );
        } catch (AiException $e) {
            throw new AllProvidersFailedException(
                failedProviders: $this->formatProviders($request->provider),
                message: $e->getMessage(),
            );
        }
    }

    public function stream(CompletionRequest $request): StreamableAgentResponse
    {
        $provider = $this->resolveProvider($request->provider);

        return agent(
            instructions: $request->systemInstruction ?? 'You are a helpful assistant.',
        )->stream(
            prompt: $request->prompt,
            provider: $provider,
            model: $request->model,
        );
    }

    private function resolveProvider(array $providers): array
    {
        return $this->router->resolve($providers);
    }

    private function formatProviders(array $providers): array
    {
        return array_map(fn ($p) => $p instanceof \BackedEnum ? $p->value : (string) $p, $providers);
    }
}
