<?php

namespace App\Domain\Automation\Tools;

use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Contracts\ToolResult;
use App\Domain\KnowledgeVector\Services\VectorSearch;

class VectorSearchTool implements ToolInterface
{
    public function __construct(
        protected VectorSearch $vectorSearch,
    ) {}

    public function name(): string
    {
        return 'vector_search';
    }

    public function description(): string
    {
        return 'Search knowledge base with semantic similarity';
    }

    public function parameterSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => [
                    'type' => 'string',
                    'description' => 'Search query',
                ],
                'knowledge_base_id' => [
                    'type' => 'integer',
                    'description' => 'Optional knowledge base ID to filter by',
                ],
            ],
            'required' => ['query'],
        ];
    }

    public function requiresHumanApproval(): bool
    {
        return false;
    }

    public function execute(array $parameters): ToolResult
    {
        $query = $parameters['query'] ?? '';
        $kbId = $parameters['knowledge_base_id'] ?? null;

        try {
            $results = $this->vectorSearch->search($query, $kbId);

            return ToolResult::success($results->toArray());
        } catch (\Throwable $e) {
            return ToolResult::failure($e->getMessage());
        }
    }
}
