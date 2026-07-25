<?php

namespace Database\Factories\Domain\KnowledgeVector\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\KnowledgeVector\Models\KnowledgeAsset;
use Illuminate\Database\Eloquent\Factories\Factory;

class KnowledgeAssetFactory extends Factory
{
    protected $model = KnowledgeAsset::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'provider_file_id' => 'file_'.fake()->lexify('??????'),
            'provider_store_id' => 'vs_'.fake()->lexify('??????'),
            'provider' => 'openai',
            'name' => fake()->word().'.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(1024, 1048576),
            'status' => 'ready',
        ];
    }
}
