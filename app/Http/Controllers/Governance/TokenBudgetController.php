<?php

namespace App\Http\Controllers\Governance;

use App\Domain\Governance\Models\TokenBudget;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TokenBudgetController extends Controller
{
    public function show(Request $request): Response
    {
        $organization = $request->user()->ensureOrganization();

        $budget = TokenBudget::query()
            ->firstOrCreate(
                ['organization_id' => $organization->id],
                [
                    'monthly_limit' => $organization->monthly_token_budget ?? 1_000_000,
                    'current_usage' => 0,
                    'reset_at' => now()->addMonth(),
                ],
            );

        return Inertia::render('governance/Budget/Show', [
            'budget' => [
                'id' => $budget->id,
                'monthly_limit' => $budget->monthly_limit,
                'current_usage' => $budget->current_usage,
                'usage_percentage' => $budget->usagePercentage(),
                'is_exhausted' => $budget->isExhausted(),
                'reset_at' => $budget->reset_at->toISOString(),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'monthly_limit' => ['required', 'integer', 'min:1000'],
        ]);

        $organization = $request->user()->ensureOrganization();

        $budget = TokenBudget::query()
            ->firstOrCreate(
                ['organization_id' => $organization->id],
                [
                    'monthly_limit' => $data['monthly_limit'],
                    'current_usage' => 0,
                    'reset_at' => now()->addMonth(),
                ],
            );

        if ($budget->exists) {
            $budget->update(['monthly_limit' => $data['monthly_limit']]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Budget updated.')]);

        return to_route('governance.budget.show');
    }
}
