<?php

namespace App\Http\Controllers\ArchitectureStudio;

use App\Domain\ArchitectureStudio\Models\ArchitectureDecisionRecord;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArchitectureStudioController extends Controller
{
    public function index(Request $request): Response
    {
        $orgId = $request->user()->organization_id;

        $adrs = ArchitectureDecisionRecord::query()
            ->where('organization_id', $orgId)
            ->orderBy('adr_number', 'desc')
            ->get()
            ->map(fn (ArchitectureDecisionRecord $adr) => [
                'id' => $adr->id,
                'title' => $adr->title,
                'adr_number' => $adr->adr_number,
                'status' => $adr->status,
                'context' => $adr->context,
                'created_at' => $adr->created_at->toISOString(),
            ]);

        return Inertia::render('architecture/Index', [
            'adrs' => $adrs,
            'stats' => [
                'total' => $adrs->count(),
                'accepted' => $adrs->where('status', 'accepted')->count(),
                'proposed' => $adrs->where('status', 'proposed')->count(),
                'draft' => $adrs->where('status', 'draft')->count(),
            ],
        ]);
    }

    public function adrs(Request $request): Response
    {
        $orgId = $request->user()->organization_id;

        $adrs = ArchitectureDecisionRecord::query()
            ->where('organization_id', $orgId)
            ->orderBy('adr_number', 'desc')
            ->paginate(20)
            ->through(fn (ArchitectureDecisionRecord $adr) => [
                'id' => $adr->id,
                'title' => $adr->title,
                'adr_number' => $adr->adr_number,
                'status' => $adr->status,
                'context' => $adr->context,
                'decision' => $adr->decision,
                'consequences' => $adr->consequences,
                'created_at' => $adr->created_at->toISOString(),
            ]);

        return Inertia::render('architecture/Adrs', [
            'adrs' => $adrs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'context' => ['nullable', 'string'],
            'decision' => ['nullable', 'string'],
            'consequences' => ['nullable', 'string'],
        ]);

        $orgId = $request->user()->organization_id;

        $nextNumber = ArchitectureDecisionRecord::where('organization_id', $orgId)->max('adr_number') + 1;

        ArchitectureDecisionRecord::query()->create([
            'organization_id' => $orgId,
            'title' => $data['title'],
            'adr_number' => $nextNumber,
            'status' => 'draft',
            'context' => $data['context'] ?? null,
            'decision' => $data['decision'] ?? null,
            'consequences' => $data['consequences'] ?? null,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('ADR created.')]);

        return to_route('architecture.index');
    }
}
