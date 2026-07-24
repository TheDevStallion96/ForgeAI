<?php

use App\Domain\Automation\Tools\GitBranchTool;
use App\Domain\Automation\Tools\GitCommitTool;
use App\Domain\Automation\Tools\GitDiffTool;

beforeEach(function () {
    // Create a temp git repo for testing
    $this->tempDir = sys_get_temp_dir().'/forgeai-git-test-'.uniqid();
    mkdir($this->tempDir, 0755, true);

    shell_exec("cd {$this->tempDir} && git init 2>&1 && git config user.email 'test@test.com' && git config user.name 'Test' && git commit --allow-empty -m 'init' 2>&1");
});

afterEach(function () {
    shell_exec("rm -rf {$this->tempDir}");
});

it('git_branch lists branches', function () {
    $tool = new GitBranchTool;
    $result = $tool->execute(['action' => 'list', 'cwd' => $this->tempDir]);

    expect($result->isSuccess())->toBeTrue();
    expect($result->output)->toContain('master');
});

it('git_branch creates a new branch', function () {
    shell_exec("cd {$this->tempDir} && git commit --allow-empty -m 'Initial commit' 2>&1");

    $tool = new GitBranchTool;
    $result = $tool->execute([
        'action' => 'create',
        'branch_name' => 'feature/test',
        'cwd' => $this->tempDir,
    ]);

    expect($result->isSuccess())->toBeTrue();
});

it('git_diff shows no diff for clean repo', function () {
    $tool = new GitDiffTool;
    $result = $tool->execute(['cwd' => $this->tempDir]);

    expect($result->isSuccess())->toBeTrue();
});

it('git_commit creates a commit', function () {
    file_put_contents($this->tempDir.'/test.txt', 'content');
    shell_exec("cd {$this->tempDir} && git add -A 2>&1");

    $tool = new GitCommitTool;
    $result = $tool->execute([
        'message' => 'Test commit',
        'cwd' => $this->tempDir,
    ]);

    expect($result->isSuccess())->toBeTrue();
    expect($result->output)->toContain('Test commit');
});
