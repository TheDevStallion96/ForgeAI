<?php

namespace Database\Factories\Domain\Marketplace\Models;

use App\Domain\Marketplace\Models\Plugin;
use Illuminate\Database\Eloquent\Factories\Factory;

class PluginFactory extends Factory
{
    protected $model = Plugin::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'slug' => fake()->unique()->slug(2),
            'description' => fake()->sentence(),
            'version' => '1.0.0',
            'author' => fake()->company(),
            'category' => fake()->randomElement(['tool', 'template', 'extension']),
            'is_official' => fake()->boolean(30),
            'is_enabled' => true,
            'tags' => fake()->randomElements(['ai', 'tools', 'devops', 'data'], 2),
        ];
    }

    public function official(): static
    {
        return $this->state(fn () => ['is_official' => true]);
    }

    public function disabled(): static
    {
        return $this->state(fn () => ['is_enabled' => false]);
    }

    public function ofCategory(string $category): static
    {
        return $this->state(fn () => ['category' => $category]);
    }
}
