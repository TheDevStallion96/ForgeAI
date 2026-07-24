<?php

namespace App\Domain\Governance\Services;

use Illuminate\Support\Facades\Config;

class PiiRedactor
{
    protected array $patterns;

    protected array $placeholders;

    public function __construct()
    {
        $config = Config::get('governance.pii_redaction', []);
        $this->patterns = $config['patterns'] ?? [];
        $this->placeholders = [];
    }

    public function redact(string $text): string
    {
        $this->placeholders = [];
        $counter = 0;

        foreach ($this->patterns as $type => $pattern) {
            $text = preg_replace_callback($pattern, function ($matches) use ($type, &$counter) {
                $key = "__PII_{$type}_{$counter}__";
                $this->placeholders[$key] = $matches[0];
                $counter++;

                return $key;
            }, $text);
        }

        return $text;
    }

    public function restore(string $text): string
    {
        foreach ($this->placeholders as $key => $original) {
            $text = str_replace($key, $original, $text);
        }

        return $text;
    }

    public function mask(string $text): string
    {
        $template = Config::get('governance.pii_redaction.placeholder_template', '[REDACTED_%s]');

        foreach ($this->patterns as $type => $pattern) {
            $placeholder = sprintf($template, strtoupper($type));
            $text = preg_replace($pattern, $placeholder, $text);
        }

        return $text;
    }
}
