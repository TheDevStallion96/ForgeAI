<?php

namespace App\Domain\Governance\Services;

use App\Domain\Governance\Models\TokenBudget;
use App\Domain\Governance\Models\TokenLedger;
use Illuminate\Support\Facades\Config;

class TokenAccountant
{
    public function record(
        string $provider,
        string $model,
        int $promptTokens,
        int $completionTokens,
        ?int $organizationId = null,
        ?int $sessionId = null,
        ?int $agentId = null,
    ): TokenLedger {
        $cost = $this->calculateCost($provider, $model, $promptTokens, $completionTokens);

        $ledger = TokenLedger::create([
            'organization_id' => $organizationId,
            'session_id' => $sessionId,
            'agent_id' => $agentId,
            'provider' => $provider,
            'model' => $model,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'estimated_cost_usd' => $cost,
        ]);

        if ($organizationId) {
            $this->deductBudget($organizationId, $promptTokens + $completionTokens);
        }

        return $ledger;
    }

    public function calculateCost(
        string $provider,
        string $model,
        int $promptTokens,
        int $completionTokens,
    ): float {
        $pricing = Config::get("governance.pricing.{$provider}.{$model}");

        if (! $pricing) {
            return 0.0;
        }

        $inputCost = ($promptTokens / 1_000_000) * $pricing['input'];
        $outputCost = ($completionTokens / 1_000_000) * $pricing['output'];

        return round($inputCost + $outputCost, 6);
    }

    protected function deductBudget(int $organizationId, int $tokenCount): void
    {
        $budget = TokenBudget::firstOrCreate(
            ['organization_id' => $organizationId],
            [
                'monthly_limit' => Config::get('governance.budget.default_monthly_limit'),
            ],
        );

        $budget->deduct($tokenCount);
    }

    public function getUsage(int $organizationId, ?string $provider = null): int
    {
        $query = TokenLedger::where('organization_id', $organizationId);

        if ($provider) {
            $query->where('provider', $provider);
        }

        return $query->sum('prompt_tokens') + $query->sum('completion_tokens');
    }
}
