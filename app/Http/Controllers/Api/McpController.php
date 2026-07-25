<?php

namespace App\Http\Controllers\Api;

use App\Domain\McpGateway\Models\McpConnection;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class McpController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        if ($orgId === null) {
            return response()->json(['data' => []]);
        }

        $connections = McpConnection::query()
            ->where('organization_id', $orgId)
            ->get();

        return response()->json(['data' => $connections]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'transport_type' => ['required', 'string', 'in:stdio,sse'],
            'command' => ['nullable', 'string', 'max:500'],
            'server_url' => ['nullable', 'url', 'max:500'],
            'auth_token' => ['nullable', 'string', 'max:500'],
        ]);

        $orgId = $request->user()->organization_id;

        if ($orgId === null) {
            return response()->json(['error' => 'No organization selected.'], 400);
        }

        $connection = McpConnection::query()->create([
            'organization_id' => $orgId,
            ...$validated,
        ]);

        return response()->json(['data' => $connection], 201);
    }

    public function destroy(Request $request, McpConnection $connection): JsonResponse
    {
        if ($connection->organization_id !== $request->user()->organization_id) {
            return response()->json(['error' => 'Forbidden.'], 403);
        }

        $connection->delete();

        return response()->json(['message' => 'Deleted.']);
    }
}
