<?php

use App\Domain\Automation\Services\SandboxedToolRunner;
use App\Domain\Automation\Tools\RunCommandTool;

it('executes a command via the sandbox runner', function () {
    $runner = new SandboxedToolRunner;
    $tool = new RunCommandTool($runner);

    $result = $tool->execute(['command' => 'echo hello']);

    expect($result->isSuccess())->toBeTrue();
});

it('returns failure for empty command', function () {
    $runner = new SandboxedToolRunner;
    $tool = new RunCommandTool($runner);

    $result = $tool->execute(['command' => '']);

    expect($result->isSuccess())->toBeFalse();
    expect($result->error)->toContain('required');
});

it('returns failure for invalid command', function () {
    $runner = new SandboxedToolRunner;
    $tool = new RunCommandTool($runner);

    $result = $tool->execute(['command' => 'nonexistent_cmd_xyz']);

    expect($result->isSuccess())->toBeFalse();
});
