<?php

namespace App\Ai\Tools;

use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\AuthTenant\Models\User;
use App\Domain\Automation\Contracts\ToolInterface;
use App\Domain\Automation\Services\ToolExecutor;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ForgeToolAdapter implements Tool
{
    public function __construct(
        private readonly ToolInterface $tool,
        private readonly ToolExecutor $executor,
        private readonly ExecutionSession $session,
        private readonly User $user,
    ) {}

    public function description(): Stringable|string
    {
        return $this->tool->description();
    }

    public function handle(Request $request): Stringable|string
    {
        $result = $this->executor->execute(
            toolName: $this->tool->name(),
            parameters: $request->toArray(),
            session: $this->session,
            user: $this->user,
        );

        if (! $result->isSuccess()) {
            return $result->error ?? 'Tool execution failed.';
        }

        $output = $result->output;

        return is_string($output) ? $output : json_encode($output);
    }

    public function schema(JsonSchema $schema): array
    {
        $raw = $this->tool->parameterSchema();
        $properties = $raw['properties'] ?? [];
        $required = $raw['required'] ?? [];
        $result = [];

        foreach ($properties as $name => $prop) {
            $type = $prop['type'] ?? 'string';
            $builder = match ($type) {
                'integer', 'int' => $schema->integer(),
                'number' => $schema->number(),
                'boolean', 'bool' => $schema->boolean(),
                'array' => $schema->array()->items($schema->string()),
                default => $schema->string(),
            };

            if (isset($prop['description'])) {
                $builder = $builder->describe($prop['description']);
            }

            if (isset($prop['enum'])) {
                $builder = $builder->enum($prop['enum']);
            }

            if (in_array($name, $required)) {
                $builder = $builder->required();
            }

            $result[$name] = $builder;
        }

        return $result;
    }
}
