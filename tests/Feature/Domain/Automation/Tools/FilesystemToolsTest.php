<?php

use App\Domain\Automation\Tools\DeleteFileTool;
use App\Domain\Automation\Tools\ListDirectoryTool;
use App\Domain\Automation\Tools\ReadFileTool;
use App\Domain\Automation\Tools\WriteFileTool;

beforeEach(function () {
    $this->tempDir = base_path().'/storage/tmp/forgeai-test-'.uniqid();
    mkdir($this->tempDir, 0755, true);
});

afterEach(function () {
    array_map('unlink', glob("{$this->tempDir}/*"));
    @rmdir($this->tempDir);
});

it('read_file reads existing file', function () {
    $filePath = $this->tempDir.'/test.txt';
    file_put_contents($filePath, 'Hello World');

    $tool = new ReadFileTool;
    $result = $tool->execute(['path' => $filePath]);

    expect($result->isSuccess())->toBeTrue();
    expect($result->output)->toBe('Hello World');
});

it('read_file fails for nonexistent file', function () {
    $tool = new ReadFileTool;
    $result = $tool->execute(['path' => '/nonexistent/path.txt']);

    expect($result->isSuccess())->toBeFalse();
});

it('write_file writes content to file', function () {
    $filePath = $this->tempDir.'/new_file.txt';

    $tool = new WriteFileTool;
    $result = $tool->execute([
        'path' => $filePath,
        'content' => 'Test content',
    ]);

    expect($result->isSuccess())->toBeTrue();
    expect(file_get_contents($filePath))->toBe('Test content');
});

it('list_directory lists directory contents', function () {
    file_put_contents($this->tempDir.'/file1.txt', 'content1');
    file_put_contents($this->tempDir.'/file2.txt', 'content2');

    $tool = new ListDirectoryTool;
    $result = $tool->execute(['path' => $this->tempDir]);

    expect($result->isSuccess())->toBeTrue();
    expect($result->output)->toHaveCount(2);
});

it('delete_file deletes existing file', function () {
    $filePath = $this->tempDir.'/delete_me.txt';
    file_put_contents($filePath, 'delete me');

    $tool = new DeleteFileTool;
    $result = $tool->execute(['path' => $filePath]);

    expect($result->isSuccess())->toBeTrue();
    expect(file_exists($filePath))->toBeFalse();
});

it('delete_file fails for nonexistent file', function () {
    $tool = new DeleteFileTool;
    $result = $tool->execute(['path' => '/nonexistent/ghost.txt']);

    expect($result->isSuccess())->toBeFalse();
});

it('list_directory fails for invalid path', function () {
    $tool = new ListDirectoryTool;
    $result = $tool->execute(['path' => '']);

    expect($result->isSuccess())->toBeFalse();
});
