<?php

namespace App\Http\Controllers\Governance;

use App\Domain\Governance\Services\SecretsManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApiKeyController extends Controller
{
    public function __construct(
        private readonly SecretsManager $secretsManager,
    ) {}

    public function index(Request $request): Response
    {
        $organization = $request->user()->ensureOrganization();

        $keys = $this->secretsManager->list($organization->id);

        return Inertia::render('governance/ApiKeys/Index', [
            'keys' => $keys,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $organization = $request->user()->ensureOrganization();

        $this->secretsManager->store(
            $organization->id,
            $data['provider'],
            $data['key'],
            $data['name'] ?? null,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('API key added.')]);

        return to_route('governance.api-keys.index');
    }

    public function destroy(Request $request, int $apiKeyId): RedirectResponse
    {
        $this->secretsManager->revoke($apiKeyId);

        Inertia::flash('toast', ['type' => 'info', 'message' => __('API key revoked.')]);

        return to_route('governance.api-keys.index');
    }
}
