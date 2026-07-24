<?php

namespace App\Domain\Automation\Services;

use App\Domain\Agent\Models\ExecutionSession;
use App\Domain\AuthTenant\Models\User;
use App\Domain\Automation\Contracts\ToolResult;
use App\Domain\Automation\Events\ToolExecutionCompleted;
use App\Domain\Automation\Events\ToolExecutionFailed;
use App\Domain\Automation\Models\ToolExecution;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class ToolExecutor
{
    public function __construct(
        protected ToolRegistry $registry,
        protected ToolPermissionChecker $permissionChecker,
    ) {}

    public function execute(
        string $toolName,
        array $parameters,
        ExecutionSession $session,
        User $user,
    ): ToolResult {
        $tool = $this->registry->get($toolName);

        if (! $tool) {
            return ToolResult::failure("Tool '{$toolName}' not found");
        }

        $execution = ToolExecution::create([
            'organization_id' => $user->organization_id,
            'session_id' => $session->id,
            'tool_name' => $toolName,
            'parameters' => $parameters,
            'requires_hitl' => $tool->requiresHumanApproval(),
            'status' => 'pending',
        ]);

        if ($tool->requiresHumanApproval()) {
            $execution->update(['status' => 'awaiting_approval']);

            return ToolResult::failure("Tool '{$toolName}' requires human approval");
        }

        if (! $this->permissionChecker->canExecute($user, $toolName)) {
            return ToolResult::failure("Permission denied for tool '{$toolName}'");
        }

        $execution->markStarted();

        try {
            $result = $tool->execute($parameters);
            $execution->markCompleted($result);

            if ($result->isSuccess()) {
                ToolExecutionCompleted::dispatch($execution, $result);
            } else {
                ToolExecutionFailed::dispatch($execution, $result->error);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('Tool execution failed', [
                'tool' => $toolName,
                'session' => $session->id,
                'error' => $e->getMessage(),
            ]);

            $result = ToolResult::failure($e->getMessage());
            $execution->markCompleted($result);
            ToolExecutionFailed::dispatch($execution, $e->getMessage());

            return $result;
        }
    }

    public function executeAsync(
        string $toolName,
        array $parameters,
        ExecutionSession $session,
        User $user,
    ): void {
        Queue::push(function () use ($toolName, $parameters, $session, $user) {
            $this->execute($toolName, $parameters, $session, $user);
        });
    }
}
