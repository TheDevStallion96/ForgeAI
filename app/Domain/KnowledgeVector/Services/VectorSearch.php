<?php

namespace App\Domain\KnowledgeVector\Services;

use App\Domain\KnowledgeVector\Models\DocumentChunk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class VectorSearch
{
    public function __construct(
        private readonly int $topK = 5,
        private readonly float $minSimilarity = 0.4,
    ) {}

    public function search(string $query, ?int $knowledgeBaseId = null): Collection
    {
        $driver = Config::get('database.default');

        if ($driver === 'pgsql') {
            return $this->searchPostgres($query, $knowledgeBaseId);
        }

        return $this->searchFallback($query, $knowledgeBaseId);
    }

    private function searchPostgres(string $query, ?int $knowledgeBaseId): Collection
    {
        $base = DocumentChunk::query();

        if ($knowledgeBaseId !== null) {
            $base->where('knowledge_base_id', $knowledgeBaseId);
        }

        return $base
            ->whereVectorSimilarTo('embedding', $query, minSimilarity: $this->minSimilarity)
            ->limit($this->topK)
            ->get();
    }

    private function searchFallback(string $query, ?int $knowledgeBaseId): Collection
    {
        $queryTokens = $this->tokenize($query);

        $base = DocumentChunk::query();

        if ($knowledgeBaseId !== null) {
            $base->where('knowledge_base_id', $knowledgeBaseId);
        }

        $chunks = $base->get();

        $scored = $chunks->map(function (DocumentChunk $chunk) use ($queryTokens) {
            $chunkTokens = $this->tokenize($chunk->content);
            $intersection = array_intersect($queryTokens, $chunkTokens);
            $similarity = count($queryTokens) > 0
                ? count($intersection) / count($queryTokens)
                : 0;

            return ['chunk' => $chunk, 'score' => $similarity];
        });

        return $scored
            ->filter(fn (array $item) => $item['score'] >= $this->minSimilarity)
            ->sortByDesc('score')
            ->take($this->topK)
            ->map(fn (array $item) => $item['chunk'])
            ->values();
    }

    private function tokenize(string $text): array
    {
        return array_unique(array_filter(
            str_word_count(mb_strtolower($text), 1),
            fn (string $word) => mb_strlen($word) > 2,
        ));
    }
}
