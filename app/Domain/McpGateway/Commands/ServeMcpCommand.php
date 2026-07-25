<?php

namespace App\Domain\McpGateway\Commands;

use App\Domain\McpGateway\Services\McpServer;
use Illuminate\Console\Command;

class ServeMcpCommand extends Command
{
    protected $signature = 'mcp:serve
        {--transport=stdio : Transport mode (stdio or sse)}
        {--port=8081 : Port for SSE mode}
        {--host=127.0.0.1 : Host for SSE mode}';

    protected $description = 'Start the MCP gateway server';

    public function handle(McpServer $server): int
    {
        $transport = $this->option('transport');

        if ($transport === 'sse') {
            return $this->serveSse($server);
        }

        return $this->serveStdio($server);
    }

    private function serveStdio(McpServer $server): int
    {
        $this->info('Starting MCP server in stdio mode...');

        while (true) {
            $line = fgets(STDIN);

            if ($line === false || $line === '') {
                break;
            }

            $request = json_decode(trim($line), true);

            if ($request === null) {
                continue;
            }

            $response = $server->handleJsonRpcRequest($request);

            if (! empty($response)) {
                echo json_encode($response)."\n";
                fflush(STDOUT);
            }
        }

        return 0;
    }

    private function serveSse(McpServer $server): int
    {
        $host = $this->option('host');
        $port = (int) $this->option('port');

        $this->info("Starting MCP server in SSE mode on {$host}:{$port}...");

        $socket = @stream_socket_server("tcp://{$host}:{$port}", $errno, $errstr);

        if (! $socket) {
            $this->error("Failed to bind: {$errstr} ({$errno})");

            return 1;
        }

        $this->info("Listening on http://{$host}:{$port}/mcp/sse");

        while ($conn = @stream_socket_accept($socket, -1)) {
            $request = json_decode(stream_get_contents($conn), true);
            $response = $server->handleJsonRpcRequest($request ?? []);
            fwrite($conn, json_encode($response));
            fclose($conn);
        }

        return 0;
    }
}
