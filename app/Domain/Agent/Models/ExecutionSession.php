<?php

namespace App\Domain\Agent\Models;

use App\Domain\Agent\Enums\SessionStatus;
use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use App\Domain\AuthTenant\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExecutionSession extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'agent_id',
        'user_id',
        'status',
        'context_tokens_accumulated',
    ];

    protected function casts(): array
    {
        return [
            'status' => SessionStatus::class,
            'context_tokens_accumulated' => 'integer',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SessionMessage::class, 'session_id');
    }
}
