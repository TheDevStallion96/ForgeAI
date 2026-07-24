<?php

namespace Database\Factories\Domain\KnowledgeVector\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\KnowledgeVector\Models\Document;
use App\Domain\KnowledgeVector\Models\KnowledgeBase;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'knowledge_base_id' => KnowledgeBase::factory(),
            'organization_id' => Organization::factory(),
            'name' => fake()->words(3, true).'.txt',
            'type' => 'text',
            'metadata' => ['source' => 'factory'],
        ];
    }
}
