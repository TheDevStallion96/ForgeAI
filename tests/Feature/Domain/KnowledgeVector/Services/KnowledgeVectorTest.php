<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\KnowledgeVector\Models\DocumentChunk;
use App\Domain\KnowledgeVector\Models\KnowledgeBase;
use App\Domain\KnowledgeVector\Services\DocumentChunker;
use App\Domain\KnowledgeVector\Services\VectorSearchService;

beforeEach(function () {
    $this->chunker = app(DocumentChunker::class);
    $this->searchService = app(VectorSearchService::class);
    $this->organization = Organization::factory()->create();
    $this->knowledgeBase = KnowledgeBase::factory()->create([
        'organization_id' => $this->organization->id,
    ]);
});

it('can chunk a document into smaller pieces', function () {
    $document = str_repeat('Lorem ipsum dolor sit amet. ', 500);

    $chunks = $this->chunker->chunk($this->knowledgeBase, $document);

    expect($chunks)->toBeArray()
        ->and(count($chunks))->toBeGreaterThan(1);

    foreach ($chunks as $chunk) {
        expect($chunk)->toHaveKeys(['content', 'chunk_index', 'metadata', 'knowledge_base_id']);
        expect($chunk['knowledge_base_id'])->toBe($this->knowledgeBase->id);
    }
});

it('assigns sequential chunk indices', function () {
    $document = str_repeat('Hello world. ', 500);

    $chunks = $this->chunker->chunk($this->knowledgeBase, $document);

    foreach ($chunks as $i => $chunk) {
        expect($chunk['chunk_index'])->toBe($i);
    }
});

it('attaches metadata to chunks', function () {
    $document = 'Test document content.';
    $metadata = ['source' => 'README.md', 'author' => 'test'];

    $chunks = $this->chunker->chunk($this->knowledgeBase, $document, $metadata);

    foreach ($chunks as $chunk) {
        expect($chunk['metadata']['source'])->toBe('README.md');
    }
});

it('can create document chunks via the model', function () {
    $chunk = DocumentChunk::factory()->create([
        'organization_id' => $this->organization->id,
        'knowledge_base_id' => $this->knowledgeBase->id,
    ]);

    expect($chunk)->toBeInstanceOf(DocumentChunk::class)
        ->and($chunk->knowledge_base_id)->toBe($this->knowledgeBase->id);
});

it('stores embedding for a document chunk', function () {
    $this->markTestSkipped('Requires PostgreSQL with pgvector extension');
});
