<?php

namespace App\Domain\KnowledgeVector\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentChunk extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'organization_id',
        'knowledge_base_id',
        'content',
        'embedding',
        'chunk_index',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'embedding' => 'array',
            'metadata' => 'array',
            'chunk_index' => 'integer',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBase::class);
    }
}
