<?php

use App\Domain\AuthTenant\Models\User;
use App\Domain\SourceControl\Models\GitHubInstallation;
use App\Domain\SourceControl\Models\GitRepository;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('redirects guests to login', function () {
    $response = $this->get(route('source-control.github.connect'));

    $response->assertRedirect(route('login'));
});

it('renders the connect page when not connected', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('source-control.github.connect'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('source-control/GitHubConnect')
        ->has('installation', null),
    );
});

it('renders the connect page with existing installation', function () {
    GitHubInstallation::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->user->organization_id,
        'provider' => 'pat',
        'github_username' => 'testuser',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->get(route('source-control.github.connect'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('source-control/GitHubConnect')
        ->has('installation'),
    );
});

it('validates PAT token', function () {
    Http::fake([
        'api.github.com/user' => Http::response(['login' => 'testuser'], 200),
    ]);

    $response = $this
        ->actingAs($this->user)
        ->post(route('source-control.github.pat'), [
            'token' => '',
        ]);

    $response->assertInvalid(['token']);
});

it('connects via PAT', function () {
    Http::fake([
        'api.github.com/user' => Http::response(['login' => 'testuser'], 200, [
            'X-OAuth-Scopes' => 'repo,read:user',
        ]),
    ]);

    $response = $this
        ->actingAs($this->user)
        ->post(route('source-control.github.pat'), [
            'token' => 'ghp_fake_token',
        ]);

    $response->assertRedirect(route('source-control.index'));
    expect(GitHubInstallation::where('user_id', $this->user->id)->exists())->toBeTrue();
});

it('redirects to GitHub OAuth', function () {
    config([
        'github.client_id' => 'test-client-id',
        'github.client_secret' => 'test-secret',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->get(route('source-control.github.oauth'));

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('github.com/login/oauth/authorize');
});

it('renders the connect page when not connected and shows the form', function () {
    Http::fake([
        'https://api.github.com/user' => Http::response(['login' => 'testuser'], 200, [
            'X-OAuth-Scopes' => 'repo,read:user',
        ]),
    ]);

    $response = $this
        ->actingAs($this->user)
        ->post(route('source-control.github.pat'), [
            'token' => 'ghp_valid_token',
        ]);

    $response->assertRedirect(route('source-control.index'));
    expect(GitHubInstallation::where('user_id', $this->user->id)->exists())->toBeTrue();
    expect(GitHubInstallation::where('user_id', $this->user->id)->first()->github_username)->toBe('testuser');
});

it('disconnects GitHub installation', function () {
    $installation = GitHubInstallation::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->user->organization_id,
    ]);

    $response = $this
        ->actingAs($this->user)
        ->post(route('source-control.github.disconnect', ['installation' => $installation->id]));

    $response->assertRedirect(route('source-control.index'));
    expect(GitHubInstallation::find($installation->id))->toBeNull();
});

it('syncs repositories from GitHub', function () {
    Http::fake([
        'api.github.com/user/repos*' => Http::response([
            [
                'id' => 1,
                'full_name' => 'test-org/test-repo',
                'name' => 'test-repo',
                'description' => 'A test repo',
                'private' => false,
                'language' => 'PHP',
                'default_branch' => 'main',
                'clone_url' => 'https://github.com/test-org/test-repo.git',
                'ssh_url' => 'git@github.com:test-org/test-repo.git',
                'html_url' => 'https://github.com/test-org/test-repo',
                'stargazers_count' => 0,
                'forks_count' => 0,
                'updated_at' => '2026-01-01T00:00:00Z',
                'owner' => ['login' => 'test-org', 'avatar_url' => ''],
            ],
        ], 200, ['X-RateLimit-Remaining' => '50']),
    ]);

    $installation = GitHubInstallation::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->user->organization_id,
        'provider' => 'pat',
        'access_token' => 'fake-token',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->post(route('source-control.github.sync', ['installation' => $installation->id]));

    $response->assertRedirect(route('source-control.index'));
    expect(GitRepository::where('organization_id', $this->user->organization_id)->exists())->toBeTrue();
});
