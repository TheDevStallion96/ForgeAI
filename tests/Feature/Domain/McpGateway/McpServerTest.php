<?php

use App\Domain\Automation\Services\ToolRegistry;
use App\Domain\McpGateway\Services\McpServer;

beforeEach(function () {
    $this->toolRegistry = app(ToolRegistry::class);
});

it('handles initialize request', function () {
    $server = new McpServer($this->toolRegistry);

    $response = $server->handleJsonRpcRequest([
        'jsonrpc' => '2.0',
        'id' => 1,
        'method' => 'initialize',
        'params' => [
            'protocolVersion' => '2025-03-26',
            'capabilities' => new stdClass,
            'clientInfo' => ['name' => 'test', 'version' => '1.0'],
        ],
    ]);

    expect($response['jsonrpc'])->toBe('2.0');
    expect($response['id'])->toBe(1);
    expect($response['result']['serverInfo']['name'])->toBe('forgeai-mcp');
});

it('responds to tools/list', function () {
    $server = new McpServer($this->toolRegistry);

    $response = $server->handleJsonRpcRequest([
        'jsonrpc' => '2.0',
        'id' => 2,
        'method' => 'tools/list',
    ]);

    expect($response['jsonrpc'])->toBe('2.0');
    expect($response['result'])->toBeArray();
});

it('returns error for unknown method', function () {
    $server = new McpServer($this->toolRegistry);

    $response = $server->handleJsonRpcRequest([
        'jsonrpc' => '2.0',
        'id' => 3,
        'method' => 'unknown/method',
    ]);

    expect($response['error']['code'])->toBe(-32603);
    expect($response['error']['message'])->toContain('Method not found');
});

it('handles notifications without response', function () {
    $server = new McpServer($this->toolRegistry);

    $response = $server->handleJsonRpcRequest([
        'jsonrpc' => '2.0',
        'method' => 'notifications/initialized',
    ]);

    expect($response)->toBe([]);
});

it('returns error for tools/call on non-existing tool', function () {
    $server = new McpServer($this->toolRegistry);

    $response = $server->handleJsonRpcRequest([
        'jsonrpc' => '2.0',
        'id' => 4,
        'method' => 'tools/call',
        'params' => [
            'name' => 'non_existent_tool',
            'arguments' => [],
        ],
    ]);

    expect($response['result']['isError'])->toBeTrue();
    expect($response['result']['content'][0]['text'])->toContain('Unknown tool');
});

it('lists resources', function () {
    $server = new McpServer($this->toolRegistry);

    $response = $server->handleJsonRpcRequest([
        'jsonrpc' => '2.0',
        'id' => 5,
        'method' => 'resources/list',
    ]);

    expect($response['result'])->toBeArray();
    expect($response['result'][0]['uri'])->toBe('forgeai://graph/nodes');
});
