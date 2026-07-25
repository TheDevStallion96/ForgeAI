<?php

return [
    'server_name' => env('MCP_SERVER_NAME', 'forgeai-mcp'),
    'server_version' => env('MCP_SERVER_VERSION', '1.0.0'),
    'default_transport' => env('MCP_DEFAULT_TRANSPORT', 'stdio'),
    'sse_url' => env('MCP_SSE_URL', 'http://localhost:8081'),
];
