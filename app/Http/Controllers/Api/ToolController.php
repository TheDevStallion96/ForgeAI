<?php

namespace App\Http\Controllers\Api;

use App\Domain\Automation\Services\ToolRegistry;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function __construct(
        private readonly ToolRegistry $toolRegistry,
    ) {}

    public function index(): JsonResponse
    {
        $definitions = $this->toolRegistry->getDefinitions();

        return response()->json([
            'data' => array_values($definitions),
        ]);
    }

    public function execute(Request $request, string $tool): JsonResponse
    {
        $validated = $request->validate([
            'arguments' => ['required', 'array'],
        ]);

        $toolInstance = $this->toolRegistry->get($tool);

        if ($toolInstance === null) {
            return response()->json(['error' => "Unknown tool: {$tool}"], 404);
        }

        $result = $toolInstance->execute($validated['arguments']);

        return response()->json([
            'success' => $result->isSuccess(),
            'output' => $result->output,
            'error' => $result->error,
        ]);
    }
}
