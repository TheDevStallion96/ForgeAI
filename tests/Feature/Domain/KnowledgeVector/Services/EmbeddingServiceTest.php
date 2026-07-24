<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\KnowledgeVector\Models\DocumentChunk;
use App\Domain\KnowledgeVector\Services\EmbeddingService;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Prompts\EmbeddingsPrompt;

beforeEach(function () {
    $this->service = app(EmbeddingService::class);
    $this->org = Organization::factory()->create();
});

it('generates an embedding for a single string', function () {
    Embeddings::fake();

    $embedding = $this->service->generateEmbedding('Napa Valley has great wine.');

    expect($embedding)->toBeArray();
});

it('generates embeddings for multiple strings', function () {
    Embeddings::fake([
        [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6]],
    ]);

    $embeddings = $this->service->generateEmbeddings([
        'First document content.',
        'Second document content.',
    ]);

    expect($embeddings)->toHaveCount(2);
    expect($embeddings[0])->toBe([0.1, 0.2, 0.3]);
    expect($embeddings[1])->toBe([0.4, 0.5, 0.6]);
});

it('embeds a document chunk', function () {
    Embeddings::fake();

    $chunk = DocumentChunk::factory()->create([
        'organization_id' => $this->org->id,
        'embedding' => null,
    ]);

    $result = $this->service->embedChunk($chunk);

    expect($result->embedding)->toBeArray();
});

it('embeds multiple chunks', function () {
    Embeddings::fake([
        [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6]],
    ]);

    $chunks = DocumentChunk::factory()->count(2)->create([
        'organization_id' => $this->org->id,
        'embedding' => null,
    ]);

    $results = $this->service->embedChunks($chunks->all());

    expect($results)->toHaveCount(2);
    expect($results[0]->fresh()->embedding)->toBe([0.1, 0.2, 0.3]);
    expect($results[1]->fresh()->embedding)->toBe([0.4, 0.5, 0.6]);
});

it('asserts embeddings were generated', function () {
    Embeddings::fake();

    $this->service->generateEmbedding('Test content.');

    Embeddings::assertGenerated(function (EmbeddingsPrompt $prompt) {
        return $prompt->contains('Test content.');
    });
});
