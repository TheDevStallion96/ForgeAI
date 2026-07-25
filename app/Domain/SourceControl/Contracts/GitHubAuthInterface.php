<?php

namespace App\Domain\SourceControl\Contracts;

use App\Domain\SourceControl\Models\GitHubInstallation;

interface GitHubAuthInterface
{
    public function connectViaPat(string $token, int $userId, int $orgId): GitHubInstallation;

    public function initiateOAuth(): string;

    public function handleOAuthCallback(string $code, int $userId, int $orgId): GitHubInstallation;

    public function disconnect(int $installationId): void;

    public function getClient(int $installationId): GitHubClientInterface;
}
