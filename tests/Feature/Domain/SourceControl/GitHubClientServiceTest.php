<?php

use App\Domain\SourceControl\Services\GitHubClientService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->client = new GitHubClientService('fake-token');
});

test('github client sets correct headers', function () {
    Http::fake([
        'https://api.github.com/user/repos*' => Http::response([], 200, [
            'X-RateLimit-Remaining' => '50',
        ]),
    ]);

    $repos = $this->client->listRepositories();

    expect($repos)->toBeInstanceOf(Collection::class);
});

test('github client normalizes repository data', function () {
    Http::fake([
        'https://api.github.com/user/repos*' => Http::response([
            [
                'id' => 123,
                'full_name' => 'test-owner/test-repo',
                'name' => 'test-repo',
                'description' => 'A test repo',
                'private' => false,
                'language' => 'PHP',
                'default_branch' => 'main',
                'clone_url' => 'https://github.com/test-owner/test-repo.git',
                'ssh_url' => 'git@github.com:test-owner/test-repo.git',
                'html_url' => 'https://github.com/test-owner/test-repo',
                'stargazers_count' => 5,
                'forks_count' => 2,
                'updated_at' => '2026-01-01T00:00:00Z',
                'owner' => ['login' => 'test-owner', 'avatar_url' => 'https://example.com/avatar.png'],
            ],
        ], 200, ['X-RateLimit-Remaining' => '50']),
    ]);

    $repos = $this->client->listRepositories();

    expect($repos->count())->toBe(1);
    expect($repos->first()['name'])->toBe('test-owner/test-repo');
    expect($repos->first()['is_private'])->toBeFalse();
    expect($repos->first()['language'])->toBe('PHP');
});

test('github client handles pagination', function () {
    Http::fake([
        'https://api.github.com/user/repos?*' => Http::sequence()
            ->push([
                ['id' => 1, 'full_name' => 'repo1', 'name' => 'repo1', 'private' => false, 'description' => 'Repo 1', 'language' => 'PHP', 'default_branch' => 'main', 'clone_url' => 'https://github.com/owner/repo1.git', 'ssh_url' => 'git@github.com:owner/repo1.git', 'html_url' => 'https://github.com/owner/repo1', 'stargazers_count' => 0, 'forks_count' => 0, 'updated_at' => '2026-01-01T00:00:00Z', 'owner' => ['login' => 'u1', 'avatar_url' => '']],
            ], 200, ['X-RateLimit-Remaining' => '50', 'Link' => '<https://api.github.com/user/repos?page=2>; rel="next"'])
            ->push([
                ['id' => 2, 'full_name' => 'repo2', 'name' => 'repo2', 'private' => true, 'description' => 'Repo 2', 'language' => 'TS', 'default_branch' => 'main', 'clone_url' => 'https://github.com/owner/repo2.git', 'ssh_url' => 'git@github.com:owner/repo2.git', 'html_url' => 'https://github.com/owner/repo2', 'stargazers_count' => 0, 'forks_count' => 0, 'updated_at' => '2026-01-01T00:00:00Z', 'owner' => ['login' => 'u2', 'avatar_url' => '']],
            ], 200, ['X-RateLimit-Remaining' => '50']),
    ]);

    $repos = $this->client->listRepositories();

    expect($repos->count())->toBe(2);
});

test('github client handles api errors', function () {
    Http::fake([
        'https://api.github.com/user/repos*' => Http::response(['message' => 'Not Found'], 404),
    ]);

    expect(fn () => $this->client->listRepositories())->toThrow(RequestException::class);
});

test('listCommits normalizes commit data', function () {
    Http::fake([
        'https://api.github.com/repos/test-owner/test-repo/commits*' => Http::response([
            [
                'sha' => 'abc123def456',
                'commit' => [
                    'message' => 'Fix bug',
                    'author' => ['name' => 'Test Author', 'email' => 'test@example.com', 'date' => '2026-01-01T12:00:00Z'],
                ],
                'author' => ['login' => 'test-author', 'avatar_url' => 'https://example.com/avatar.png'],
                'html_url' => 'https://github.com/test-owner/test-repo/commit/abc123',
            ],
        ], 200, ['X-RateLimit-Remaining' => '50', 'Link' => '']),
    ]);

    $commits = $this->client->listCommits('test-owner', 'test-repo');

    expect($commits->count())->toBe(1);
    expect($commits->first()['sha'])->toBe('abc123def456');
    expect($commits->first()['short_sha'])->toBe('abc123d');
    expect($commits->first()['message'])->toBe('Fix bug');
    expect($commits->first()['author_name'])->toBe('Test Author');
});

test('listPullRequests normalizes PR data', function () {
    Http::fake([
        'https://api.github.com/repos/test-owner/test-repo/pulls*' => Http::response([
            [
                'id' => 1,
                'number' => 10,
                'title' => 'Add feature',
                'state' => 'open',
                'draft' => false,
                'merged' => false,
                'body' => 'This is a PR',
                'user' => ['login' => 'contributor', 'avatar_url' => 'https://example.com/avatar.png'],
                'head' => ['ref' => 'feature-branch'],
                'base' => ['ref' => 'main'],
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-01-02T00:00:00Z',
                'html_url' => 'https://github.com/test-owner/test-repo/pull/10',
            ],
        ], 200, ['X-RateLimit-Remaining' => '50', 'Link' => '']),
    ]);

    $prs = $this->client->listPullRequests('test-owner', 'test-repo');

    expect($prs->count())->toBe(1);
    expect($prs->first()['number'])->toBe(10);
    expect($prs->first()['state'])->toBe('open');
    expect($prs->first()['draft'])->toBeFalse();
    expect($prs->first()['head_branch'])->toBe('feature-branch');
});

test('listBranches normalizes branch data', function () {
    Http::fake([
        'https://api.github.com/repos/test-owner/test-repo/branches*' => Http::response([
            ['name' => 'main', 'commit' => ['sha' => 'abc123'], 'protected' => true],
            ['name' => 'develop', 'commit' => ['sha' => 'def456'], 'protected' => false],
        ], 200, ['X-RateLimit-Remaining' => '50', 'Link' => '']),
    ]);

    $branches = $this->client->listBranches('test-owner', 'test-repo');

    expect($branches->count())->toBe(2);
    expect($branches->first()['protected'])->toBeTrue();
});
