<?php

namespace App\Domain\Governance\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SuspiciousActivityLogged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $organizationId,
        public readonly ?int $userId,
        public readonly string $riskType,
        public readonly array $details = [],
    ) {}
}
