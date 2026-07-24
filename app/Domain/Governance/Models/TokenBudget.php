<?php

namespace App\Domain\Governance\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use App\Domain\AuthTenant\Models\Organization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenBudget extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'organization_id',
        'monthly_limit',
        'current_usage',
        'reset_at',
    ];

    protected function casts(): array
    {
        return [
            'monthly_limit' => 'integer',
            'current_usage' => 'integer',
            'reset_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function usagePercentage(): float
    {
        if ($this->monthly_limit === 0) {
            return 0.0;
        }

        return $this->current_usage / $this->monthly_limit;
    }

    public function isExhausted(): bool
    {
        return $this->current_usage >= $this->monthly_limit;
    }

    public function deduct(int $tokens): void
    {
        $this->increment('current_usage', $tokens);
    }

    public function needsReset(): bool
    {
        return $this->reset_at && Carbon::now()->gte($this->reset_at);
    }
}
