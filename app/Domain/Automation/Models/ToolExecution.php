<?php

namespace App\Domain\Automation\Models;

use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Automation\Contracts\ToolResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolExecution extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'organization_id',
        'session_id',
        'agent_id',
        'tool_name',
        'parameters',
        'result',
        'status',
        'requires_hitl',
        'approved_at',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
            'result' => 'array',
            'requires_hitl' => 'boolean',
            'approved_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExecutionSession::class, 'session_id');
    }

    public function markStarted(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    public function markCompleted(ToolResult $result): void
    {
        $this->update([
            'status' => $result->isSuccess() ? 'completed' : 'failed',
            'result' => $result->toArray(),
            'completed_at' => now(),
        ]);
    }

    public function markApproved(): void
    {
        $this->update([
            'approved_at' => now(),
        ]);
    }
}
