<?php

use App\Domain\AIEngine\Data\CompletionRequest;
use App\Domain\AIEngine\Enums\AIProvider;
use App\Domain\AIEngine\Exceptions\AllProvidersFailedException;
use App\Domain\AIEngine\Services\AIEngineService;
use App\Domain\AIEngine\Services\ProviderRouter;
use Laravel\Ai\AnonymousAgent;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Responses\StreamableAgentResponse;

beforeEach(function () {
    $this->service = app(AIEngineService::class);
});

it('returns a completion response with text', function () {
    AnonymousAgent::fake(['Hello from the AI!']);

    $response = $this->service->prompt(new CompletionRequest(
        prompt: 'Say hello',
        provider: [AIProvider::OpenAI],
        model: 'gpt-4o-mini',
    ));

    expect($response->text)->toBe('Hello from the AI!');
});

it('accepts a system instruction', function () {
    AnonymousAgent::fake(['I am a scientist.']);

    $response = $this->service->prompt(new CompletionRequest(
        prompt: 'Who are you?',
        systemInstruction: 'You are a helpful scientist.',
        provider: [AIProvider::OpenAI],
        model: 'gpt-4o-mini',
    ));

    expect($response->text)->toBe('I am a scientist.');
});

it('resolves a single provider', function () {
    AnonymousAgent::fake(['Done']);

    $response = $this->service->prompt(new CompletionRequest(
        prompt: 'Test',
        provider: [AIProvider::OpenAI],
    ));

    expect($response->text)->toBe('Done');
});

it('resolves a provider cascade', function () {
    AnonymousAgent::fake(['Fallback worked']);

    $response = $this->service->prompt(new CompletionRequest(
        prompt: 'Test cascade',
        provider: [AIProvider::OpenAI, AIProvider::Anthropic],
    ));

    expect($response->text)->toBe('Fallback worked');
});

it('can stream a response', function () {
    AnonymousAgent::fake(['Streamed response']);

    $stream = $this->service->stream(new CompletionRequest(
        prompt: 'Stream test',
        provider: [AIProvider::OpenAI],
        model: 'gpt-4o-mini',
    ));

    expect($stream)->toBeInstanceOf(StreamableAgentResponse::class);
});

it('resolves provider cascade via ProviderRouter', function () {
    $router = app(ProviderRouter::class);

    $resolved = $router->resolve([AIProvider::OpenAI, AIProvider::Anthropic]);

    expect($resolved)->toHaveCount(2);
    expect($resolved[0])->toBe(Lab::OpenAI);
    expect($resolved[1])->toBe(Lab::Anthropic);
});

it('constructs AllProvidersFailedException with failed provider names', function () {
    $exception = new AllProvidersFailedException(
        failedProviders: ['openai', 'anthropic'],
    );

    expect($exception->failedProviders)->toBe(['openai', 'anthropic']);
    expect($exception->getMessage())->toContain('openai');
    expect($exception->getMessage())->toContain('anthropic');
});
