<?php

namespace App\Providers;

use App\Domain\SourceControl\Contracts\GitHubAuthInterface;
use App\Domain\SourceControl\Services\GitHubAuthService;
use Illuminate\Support\ServiceProvider;

class SourceControlServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GitHubAuthInterface::class, GitHubAuthService::class);
    }

    public function provides(): array
    {
        return [
            GitHubAuthInterface::class,
        ];
    }
}
