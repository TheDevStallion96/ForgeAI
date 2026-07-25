<?php

namespace App\Domain\KnowledgeVector\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use Database\Factories\Domain\KnowledgeVector\Models\KnowledgeAssetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeAsset extends Model
{
    /** @use HasFactory<KnowledgeAssetFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'organization_id',
        'provider_file_id',
        'provider_store_id',
        'provider',
        'name',
        'mime_type',
        'file_size',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }
}
