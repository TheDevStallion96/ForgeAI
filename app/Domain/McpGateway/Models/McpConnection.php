<?php

namespace App\Domain\McpGateway\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use App\Domain\AuthTenant\Models\Organization;
use App\Domain\McpGateway\Contracts\McpTransportType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class McpConnection extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'transport_type',
        'command',
        'server_url',
        'auth_token',
        'enabled_tools',
        'is_enabled',
        'configuration',
    ];

    protected function casts(): array
    {
        return [
            'transport_type' => McpTransportType::class,
            'enabled_tools' => 'array',
            'is_enabled' => 'boolean',
            'configuration' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
