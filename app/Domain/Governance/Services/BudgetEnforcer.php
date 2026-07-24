<?php

namespace App\Domain\Governance\Services;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Governance\Events\QuotaThresholdExceeded;
use App\Domain\Governance\Models\TokenBudget;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class BudgetEnforcer
{
    protected array $notifiedThresholds = [];

    public function authorize(Organization $organization): bool
    {
        $budget = TokenBudget::firstOrCreate(
            ['organization_id' => $organization->id],
            [
                'monthly_limit' => Config::get('governance.budget.default_monthly_limit'),
            ],
        );

        if ($budget->needsReset()) {
            $budget->update([
                'current_usage' => 0,
                'reset_at' => now()->addMonth(),
            ]);

            return true;
        }

        if ($budget->isExhausted()) {
            Log::warning('Token budget exhausted', [
                'org_id' => $organization->id,
                'usage' => $budget->current_usage,
                'limit' => $budget->monthly_limit,
            ]);

            return false;
        }

        $this->checkThresholds($budget);

        return true;
    }

    protected function checkThresholds(TokenBudget $budget): void
    {
        $percentage = $budget->usagePercentage();
        $thresholds = Config::get('governance.budget.thresholds', []);

        foreach ($thresholds as $threshold) {
            $key = "{$budget->organization_id}:{$threshold}";

            if ($percentage >= $threshold && ! isset($this->notifiedThresholds[$key])) {
                $this->notifiedThresholds[$key] = true;

                QuotaThresholdExceeded::dispatch(
                    $budget->organization_id,
                    $percentage,
                    $budget->monthly_limit,
                );

                Log::info('Budget threshold reached', [
                    'org_id' => $budget->organization_id,
                    'threshold' => $threshold,
                    'percentage' => $percentage,
                ]);
            }
        }
    }
}
