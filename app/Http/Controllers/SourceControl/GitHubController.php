<?php

namespace App\Http\Controllers\SourceControl;

use App\Domain\SourceControl\Contracts\GitHubAuthInterface;
use App\Domain\SourceControl\Models\GitHubInstallation;
use App\Domain\SourceControl\Models\GitRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GitHubController extends Controller
{
    public function __construct(private readonly GitHubAuthInterface $githubAuth) {}

    public function connect(): Response
    {
        $installation = GitHubInstallation::where('user_id', request()->user()->id)
            ->where('organization_id', request()->user()->organization_id)
            ->first();

        return Inertia::render('source-control/GitHubConnect', [
            'installation' => $installation ? [
                'id' => $installation->id,
                'provider' => $installation->provider,
                'github_username' => $installation->github_username,
                'scopes' => $installation->scopes,
                'created_at' => $installation->created_at->diffForHumans(),
            ] : null,
        ]);
    }

    public function storePat(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'min:2'],
        ]);

        $installation = $this->githubAuth->connectViaPat(
            $validated['token'],
            $request->user()->id,
            $request->user()->organization_id,
        );

        return to_route('source-control.index')
            ->with('flash', ['success' => 'Connected to GitHub as '.$installation->github_username]);
    }

    public function redirectToOAuth(Request $request): RedirectResponse
    {
        $url = $this->githubAuth->initiateOAuth();

        return redirect()->away($url);
    }

    public function handleOAuthCallback(Request $request): RedirectResponse
    {
        if ($request->missing('code')) {
            return to_route('source-control.github.connect')
                ->with('flash', ['error' => 'GitHub OAuth was cancelled or failed.']);
        }

        $installation = $this->githubAuth->handleOAuthCallback(
            $request->input('code'),
            $request->user()->id,
            $request->user()->organization_id,
        );

        return to_route('source-control.index')
            ->with('flash', ['success' => 'Connected to GitHub as '.$installation->github_username]);
    }

    public function disconnect(int $installationId, Request $request): RedirectResponse
    {
        $installation = GitHubInstallation::where('id', $installationId)
            ->where('organization_id', $request->user()->organization_id)
            ->firstOrFail();

        $this->githubAuth->disconnect($installation->id);

        return to_route('source-control.index')
            ->with('flash', ['success' => 'Disconnected from GitHub.']);
    }

    public function syncRepos(int $installationId, Request $request): RedirectResponse
    {
        $installation = GitHubInstallation::where('id', $installationId)
            ->where('organization_id', $request->user()->organization_id)
            ->firstOrFail();

        $client = $this->githubAuth->getClient($installation->id);

        $repos = $client->listRepositories();

        $orgId = $request->user()->organization_id;

        foreach ($repos as $repo) {
            GitRepository::updateOrCreate(
                ['organization_id' => $orgId, 'github_id' => $repo['id']],
                [
                    'name' => $repo['name'],
                    'url' => $repo['url'],
                    'description' => $repo['description'],
                    'clone_url' => $repo['clone_url'],
                    'ssh_url' => $repo['ssh_url'],
                    'language' => $repo['language'],
                    'default_branch' => $repo['default_branch'],
                    'is_private' => $repo['is_private'],
                    'provider' => 'github',
                    'status' => 'active',
                ],
            );
        }

        return to_route('source-control.index')
            ->with('flash', ['success' => 'Synced '.count($repos).' repositories from GitHub.']);
    }
}
