<?php

namespace Database\Factories\Domain\Workspace\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Workspace\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkspaceFactory extends Factory
{
    protected $model = Workspace::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(2),
            'description' => fake()->sentence(),
            'status' => 'active',
        ];
    }
}
