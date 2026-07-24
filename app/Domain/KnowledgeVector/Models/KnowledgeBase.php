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

    protected function casts(): array
    {
        return [
            'embedding_model' => 'string',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
