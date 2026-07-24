<?php

return [
    'pricing' => [
        'openai' => [
            'gpt-4o' => ['input' => 2.50, 'output' => 10.00],
            'gpt-4o-mini' => ['input' => 0.15, 'output' => 0.60],
            'gpt-4-turbo' => ['input' => 10.00, 'output' => 30.00],
            'text-embedding-3-small' => ['input' => 0.02, 'output' => 0.02],
            'text-embedding-3-large' => ['input' => 0.13, 'output' => 0.13],
        ],
        'anthropic' => [
            'claude-3-5-sonnet' => ['input' => 3.00, 'output' => 15.00],
            'claude-3-5-haiku' => ['input' => 0.80, 'output' => 4.00],
            'claude-3-opus' => ['input' => 15.00, 'output' => 75.00],
        ],
        'google' => [
            'gemini-1.5-pro' => ['input' => 1.25, 'output' => 5.00],
            'gemini-1.5-flash' => ['input' => 0.075, 'output' => 0.30],
        ],
    ],

    'budget' => [
        'default_monthly_limit' => 1_000_000,
        'thresholds' => [0.80, 0.90, 0.95],
        'currency' => 'USD',
    ],

    'injection_shield' => [
        'max_risk_score' => 0.85,
        'suspicious_patterns' => [
            '/ignore all previous instructions/i',
            '/you are now (?:an? )?(?:free|unbound|unleashed)/i',
            '/system.?prompt.?override/i',
            '/forget (?:all )?(?:previous )?(?:instructions|context)/i',
            '/new.?era.?protocol/i',
            '/DAN|jailbreak/im',
            '/respond in (?:a )?way that violates/i',
            '/pretend (?:you are|to be)/i',
            '/output (?:your )?(?:system )?prompt/i',
            '/role.?play/i',
            '/break.?character/i',
            '/override your (?:core|default|base)/i',
            '/act as (?:a )?(?:free|hacker|unfiltered|unrestricted|root|admin|god)/i',
            '/you (?:have |are )?(?:no |been )?(?:rules|limits|restrictions|constraints)/i',
            '/reveal your (?:system )?(?:prompt|instructions)/i',
        ],
    ],

    'pii_redaction' => [
        'patterns' => [
            'email' => '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
            'phone' => '/\+?1?\d{10,15}/',
            'ssn' => '/\b\d{3}-\d{2}-\d{4}\b/',
            'api_key' => '/sk-[a-zA-Z0-9_-]{20,}/',
            'ip_address' => '/\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/',
        ],
        'placeholder_template' => '[REDACTED_%s]',
    ],

    'secrets' => [
        'encryption_store' => env('SECRETS_CACHE_STORE', 'file'),
    ],

    'rate_limiting' => [
        'default_max_requests' => 60,
        'default_decay_minutes' => 1,
    ],
];
