<?php

namespace App\Domain\Governance\Services;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\AuthTenant\Models\User;
use App\Domain\Governance\Models\AuditLog;

class AuditLogger
{
    public function log(
        string $eventType,
        array $payload,
        ?Organization $organization = null,
        ?User $user = null,
        array $metadata = [],
    ): AuditLog {
        $previousHash = AuditLog::where('organization_id', $organization?->id)
            ->latest('id')
            ->value('payload_hash');

        return AuditLog::create([
            'organization_id' => $organization?->id,
            'user_id' => $user?->id,
            'event_type' => $eventType,
            'payload' => $payload,
            'payload_hash' => AuditLog::computeHash($payload),
            'previous_hash' => $previousHash,
            'metadata' => $metadata,
        ]);
    }

    public function logCritical(
        string $eventType,
        array $payload,
        ?Organization $organization = null,
        ?User $user = null,
        array $metadata = [],
    ): AuditLog {
        return $this->log($eventType, $payload, $organization, $user, array_merge(
            $metadata,
            ['severity' => 'critical', 'logged_at' => now()->toIso8601String()],
        ));
    }

    public function verifyChain(int $organizationId): bool
    {
        $logs = AuditLog::where('organization_id', $organizationId)
            ->orderBy('id')
            ->get();

        $previousHash = null;

        foreach ($logs as $log) {
            $expectedHash = AuditLog::computeHash($log->payload);

            if ($log->payload_hash !== $expectedHash) {
                return false;
            }

            if ($log->previous_hash !== $previousHash) {
                return false;
            }

            $previousHash = $log->payload_hash;
        }

        return true;
    }
}
