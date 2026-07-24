<?php

namespace App\Domain\KnowledgeVector\Services;

class TextChunker
{
    public function __construct(
        private readonly int $chunkSize = 512,
        private readonly int $overlap = 64,
    ) {}

    public function chunk(string $text): array
    {
        $words = str_word_count($text, 1);
        $totalWords = count($words);

        if ($totalWords === 0) {
            return [];
        }

        $targetWords = (int) round($this->chunkSize * 0.75);
        $overlapWords = (int) round($this->overlap * 0.75);
        $chunks = [];
        $offset = 0;

        while ($offset < $totalWords) {
            $slice = array_slice($words, $offset, $targetWords);
            $chunkText = implode(' ', $slice);

            if (trim($chunkText) === '') {
                break;
            }

            $chunks[] = $chunkText;
            $offset += $targetWords - $overlapWords;
        }

        return $chunks;
    }

    public function chunkWithMetadata(string $text): array
    {
        $chunks = $this->chunk($text);

        return array_map(fn (string $content, int $index) => [
            'content' => $content,
            'chunk_index' => $index,
            'token_count' => $this->countTokens($content),
        ], $chunks, array_keys($chunks));
    }

    public function countTokens(string $text): int
    {
        return (int) ceil(str_word_count($text) / 0.75);
    }
}
