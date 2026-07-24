<?php

namespace App\Http\Controllers\Governance;

use App\Domain\Governance\Models\AuditLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $logs = AuditLog::query()
            ->where('organization_id', $request->user()->organization_id)
            ->orderBy('created_at', 'desc')
            ->paginate(50)
            ->through(fn (AuditLog $log) => [
                'id' => $log->id,
                'event_type' => $log->event_type,
                'payload' => $log->payload,
                'metadata' => $log->metadata,
                'is_tampered' => $log->isTampered(),
                'created_at' => $log->created_at->toISOString(),
            ]);

        return Inertia::render('governance/AuditLogs/Index', [
            'logs' => $logs,
        ]);
    }
}
