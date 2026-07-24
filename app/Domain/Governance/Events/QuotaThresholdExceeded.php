<?php

namespace App\Domain\Governance\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuotaThresholdExceeded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $organizationId,
        public readonly float $percentageUsed,
        public readonly int $monthlyLimit,
    ) {}
}
