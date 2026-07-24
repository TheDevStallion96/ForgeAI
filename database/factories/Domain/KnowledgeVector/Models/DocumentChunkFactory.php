<?php

namespace Database\Factories\Domain\KnowledgeVector\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\KnowledgeVector\Models\Document;
use App\Domain\KnowledgeVector\Models\DocumentChunk;
use App\Domain\KnowledgeVector\Models\KnowledgeBase;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentChunkFactory extends Factory
{
    protected $model = DocumentChunk::class;

    public function definition(): array
    {
        return [
            'knowledge_base_id' => KnowledgeBase::factory(),
            'document_id' => Document::factory(),
            'organization_id' => Organization::factory(),
            'chunk_index' => fake()->numberBetween(0, 10),
            'content' => fake()->paragraphs(3, true),
            'token_count' => fake()->numberBetween(100, 500),
            'embedding' => array_map(fn () => fake()->randomFloat(8, -0.1, 0.1), range(1, 4)),
            'metadata' => ['chunk_strategy' => '512_overlap_64'],
        ];
    }
}
