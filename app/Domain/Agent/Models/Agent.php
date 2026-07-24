<?php

namespace App\Domain\Agent\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'primary_model',
        'fallback_model',
        'system_instruction',
        'temperature',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'temperature' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function executionSessions(): HasMany
    {
        return $this->hasMany(ExecutionSession::class);
    }
}
