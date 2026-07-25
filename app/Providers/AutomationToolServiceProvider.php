<?php

namespace App\Providers;

use App\Domain\Automation\Services\SandboxedToolRunner;
use App\Domain\Automation\Services\ToolExecutor;
use App\Domain\Automation\Services\ToolPermissionChecker;
use App\Domain\Automation\Services\ToolRegistry;
use App\Domain\Automation\Tools\DeleteFileTool;
use App\Domain\Automation\Tools\GitBranchTool;
use App\Domain\Automation\Tools\GitCommitTool;
use App\Domain\Automation\Tools\GitDiffTool;
use App\Domain\Automation\Tools\ListDirectoryTool;
use App\Domain\Automation\Tools\ReadFileTool;
use App\Domain\Automation\Tools\RipgrepTool;
use App\Domain\Automation\Tools\RunCommandTool;
use App\Domain\Automation\Tools\VectorSearchTool;
use App\Domain\Automation\Tools\WriteFileTool;
use App\Domain\KnowledgeVector\Services\VectorSearch;
use Illuminate\Support\ServiceProvider;

class AutomationToolServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ToolRegistry::class);
        $this->app->singleton(ToolPermissionChecker::class);
        $this->app->singleton(SandboxedToolRunner::class);
        $this->app->singleton(ToolExecutor::class);
    }

    public function boot(): void
    {
        $registry = $this->app->make(ToolRegistry::class);

        $registry->register(new ReadFileTool);
        $registry->register(new WriteFileTool);
        $registry->register(new ListDirectoryTool);
        $registry->register(new DeleteFileTool);
        $registry->register(new RunCommandTool($this->app->make(SandboxedToolRunner::class)));
        $registry->register(new GitCommitTool);
        $registry->register(new GitBranchTool);
        $registry->register(new GitDiffTool);
        $registry->register(new RipgrepTool);

        if ($this->app->has(VectorSearch::class)) {
            $registry->register(new VectorSearchTool($this->app->make(VectorSearch::class)));
        }
    }
}
