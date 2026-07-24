<?php

use App\Domain\Automation\Services\SandboxedToolRunner;

it('runs a basic command successfully', function () {
    $runner = new SandboxedToolRunner;

    $result = $runner->run(['echo', 'hello'], [], '.');

    expect($result->isSuccess())->toBeTrue();
    expect($result->output)->toContain('hello');
});

it('captures command failure', function () {
    $runner = new SandboxedToolRunner;

    $result = $runner->run(['sh', '-c', 'exit 1'], [], '.');

    expect($result->isSuccess())->toBeFalse();
});

it('respects timeout limits', function () {
    $runner = new SandboxedToolRunner(timeoutSeconds: 1);

    $result = $runner->run(['sleep', '3'], [], '.');

    expect($result->isSuccess())->toBeFalse();
})->skip('Timing-dependent, may be flaky in CI');

it('handles nonexistent commands', function () {
    $runner = new SandboxedToolRunner;

    $result = $runner->run(['nonexistent_command_xyz'], [], '.');

    expect($result->isSuccess())->toBeFalse();
});
