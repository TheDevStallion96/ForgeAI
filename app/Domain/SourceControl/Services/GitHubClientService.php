<?php

namespace App\Domain\SourceControl\Services;

use App\Domain\SourceControl\Contracts\GitHubClientInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GitHubClientService implements GitHubClientInterface
{
    private const BASE_URL = 'https://api.github.com';

    public function __construct(private readonly string $token) {}

    public function listRepositories(): Collection
    {
        $repos = $this->paginatedGet('/user/repos', ['sort' => 'updated', 'per_page' => 100]);

        return collect($repos)->map(fn (array $repo) => $this->normalizeRepo($repo));
    }

    public function getRepository(string $owner, string $repo): array
    {
        $response = $this->request()->get("/repos/{$owner}/{$repo}");

        $response->throw();

        return $this->normalizeRepo($response->json());
    }

    public function listCommits(string $owner, string $repo, array $params = []): Collection
    {
        $params['per_page'] ??= 30;
        $commits = $this->paginatedGet("/repos/{$owner}/{$repo}/commits", $params);

        return collect($commits)->map(fn (array $c) => [
            'sha' => $c['sha'],
            'short_sha' => substr($c['sha'], 0, 7),
            'message' => $c['commit']['message'],
            'author_name' => $c['commit']['author']['name'],
            'author_email' => $c['commit']['author']['email'],
            'author_username' => $c['author']['login'] ?? null,
            'author_avatar' => $c['author']['avatar_url'] ?? null,
            'date' => $c['commit']['author']['date'],
            'url' => $c['html_url'],
        ]);
    }

    public function listPullRequests(string $owner, string $repo, array $params = []): Collection
    {
        $params['per_page'] ??= 30;
        $params['state'] ??= 'open';
        $prs = $this->paginatedGet("/repos/{$owner}/{$repo}/pulls", $params);

        return collect($prs)->map(fn (array $pr) => [
            'id' => $pr['id'],
            'number' => $pr['number'],
            'title' => $pr['title'],
            'state' => $pr['state'],
            'body' => $pr['body'],
            'author' => $pr['user']['login'] ?? null,
            'author_avatar' => $pr['user']['avatar_url'] ?? null,
            'created_at' => $pr['created_at'],
            'updated_at' => $pr['updated_at'],
            'head_branch' => $pr['head']['ref'],
            'base_branch' => $pr['base']['ref'],
            'url' => $pr['html_url'],
            'draft' => $pr['draft'] ?? false,
            'merged' => $pr['merged'] ?? false,
        ]);
    }

    public function getPullRequest(string $owner, string $repo, int $prNumber): array
    {
        $response = $this->request()->get("/repos/{$owner}/{$repo}/pulls/{$prNumber}");

        $response->throw();

        $pr = $response->json();

        return [
            'id' => $pr['id'],
            'number' => $pr['number'],
            'title' => $pr['title'],
            'state' => $pr['state'],
            'body' => $pr['body'],
            'author' => $pr['user']['login'] ?? null,
            'author_avatar' => $pr['user']['avatar_url'] ?? null,
            'created_at' => $pr['created_at'],
            'updated_at' => $pr['updated_at'],
            'head_branch' => $pr['head']['ref'],
            'base_branch' => $pr['base']['ref'],
            'url' => $pr['html_url'],
            'draft' => $pr['draft'] ?? false,
            'merged' => $pr['merged'] ?? false,
        ];
    }

    public function listBranches(string $owner, string $repo): Collection
    {
        $branches = $this->paginatedGet("/repos/{$owner}/{$repo}/branches", ['per_page' => 100]);

        return collect($branches)->map(fn (array $b) => [
            'name' => $b['name'],
            'sha' => $b['commit']['sha'],
            'protected' => $b['protected'] ?? false,
        ]);
    }

    private function request(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'ForgeAI/1.0',
        ])->timeout(15);
    }

    private function paginatedGet(string $path, array $params = []): array
    {
        $all = [];
        $page = 1;

        do {
            $response = $this->request()->get(self::BASE_URL.$path, array_merge($params, ['page' => $page, 'per_page' => $params['per_page'] ?? 100]));

            $response->throw();

            $items = $response->json();
            $all = array_merge($all, $items);

            $linkHeader = $response->header('Link');
            $hasNext = $linkHeader && str_contains($linkHeader, 'rel="next"');
            $page++;

            $rateLimitRemaining = (int) $response->header('X-RateLimit-Remaining', 0);
            if ($rateLimitRemaining < 10) {
                break;
            }
        } while ($hasNext && $page <= 10);

        return $all;
    }

    private function normalizeRepo(array $repo): array
    {
        return [
            'id' => $repo['id'],
            'name' => $repo['full_name'],
            'short_name' => $repo['name'],
            'description' => $repo['description'],
            'url' => $repo['html_url'],
            'clone_url' => $repo['clone_url'],
            'ssh_url' => $repo['ssh_url'],
            'language' => $repo['language'],
            'default_branch' => $repo['default_branch'],
            'is_private' => $repo['private'],
            'owner' => $repo['owner']['login'],
            'owner_avatar' => $repo['owner']['avatar_url'],
            'stars' => $repo['stargazers_count'],
            'forks' => $repo['forks_count'],
            'updated_at' => $repo['updated_at'],
        ];
    }
}
