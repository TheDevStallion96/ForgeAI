<?php

use App\Domain\Governance\Services\PiiRedactor;

beforeEach(function () {
    $this->redactor = app(PiiRedactor::class);
});

it('redacts email addresses', function () {
    $result = $this->redactor->redact('Contact me at user@example.com');

    expect($result)->not->toContain('user@example.com');
    expect($result)->toContain('__PII_email_0__');
});

it('redacts phone numbers', function () {
    $result = $this->redactor->redact('Call +14155551234 for help');

    expect($result)->not->toContain('+14155551234');
});

it('redacts SSNs', function () {
    $result = $this->redactor->redact('SSN: 123-45-6789');

    expect($result)->not->toContain('123-45-6789');
});

it('redacts API keys', function () {
    $result = $this->redactor->redact('sk-proj-rXIoXh5l2jF3kD9mQ7vA8bN0cP1qR4sT6uW2yE5gH');

    expect($result)->not->toContain('sk-proj');
});

it('restores original content from placeholders', function () {
    $original = 'Email me at alice@example.com';
    $redacted = $this->redactor->redact($original);
    $restored = $this->redactor->restore($redacted);

    expect($restored)->toBe($original);
});

it('masks PII permanently', function () {
    $result = $this->redactor->mask('Email: bob@test.com, Phone: +12025551234');

    expect($result)->toContain('[REDACTED_EMAIL]');
    expect($result)->toContain('[REDACTED_PHONE]');
    expect($result)->not->toContain('bob@test.com');
});

it('handles text without PII', function () {
    $text = 'This is plain text with no sensitive data';
    $result = $this->redactor->redact($text);

    expect($result)->toBe($text);
});

it('restores multiple placeholders correctly', function () {
    $text = 'Email: a@b.com, Phone: +15551234567';
    $redacted = $this->redactor->redact($text);
    $restored = $this->redactor->restore($redacted);

    expect($restored)->toBe($text);
});
