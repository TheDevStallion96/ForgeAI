<?php

namespace App\Domain\ArchitectureStudio\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use Database\Factories\Domain\ArchitectureStudio\Models\ArchitectureDecisionRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchitectureDecisionRecord extends Model
{
    /** @use HasFactory<ArchitectureDecisionRecordFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'organization_id',
        'title',
        'adr_number',
        'status',
        'context',
        'decision',
        'consequences',
        'superseded_by_id',
    ];

    protected function casts(): array
    {
        return [
            'adr_number' => 'integer',
        ];
    }
}
