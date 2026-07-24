<?php

namespace App\Domain\Governance\Middleware;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Governance\Events\SuspiciousActivityLogged;
use App\Domain\Governance\Services\BudgetEnforcer;
use App\Domain\Governance\Services\PiiRedactor;
use App\Domain\Governance\Services\PromptInjectionShield;
use App\Domain\Governance\Services\TokenAccountant;
use Closure;

class GovernanceMiddleware
{
    public function __construct(
        protected BudgetEnforcer $budgetEnforcer,
        protected PromptInjectionShield $injectionShield,
        protected PiiRedactor $piiRedactor,
        protected TokenAccountant $tokenAccountant,
    ) {}

    public function handlePrompt(
        string $prompt,
        Organization $organization,
        Closure $next,
    ): mixed {
        $redacted = $this->piiRedactor->redact($prompt);

        $analysis = $this->injectionShield->analyze($redacted);

        if ($analysis['is_blocked']) {
            SuspiciousActivityLogged::dispatch(
                $organization->id,
                null,
                'prompt_injection',
                $analysis,
            );

            return response()->json([
                'error' => 'Prompt blocked by security shield',
                'risk_score' => $analysis['risk_score'],
            ], 403);
        }

        if (! $this->budgetEnforcer->authorize($organization)) {
            return response()->json([
                'error' => 'Token budget exhausted',
            ], 429);
        }

        return $next($redacted, $organization);
    }

    public function handleResponse(
        string $output,
        array $usage,
        Organization $organization,
        string $provider,
        string $model,
    ): string {
        $this->tokenAccountant->record(
            provider: $provider,
            model: $model,
            promptTokens: $usage['prompt_tokens'] ?? 0,
            completionTokens: $usage['completion_tokens'] ?? 0,
            organizationId: $organization->id,
        );

        return $this->piiRedactor->mask($output);
    }
}
