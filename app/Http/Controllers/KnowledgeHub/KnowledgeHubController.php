<?php

namespace App\Http\Controllers\KnowledgeHub;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\KnowledgeVector\Models\KnowledgeAsset;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Ai\Files\Document;
use Laravel\Ai\Stores;

class KnowledgeHubController extends Controller
{
    public function index(Request $request): Response
    {
        $orgId = $request->user()->organization_id;

        $assets = KnowledgeAsset::query()
            ->where('organization_id', $orgId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (KnowledgeAsset $asset) => [
                'id' => $asset->id,
                'name' => $asset->name,
                'mime_type' => $asset->mime_type,
                'file_size' => $asset->file_size,
                'status' => $asset->status,
                'created_at' => $asset->created_at->toISOString(),
            ]);

        return Inertia::render('knowledge/Index', [
            'assets' => $assets,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:txt,pdf,md,csv,json,xml,html,jpg,jpeg,png,gif,webp'],
        ]);

        $org = $request->user()->organization;
        $file = $data['file'];

        try {
            $stored = Document::fromUpload($file)->put();

            $storeId = $this->ensureVectorStore($org);

            $store = Stores::get($storeId);
            $store->add($stored->id);

            KnowledgeAsset::query()->create([
                'organization_id' => $org->id,
                'provider_file_id' => $stored->id,
                'provider_store_id' => $storeId,
                'provider' => config('ai.default'),
                'name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'status' => 'ready',
            ]);

            Inertia::flash('toast', ['type' => 'success', 'message' => __('File uploaded and indexed.')]);
        } catch (\Throwable $e) {
            report($e);
            Inertia::flash('toast', ['type' => 'error', 'message' => __('Upload failed: :error', ['error' => $e->getMessage()])]);
        }

        return to_route('knowledge.index');
    }

    public function destroy(Request $request, KnowledgeAsset $asset): RedirectResponse
    {
        abort_unless($asset->organization_id === $request->user()->organization_id, 403);

        try {
            if ($asset->provider_store_id) {
                $store = Stores::get($asset->provider_store_id);
                if ($asset->provider_file_id) {
                    $store->remove($asset->provider_file_id);
                }
            }

            if ($asset->provider_file_id) {
                Document::fromId($asset->provider_file_id)->delete();
            }

            $asset->delete();

            Inertia::flash('toast', ['type' => 'info', 'message' => __('File removed.')]);
        } catch (\Throwable $e) {
            report($e);
            Inertia::flash('toast', ['type' => 'error', 'message' => __('Deletion failed: :error', ['error' => $e->getMessage()])]);
        }

        return to_route('knowledge.index');
    }

    private function ensureVectorStore(Organization $org): string
    {
        if ($org->vector_store_id) {
            return $org->vector_store_id;
        }

        $store = Stores::create(
            name: $org->name.' Knowledge Base',
            description: 'Vector store for '.$org->name,
        );

        $org->update(['vector_store_id' => $store->id]);

        return $store->id;
    }
}
