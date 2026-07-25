<?php

namespace App\Domain\SourceControl\Services;

use App\Domain\SourceControl\Contracts\GitHubAuthInterface;
use App\Domain\SourceControl\Contracts\GitHubClientInterface;
use App\Domain\SourceControl\Models\GitHubInstallation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GitHubAuthService implements GitHubAuthInterface
{
    public function connectViaPat(string $token, int $userId, int $orgId): GitHubInstallation
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'ForgeAI/1.0',
        ])->get('https://api.github.com/user');

        $response->throw();

        $user = $response->json();

        return GitHubInstallation::updateOrCreate(
            ['organization_id' => $orgId, 'user_id' => $userId],
            [
                'provider' => 'pat',
                'access_token' => $token,
                'refresh_token' => null,
                'token_expires_at' => null,
                'github_username' => $user['login'],
                'scopes' => explode(',', $response->header('X-OAuth-Scopes', '')),
            ],
        );
    }

    public function initiateOAuth(): string
    {
        $clientId = config('github.client_id');
        $redirectUri = config('github.redirect_uri');
        $scopes = implode(',', config('github.default_scopes', ['repo', 'read:user']));
        $state = Str::random(40);

        session(['github_oauth_state' => $state]);

        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => url($redirectUri),
            'scope' => $scopes,
            'state' => $state,
        ]);

        return "https://github.com/login/oauth/authorize?{$params}";
    }

    public function handleOAuthCallback(string $code, int $userId, int $orgId): GitHubInstallation
    {
        $storedState = session('github_oauth_state');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'User-Agent' => 'ForgeAI/1.0',
        ])->post('https://github.com/login/oauth/access_token', [
            'client_id' => config('github.client_id'),
            'client_secret' => config('github.client_secret'),
            'code' => $code,
            'redirect_uri' => url(config('github.redirect_uri')),
        ]);

        $response->throw();

        $data = $response->json();

        if (isset($data['error'])) {
            throw new \RuntimeException('GitHub OAuth error: '.($data['error_description'] ?? $data['error']));
        }

        $accessToken = $data['access_token'];

        $userResponse = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'ForgeAI/1.0',
        ])->get('https://api.github.com/user');

        $userResponse->throw();

        $githubUser = $userResponse->json();

        $scopes = isset($data['scope']) ? explode(',', $data['scope']) : [];

        return GitHubInstallation::updateOrCreate(
            ['organization_id' => $orgId, 'user_id' => $userId],
            [
                'provider' => 'oauth',
                'access_token' => $accessToken,
                'refresh_token' => $data['refresh_token'] ?? null,
                'token_expires_at' => isset($data['expires_in'])
                    ? now()->addSeconds($data['expires_in'])
                    : null,
                'github_username' => $githubUser['login'],
                'scopes' => $scopes,
            ],
        );
    }

    public function disconnect(int $installationId): void
    {
        $installation = GitHubInstallation::findOrFail($installationId);

        $installation->delete();
    }

    public function getClient(int $installationId): GitHubClientInterface
    {
        $installation = GitHubInstallation::findOrFail($installationId);

        return new GitHubClientService($installation->access_token);
    }
}
