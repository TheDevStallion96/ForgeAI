<?php

namespace Database\Factories\Domain\Governance\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Governance\Models\ApiKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiKeyFactory extends Factory
{
    protected $model = ApiKey::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'provider' => 'openai',
            'encrypted_key' => ApiKey::encryptValue('sk-'.fake()->sha256()),
            'name' => 'Default OpenAI Key',
            'is_active' => true,
        ];
    }

    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
