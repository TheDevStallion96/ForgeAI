<?php

namespace App\Domain\Governance\Services;

use App\Domain\Governance\Models\ApiKey;

class SecretsManager
{
    public function store(
        int $organizationId,
        string $provider,
        string $key,
        ?string $name = null,
    ): ApiKey {
        return ApiKey::create([
            'organization_id' => $organizationId,
            'provider' => $provider,
            'encrypted_key' => ApiKey::encryptValue($key),
            'name' => $name ?? "{$provider}-key",
            'is_active' => true,
        ]);
    }

    public function retrieve(int $organizationId, string $provider): ?string
    {
        $apiKey = ApiKey::where('organization_id', $organizationId)
            ->where('provider', $provider)
            ->where('is_active', true)
            ->latest()
            ->first();

        if (! $apiKey) {
            return null;
        }

        $apiKey->update(['last_used_at' => now()]);

        return $apiKey->getDecryptedKey();
    }

    public function revoke(int $apiKeyId): bool
    {
        return (bool) ApiKey::where('id', $apiKeyId)->update(['is_active' => false]);
    }

    public function list(int $organizationId): array
    {
        return ApiKey::where('organization_id', $organizationId)
            ->get()
            ->map(fn (ApiKey $key) => [
                'id' => $key->id,
                'provider' => $key->provider,
                'name' => $key->name,
                'is_active' => $key->is_active,
                'last_used_at' => $key->last_used_at,
                'key_preview' => str_repeat('*', 8).substr($key->getDecryptedKey(), -4),
            ])
            ->toArray();
    }
}
