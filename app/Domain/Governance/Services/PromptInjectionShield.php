<?php

namespace App\Domain\Governance\Services;

use Illuminate\Support\Facades\Config;

class PromptInjectionShield
{
    protected array $patterns;

    public function __construct()
    {
        $this->patterns = Config::get('governance.injection_shield.suspicious_patterns', []);
    }

    public function analyze(string $prompt): array
    {
        $matches = [];

        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $prompt)) {
                $matches[] = $pattern;
            }
        }

        $riskScore = $this->calculateRiskScore($matches, $prompt);
        $isBlocked = $riskScore >= Config::get('governance.injection_shield.max_risk_score', 0.85);

        return [
            'risk_score' => $riskScore,
            'is_blocked' => $isBlocked,
            'matched_patterns' => count($matches),
            'matched_rules' => $matches,
            'prompt_length' => strlen($prompt),
        ];
    }

    public function isSafe(string $prompt): bool
    {
        return ! $this->analyze($prompt)['is_blocked'];
    }

    protected function calculateRiskScore(array $matches, string $prompt): float
    {
        $patternCount = max(count($this->patterns), 1);
        $matchCount = count($matches);

        $matchDensity = $matchCount > 0 ? $matchCount / $patternCount : 0;
        $lengthScore = min(strlen($prompt) / 10000, 0.15);
        $matchFrequency = $matchCount * 0.4;

        return min($matchDensity + $lengthScore + $matchFrequency, 1.0);
    }
}
