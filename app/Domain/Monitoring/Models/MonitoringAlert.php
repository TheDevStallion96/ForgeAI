<?php

namespace App\Domain\Monitoring\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use Database\Factories\Domain\Monitoring\Models\MonitoringAlertFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringAlert extends Model
{
    /** @use HasFactory<MonitoringAlertFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'organization_id',
        'severity',
        'message',
        'acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'acknowledged_at' => 'datetime',
        ];
    }
}
