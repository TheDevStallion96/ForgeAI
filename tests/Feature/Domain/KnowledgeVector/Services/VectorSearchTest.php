<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\KnowledgeVector\Models\DocumentChunk;
use App\Domain\KnowledgeVector\Models\KnowledgeBase;
use App\Domain\KnowledgeVector\Services\VectorSearch;

beforeEach(function () {
    $this->search = app(VectorSearch::class);
    $this->org = Organization::factory()->create();

    KnowledgeBase::factory()->create([
        'organization_id' => $this->org->id,
    ]);
});

it('returns empty collection when no chunks exist', function () {
    $results = $this->search->search('test query');

    expect($results)->toHaveCount(0);
});

it('finds chunks by keyword overlap in fallback mode', function () {
    $kb = KnowledgeBase::factory()->create([
        'organization_id' => $this->org->id,
    ]);

    $matching = DocumentChunk::factory()->create([
        'knowledge_base_id' => $kb->id,
        'organization_id' => $this->org->id,
        'content' => 'Laravel is a great PHP framework for building web applications.',
    ]);

    DocumentChunk::factory()->create([
        'knowledge_base_id' => $kb->id,
        'organization_id' => $this->org->id,
        'content' => 'The weather today is sunny and warm.',
    ]);

    $results = $this->search->search('PHP framework Laravel web');

    expect($results)->toHaveCount(1);
    expect($results->first()->id)->toBe($matching->id);
});

it('filters by knowledge base', function () {
    $kb1 = KnowledgeBase::factory()->create([
        'organization_id' => $this->org->id,
    ]);
    $kb2 = KnowledgeBase::factory()->create([
        'organization_id' => $this->org->id,
    ]);

    DocumentChunk::factory()->create([
        'knowledge_base_id' => $kb1->id,
        'organization_id' => $this->org->id,
        'content' => 'Laravel PHP framework',
    ]);

    DocumentChunk::factory()->create([
        'knowledge_base_id' => $kb2->id,
        'organization_id' => $this->org->id,
        'content' => 'Laravel PHP framework',
    ]);

    $results = $this->search->search('Laravel PHP framework', knowledgeBaseId: $kb1->id);

    expect($results)->toHaveCount(1);
    expect($results->first()->knowledge_base_id)->toBe($kb1->id);
});

it('respects min similarity threshold', function () {
    $kb = KnowledgeBase::factory()->create([
        'organization_id' => $this->org->id,
    ]);

    DocumentChunk::factory()->create([
        'knowledge_base_id' => $kb->id,
        'organization_id' => $this->org->id,
        'content' => 'Completely unrelated topic about astronomy.',
    ]);

    $highThresholdSearch = new VectorSearch(minSimilarity: 0.9);
    $results = $highThresholdSearch->search('PHP framework Laravel');

    expect($results)->toHaveCount(0);
});

it('limits results to topK', function () {
    $kb = KnowledgeBase::factory()->create([
        'organization_id' => $this->org->id,
    ]);

    DocumentChunk::factory()->count(10)->create([
        'knowledge_base_id' => $kb->id,
        'organization_id' => $this->org->id,
        'content' => 'Laravel PHP framework for web development.',
    ]);

    $limitedSearch = new VectorSearch(topK: 3);
    $results = $limitedSearch->search('Laravel PHP');

    expect($results)->toHaveCount(3);
});
