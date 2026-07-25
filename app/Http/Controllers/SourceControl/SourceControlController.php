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

class SourceControlController extends Controller
{
    public function __construct(
        private readonly GitHubAuthInterface $githubAuth,
    ) {}

    public function index(Request $request): Response
    {
        $orgId = $request->user()->organization_id;

        $repos = GitRepository::query()
            ->where('organization_id', $orgId)
            ->orderBy('name')
            ->get()
            ->map(fn (GitRepository $repo) => [
                'id' => $repo->id,
                'name' => $repo->name,
                'url' => $repo->url,
                'description' => $repo->description,
                'language' => $repo->language,
                'is_private' => $repo->is_private,
                'provider' => $repo->provider,
                'default_branch' => $repo->default_branch,
                'status' => $repo->status,
                'created_at' => $repo->created_at->diffForHumans(),
            ]);

        $installation = GitHubInstallation::where('user_id', $request->user()->id)
            ->where('organization_id', $orgId)
            ->first();

        return Inertia::render('source-control/Index', [
            'repos' => $repos,
            'installation' => $installation ? [
                'id' => $installation->id,
                'provider' => $installation->provider,
                'github_username' => $installation->github_username,
                'created_at' => $installation->created_at->diffForHumans(),
            ] : null,
        ]);
    }

    public function show(GitRepository $repo, Request $request): Response
    {
        $this->authorizeRepo($repo, $request);

        return Inertia::render('source-control/Show', [
            'repo' => [
                'id' => $repo->id,
                'name' => $repo->name,
                'url' => $repo->url,
                'description' => $repo->description,
                'language' => $repo->language,
                'is_private' => $repo->is_private,
                'default_branch' => $repo->default_branch,
                'clone_url' => $repo->clone_url,
                'ssh_url' => $repo->ssh_url,
                'provider' => $repo->provider,
            ],
        ]);
    }

    public function commits(GitRepository $repo, Request $request): Response
    {
        $this->authorizeRepo($repo, $request);

        $installation = GitHubInstallation::where('organization_id', $request->user()->organization_id)
            ->where('user_id', $request->user()->id)
            ->first();

        $commits = [];

        if ($installation) {
            $client = $this->githubAuth->getClient($installation->id);

            $owner = explode('/', $repo->name)[0] ?? null;

            if ($owner) {
                $name = explode('/', $repo->name, 2)[1] ?? $repo->name;
                $commits = $client->listCommits($owner, $name, [
                    'sha' => $request->input('branch', $repo->default_branch),
                ])->toArray();
            }
        }

        return Inertia::render('source-control/Commits', [
            'repo' => [
                'id' => $repo->id,
                'name' => $repo->name,
                'default_branch' => $repo->default_branch,
            ],
            'commits' => $commits,
            'branch' => $request->input('branch', $repo->default_branch),
        ]);
    }

    public function pullRequests(GitRepository $repo, Request $request): Response
    {
        $this->authorizeRepo($repo, $request);

        $installation = GitHubInstallation::where('organization_id', $request->user()->organization_id)
            ->where('user_id', $request->user()->id)
            ->first();

        $pullRequests = [];

        if ($installation) {
            $client = $this->githubAuth->getClient($installation->id);

            $parts = explode('/', $repo->name);
            $owner = $parts[0] ?? null;
            $name = $parts[1] ?? $repo->name;

            if ($owner) {
                $pullRequests = $client->listPullRequests($owner, $name, [
                    'state' => $request->input('state', 'open'),
                ])->toArray();
            }
        }

        return Inertia::render('source-control/PullRequests', [
            'repo' => [
                'id' => $repo->id,
                'name' => $repo->name,
                'default_branch' => $repo->default_branch,
            ],
            'pullRequests' => $pullRequests,
            'state' => $request->input('state', 'open'),
        ]);
    }

    public function destroy(GitRepository $repo, Request $request): RedirectResponse
    {
        $this->authorizeRepo($repo, $request);

        $repo->delete();

        return to_route('source-control.index')
            ->with('flash', ['success' => 'Repository removed.']);
    }

    private function authorizeRepo(GitRepository $repo, Request $request): void
    {
        if ($repo->organization_id !== $request->user()->organization_id) {
            abort(404);
        }
    }
}
