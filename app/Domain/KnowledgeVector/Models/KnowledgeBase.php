<?php

namespace App\Domain\KnowledgeVector\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeBase extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'embedding_model',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(DocumentChunk::class);
    }
}
