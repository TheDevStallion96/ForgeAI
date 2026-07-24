<?php

use App\Domain\AuthTenant\Models\User;
use App\Domain\Automation\Services\ToolPermissionChecker;

beforeEach(function () {
    $this->checker = new ToolPermissionChecker;
    $this->user = User::factory()->create();
});

it('allows registered tools', function () {
    expect($this->checker->canExecute($this->user, 'read_file'))->toBeTrue();
    expect($this->checker->canExecute($this->user, 'write_file'))->toBeTrue();
    expect($this->checker->canExecute($this->user, 'list_directory'))->toBeTrue();
});

it('denies unregistered tools', function () {
    expect($this->checker->canExecute($this->user, 'hack_database'))->toBeFalse();
    expect($this->checker->canExecute($this->user, 'delete_system'))->toBeFalse();
});

it('registers new allowed tools', function () {
    $this->checker->allowTool('custom_tool');

    expect($this->checker->canExecute($this->user, 'custom_tool'))->toBeTrue();
});

it('identifies HITL-required tools', function () {
    expect($this->checker->requiresHitl('delete_file'))->toBeTrue();
    expect($this->checker->requiresHitl('run_command'))->toBeTrue();
    expect($this->checker->requiresHitl('read_file'))->toBeFalse();
});

it('registers new HITL tools', function () {
    $this->checker->requireHitl('read_file');

    expect($this->checker->requiresHitl('read_file'))->toBeTrue();
});

it('does not duplicate tool registrations', function () {
    $this->checker->allowTool('read_file');
    $this->checker->allowTool('read_file');

    expect($this->checker->canExecute($this->user, 'read_file'))->toBeTrue();
});
