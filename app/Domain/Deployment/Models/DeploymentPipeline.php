<?php

namespace App\Domain\Deployment\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use Database\Factories\Domain\Deployment\Models\DeploymentPipelineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeploymentPipeline extends Model
{
    /** @use HasFactory<DeploymentPipelineFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'environment',
        'status',
        'version',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}
