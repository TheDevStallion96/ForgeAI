<?php

namespace App\Domain\KnowledgeVector\Services;

use App\Domain\KnowledgeVector\Models\KnowledgeBase;

class DocumentChunker
{
    public function chunk(KnowledgeBase $knowledgeBase, string $document, array $metadata = [], int $chunkSize = 1000, int $overlap = 200): array
    {
        $chunks = [];
        $length = mb_strlen($document);
        $index = 0;

        for ($offset = 0; $offset < $length; $offset += $chunkSize - $overlap) {
            $content = mb_substr($document, $offset, $chunkSize);

            if (mb_strlen($content) < 50 && $index > 0) {
                $chunks[$index - 1]['content'] .= "\n".$content;
                break;
            }

            $chunks[] = [
                'knowledge_base_id' => $knowledgeBase->id,
                'organization_id' => $knowledgeBase->organization_id,
                'content' => $content,
                'chunk_index' => $index,
                'metadata' => array_merge($metadata, ['chunk_offset' => $offset]),
            ];

            $index++;
        }

        return $chunks;
    }
}
