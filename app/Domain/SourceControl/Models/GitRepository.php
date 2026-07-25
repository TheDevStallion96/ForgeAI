<?php

namespace App\Domain\SourceControl\Models;

use App\Domain\AuthTenant\Concerns\BelongsToTenant;
use Database\Factories\Domain\SourceControl\Models\GitRepositoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitRepository extends Model
{
    /** @use HasFactory<GitRepositoryFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'organization_id',
        'github_id',
        'name',
        'url',
        'description',
        'is_private',
        'language',
        'provider',
        'default_branch',
        'clone_url',
        'ssh_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
        ];
    }
}
