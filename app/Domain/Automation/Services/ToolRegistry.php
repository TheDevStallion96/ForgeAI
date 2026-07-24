<?php

namespace App\Domain\Automation\Services;

use App\Domain\Automation\Contracts\ToolInterface;
use Illuminate\Support\Collection;

class ToolRegistry
{
    protected Collection $tools;

    public function __construct()
    {
        $this->tools = collect();
    }

    public function register(ToolInterface $tool): self
    {
        $this->tools->put($tool->name(), $tool);

        return $this;
    }

    public function get(string $name): ?ToolInterface
    {
        return $this->tools->get($name);
    }

    public function has(string $name): bool
    {
        return $this->tools->has($name);
    }

    public function all(): Collection
    {
        return $this->tools;
    }

    public function getDefinitions(): array
    {
        return $this->tools->map(fn (ToolInterface $tool) => [
            'name' => $tool->name(),
            'description' => $tool->description(),
            'parameters' => $tool->parameterSchema(),
            'requires_hitl' => $tool->requiresHumanApproval(),
        ])->values()->toArray();
    }
}
