<?php

namespace App\Domain\SourceControl\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use App\Domain\AuthTenant\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GitHubInstallation extends Model
{
    use BelongsToTenant, HasFactory;

    protected $table = 'github_installations';

    protected $fillable = [
        'organization_id',
        'user_id',
        'provider',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'github_username',
        'scopes',
    ];

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'token_expires_at' => 'datetime',
            'scopes' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
