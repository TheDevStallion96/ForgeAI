<?php

namespace App\Http\Controllers\Monitoring;

use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\Governance\Models\AuditLog;
use App\Domain\Governance\Models\TokenBudget;
use App\Domain\Monitoring\Models\MonitoringAlert;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MonitoringController extends Controller
{
    public function index(Request $request): Response
    {
        $orgId = $request->user()->organization_id;

        $budget = TokenBudget::where('organization_id', $orgId)->first();
        $tokenUsage = $budget?->current_usage ?? 0;
        $monthlyLimit = $budget?->monthly_limit ?? 1_000_000;

        $completedSessions = ExecutionSession::where('organization_id', $orgId)
            ->where('status', 'completed')
            ->get()
            ->take(100);
        $avgLatency = $completedSessions->count() > 0
            ? $completedSessions->avg(fn ($s) => $s->created_at->diffInSeconds($s->updated_at))
            : 0;

        $metrics = [
            ['label' => 'API Response Time', 'value' => round($avgLatency * 1000).'ms', 'icon' => 'Timer', 'change' => 'Average latency', 'color' => 'text-emerald-500 bg-emerald-100 dark:bg-emerald-950/50'],
            ['label' => 'Active Sessions', 'value' => (string) ExecutionSession::where('organization_id', $orgId)->where('status', 'active')->count(), 'icon' => 'Server', 'change' => 'Currently running', 'color' => 'text-sky-500 bg-sky-100 dark:bg-sky-950/50'],
            ['label' => 'Error Rate', 'value' => $budget && $tokenUsage > 0 ? round(($budget->exceeded_count ?? 0) / max($tokenUsage, 1) * 100, 2).'%' : '0%', 'icon' => 'AlertTriangle', 'change' => 'Below threshold', 'color' => 'text-emerald-500 bg-emerald-100 dark:bg-emerald-950/50'],
            ['label' => 'Avg Token Latency', 'value' => round($avgLatency, 1).'s', 'icon' => 'Cpu', 'change' => 'Per completion', 'color' => 'text-amber-500 bg-amber-100 dark:bg-amber-950/50'],
        ];

        $alerts = MonitoringAlert::where('organization_id', $orgId)
            ->whereNull('acknowledged_at')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn (MonitoringAlert $alert) => [
                'severity' => $alert->severity,
                'message' => $alert->message,
                'time' => $alert->created_at->diffForHumans(),
            ]);

        $recentAudit = AuditLog::where('organization_id', $orgId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn (AuditLog $log) => [
                'message' => $log->event_type,
                'time' => $log->created_at->diffForHumans(),
            ]);

        return Inertia::render('monitoring/Index', [
            'metrics' => $metrics,
            'alerts' => $alerts,
            'recentAudit' => $recentAudit,
        ]);
    }
}
