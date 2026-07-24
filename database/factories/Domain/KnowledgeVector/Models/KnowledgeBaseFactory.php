<?php

namespace Database\Factories\Domain\KnowledgeVector\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\KnowledgeVector\Models\KnowledgeBase;
use Illuminate\Database\Eloquent\Factories\Factory;

class KnowledgeBaseFactory extends Factory
{
    protected $model = KnowledgeBase::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->words(2, true),
            'embedding_model' => 'text-embedding-3-small',
        ];
    }
}
