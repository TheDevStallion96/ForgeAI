<?php

namespace Database\Factories\Domain\SourceControl\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\AuthTenant\Models\User;
use App\Domain\SourceControl\Models\GitHubInstallation;
use Illuminate\Database\Eloquent\Factories\Factory;

class GitHubInstallationFactory extends Factory
{
    protected $model = GitHubInstallation::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'provider' => 'pat',
            'access_token' => 'ghp_fake_token',
            'github_username' => fake()->userName(),
            'scopes' => ['repo', 'read:user'],
        ];
    }
}
