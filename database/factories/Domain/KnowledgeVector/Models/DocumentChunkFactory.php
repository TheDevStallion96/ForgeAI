<?php

namespace Database\Factories\Domain\KnowledgeVector\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\KnowledgeVector\Models\DocumentChunk;
use App\Domain\KnowledgeVector\Models\KnowledgeBase;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentChunkFactory extends Factory
{
    protected $model = DocumentChunk::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'knowledge_base_id' => KnowledgeBase::factory(),
            'content' => fake()->paragraphs(3, true),
            'chunk_index' => fake()->numberBetween(0, 100),
            'metadata' => ['source' => fake()->word()],
        ];
    }
}
