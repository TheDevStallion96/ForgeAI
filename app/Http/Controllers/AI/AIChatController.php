<?php

namespace App\Http\Controllers\AI;

use App\Domain\AIEngine\Contracts\AIEngine;
use App\Domain\AIEngine\Data\CompletionRequest;
use App\Domain\AIEngine\Enums\AIProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AIChatController extends Controller
{
    public function __construct(
        private readonly AIEngine $engine,
    ) {}

    public function index(): Response
    {
        return Inertia::render('AI/Chat');
    }

    public function chat(Request $request)
    {
        $data = $request->validate([
            'prompt' => ['required', 'string', 'max:2000'],
            'system_instruction' => ['nullable', 'string', 'max:5000'],
            'provider' => ['nullable', 'string'],
        ]);

        $request = new CompletionRequest(
            prompt: $data['prompt'],
            systemInstruction: $data['system_instruction'] ?? null,
            provider: $data['provider'] ?? [AIProvider::OpenAI],
            model: 'gpt-4o-mini',
        );

        return $this->engine->stream($request);
    }
}
