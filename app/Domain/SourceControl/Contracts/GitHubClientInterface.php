<?php

namespace App\Domain\SourceControl\Contracts;

use Illuminate\Support\Collection;

interface GitHubClientInterface
{
    public function listRepositories(): Collection;

    public function getRepository(string $owner, string $repo): array;

    public function listCommits(string $owner, string $repo, array $params = []): Collection;

    public function listPullRequests(string $owner, string $repo, array $params = []): Collection;

    public function getPullRequest(string $owner, string $repo, int $prNumber): array;

    public function listBranches(string $owner, string $repo): Collection;
}
