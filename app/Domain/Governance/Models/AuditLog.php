<?php

namespace App\Domain\Governance\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use BelongsToTenant, HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'organization_id',
        'user_id',
        'event_type',
        'payload',
        'payload_hash',
        'previous_hash',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'metadata' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public static function computeHash(array $payload): string
    {
        return hash('sha256', json_encode($payload));
    }

    public function isTampered(): bool
    {
        return self::computeHash($this->payload) !== $this->payload_hash;
    }
}
