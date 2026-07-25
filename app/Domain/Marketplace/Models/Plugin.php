<?php

namespace App\Domain\Marketplace\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plugin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'author',
        'homepage',
        'category',
        'icon',
        'is_official',
        'is_enabled',
        'repository_url',
        'config_schema',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'is_official' => 'boolean',
            'is_enabled' => 'boolean',
            'config_schema' => 'array',
            'tags' => 'array',
        ];
    }

    public function installations(): HasMany
    {
        return $this->hasMany(PluginInstallation::class);
    }

    public function scopeOfficial($query)
    {
        return $query->where('is_official', true);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
