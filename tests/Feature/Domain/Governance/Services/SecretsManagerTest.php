<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Governance\Models\ApiKey;
use App\Domain\Governance\Services\SecretsManager;

beforeEach(function () {
    $this->manager = app(SecretsManager::class);
    $this->org = Organization::factory()->create();
});

it('stores encrypted keys', function () {
    $key = $this->manager->store($this->org->id, 'openai', 'sk-test-key-12345');

    expect($key)->toBeInstanceOf(ApiKey::class);
    expect($key->encrypted_key)->not->toBe('sk-test-key-12345');
    expect($key->is_active)->toBeTrue();
});

it('retrieves decrypted keys', function () {
    $this->manager->store($this->org->id, 'openai', 'sk-test-key-12345');

    $retrieved = $this->manager->retrieve($this->org->id, 'openai');

    expect($retrieved)->toBe('sk-test-key-12345');
});

it('returns null for unknown keys', function () {
    $retrieved = $this->manager->retrieve($this->org->id, 'unknown');

    expect($retrieved)->toBeNull();
});

it('revokes keys', function () {
    $key = $this->manager->store($this->org->id, 'openai', 'sk-test-key');

    $this->manager->revoke($key->id);

    expect($key->fresh()->is_active)->toBeFalse();
});

it('revoked keys are not returned', function () {
    $this->manager->store($this->org->id, 'openai', 'sk-revoked');

    ApiKey::where('provider', 'openai')->where('organization_id', $this->org->id)->update(['is_active' => false]);

    $retrieved = $this->manager->retrieve($this->org->id, 'openai');

    expect($retrieved)->toBeNull();
});

it('lists keys with masked preview', function () {
    $this->manager->store($this->org->id, 'openai', 'sk-test-key-abcdef');

    $keys = $this->manager->list($this->org->id);

    expect($keys)->toHaveCount(1);
    expect($keys[0]['provider'])->toBe('openai');
    expect($keys[0]['key_preview'])->toContain('cdef');
});
