<?php

namespace App\Domain\Governance\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class ApiKey extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'organization_id',
        'provider',
        'encrypted_key',
        'name',
        'is_active',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public static function encryptValue(string $plaintext): string
    {
        return Crypt::encryptString($plaintext);
    }

    public static function decryptValue(string $ciphertext): string
    {
        return Crypt::decryptString($ciphertext);
    }

    public function getDecryptedKey(): string
    {
        return self::decryptValue($this->encrypted_key);
    }
}
