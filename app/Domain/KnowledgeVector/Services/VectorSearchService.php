<?php

namespace App\Domain\KnowledgeVector\Services;

use App\Domain\KnowledgeVector\Models\DocumentChunk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VectorSearchService
{
    public function search(int $organizationId, array $queryEmbedding, int $limit = 10, float $minSimilarity = 0.7): Collection
    {
        $embeddingString = '['.implode(',', $queryEmbedding).']';

        $rows = DB::select('
            SELECT
                dc.id,
                dc.content,
                dc.knowledge_base_id,
                dc.chunk_index,
                dc.metadata,
                1 - (dc.embedding <=> ?::vector) AS similarity
            FROM document_chunks dc
            WHERE dc.organization_id = ?
              AND dc.embedding IS NOT NULL
              AND 1 - (dc.embedding <=> ?::vector) >= ?
            ORDER BY dc.embedding <=> ?::vector
            LIMIT ?
        ', [$embeddingString, $organizationId, $embeddingString, $minSimilarity, $embeddingString, $limit]);

        return collect($rows);
    }

    public function storeEmbedding(DocumentChunk $chunk, array $embedding): void
    {
        $embeddingString = '['.implode(',', $embedding).']';

        DB::statement('UPDATE document_chunks SET embedding = ?::vector WHERE id = ?', [$embeddingString, $chunk->id]);
    }
}
