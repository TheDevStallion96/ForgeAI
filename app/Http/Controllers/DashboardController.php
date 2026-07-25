<?php

namespace App\Http\Controllers;

use App\Domain\Agent\Models\Agent;
use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\AuthTenant\Models\TeamInvitation;
use App\Domain\Governance\Models\AuditLog;
use App\Domain\Governance\Models\TokenBudget;
use App\Domain\Workspace\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $orgId = $request->user()->organization_id;
        $email = strtolower($request->user()->email);

        $pendingInvitations = TeamInvitation::query()
            ->with(['inviter', 'team'])
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->where(fn ($query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now()))
            ->latest()
            ->get()
            ->map(fn ($invitation) => [
                'code' => $invitation->code,
                'inviterName' => $invitation->inviter->name,
                'team' => [
                    'name' => $invitation->team->name,
                    'slug' => $invitation->team->slug,
                ],
            ]);

        $agentCount = Agent::where('organization_id', $orgId)->where('is_active', true)->count();
        $sessionCount = ExecutionSession::where('organization_id', $orgId)->count();
        $workspaceCount = Workspace::where('organization_id', $orgId)->where('status', 'active')->count();

        $budget = TokenBudget::where('organization_id', $orgId)->first();
        $tokenUsage = $budget ? $budget->current_usage : 0;
        $monthlyLimit = $budget ? $budget->monthly_limit : 1_000_000;
        $tokenPercent = $monthlyLimit > 0 ? round(($tokenUsage / $monthlyLimit) * 100) : 0;

        $recentLogs = AuditLog::where('organization_id', $orgId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn (AuditLog $log) => [
                'event' => $log->event_type,
                'time' => $log->created_at->diffForHumans(),
                'type' => str_contains($log->event_type, 'agent') ? 'agent'
                    : (str_contains($log->event_type, 'workspace') ? 'workspace' : 'governance'),
            ]);

        return Inertia::render('Dashboard', [
            'pendingInvitations' => $pendingInvitations,
            'stats' => [
                ['label' => 'Active Agents', 'value' => (string) $agentCount, 'change' => 'Across all workspaces', 'color' => 'text-indigo-500 bg-indigo-100 dark:bg-indigo-950/50'],
                ['label' => 'Token Usage', 'value' => number_format($tokenUsage), 'change' => $tokenPercent.'% of budget', 'color' => 'text-emerald-500 bg-emerald-100 dark:bg-emerald-950/50'],
                ['label' => 'Executions', 'value' => number_format($sessionCount), 'change' => 'Total sessions run', 'color' => 'text-amber-500 bg-amber-100 dark:bg-amber-950/50'],
                ['label' => 'Active Workspaces', 'value' => (string) $workspaceCount, 'change' => 'With recent activity', 'color' => 'text-sky-500 bg-sky-100 dark:bg-sky-950/50'],
            ],
            'recentActivity' => $recentLogs,
        ]);
    }
}
