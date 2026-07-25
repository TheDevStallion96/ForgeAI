<?php

return [
    'client_id' => env('GITHUB_CLIENT_ID'),
    'client_secret' => env('GITHUB_CLIENT_SECRET'),
    'redirect_uri' => env('GITHUB_REDIRECT_URI', '/source-control/github/oauth/callback'),
    'api_version' => env('GITHUB_API_VERSION', '2022-11-28'),
    'default_scopes' => ['repo', 'read:user'],
];
