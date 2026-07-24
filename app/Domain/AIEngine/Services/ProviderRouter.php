<?php

namespace App\Domain\AIEngine\Services;

use App\Domain\AIEngine\Enums\AIProvider;
use Laravel\Ai\Enums\Lab;

class ProviderRouter
{
    public function resolve(array $providers): array
    {
        $resolved = [];

        foreach ($providers as $key => $value) {
            if ($value instanceof AIProvider) {
                if (is_string($key)) {
                    $resolved[$value->value] = $key;
                } else {
                    $resolved[] = $value->toLab();
                }
            } elseif ($value instanceof Lab) {
                $resolved[] = $value;
            } else {
                $resolved[] = $value;
            }
        }

        return $resolved;
    }
}
