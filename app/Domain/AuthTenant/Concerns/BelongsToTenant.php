<?php

namespace App\Domain\AuthTenant\Concerns;

use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
