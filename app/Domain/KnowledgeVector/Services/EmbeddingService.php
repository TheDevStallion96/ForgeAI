<?php

namespace App\Domain\KnowledgeVector\Services;

use App\Domain\KnowledgeVector\Models\DocumentChunk;
use Illuminate\Support\Str;
use Laravel\Ai\Embeddings;

class EmbeddingService
{
    public function __construct(
        private readonly string $model = 'text-embedding-3-small',
        private readonly int $dimensions = 1536,
    ) {}

    public function generateEmbedding(string $content): array
    {
        return Str::of($content)->toEmbeddings(cache: true);
    }

    public function generateEmbeddings(array $contents): array
    {
        $response = Embeddings::for($contents)
            ->dimensions($this->dimensions)
            ->cache()
            ->generate();

        return $response->embeddings;
    }

    public function embedChunk(DocumentChunk $chunk): DocumentChunk
    {
        $embedding = $this->generateEmbedding($chunk->content);
        $chunk->update(['embedding' => $embedding]);

        return $chunk->fresh();
    }

    public function embedChunks(array $chunks): array
    {
        $contents = array_map(fn (DocumentChunk $chunk) => $chunk->content, $chunks);
        $embeddings = $this->generateEmbeddings($contents);

        foreach ($chunks as $i => $chunk) {
            $chunk->update(['embedding' => $embeddings[$i]]);
        }

        return $chunks;
    }
}
