<?php

namespace Database\Factories\Domain\SourceControl\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\SourceControl\Models\GitRepository;
use Illuminate\Database\Eloquent\Factories\Factory;

class GitRepositoryFactory extends Factory
{
    protected $model = GitRepository::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->word().'-'.fake()->word(),
            'url' => 'https://github.com/forge-ai/'.fake()->word(),
            'provider' => 'github',
            'default_branch' => 'main',
            'status' => 'active',
        ];
    }
}
