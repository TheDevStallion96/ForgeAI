<?php

namespace App\Domain\EngineeringGraph\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraphEdge extends Model
{
    use BelongsToTenant, HasFactory;

    protected $table = 'graph_edges';

    protected $fillable = [
        'organization_id',
        'source_node_id',
        'target_node_id',
        'relationship_type',
        'weight',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'properties' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function sourceNode(): BelongsTo
    {
        return $this->belongsTo(GraphNode::class, 'source_node_id');
    }

    public function targetNode(): BelongsTo
    {
        return $this->belongsTo(GraphNode::class, 'target_node_id');
    }
}
