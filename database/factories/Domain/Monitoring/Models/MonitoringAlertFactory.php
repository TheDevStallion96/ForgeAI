<?php

namespace Database\Factories\Domain\Monitoring\Models;

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Monitoring\Models\MonitoringAlert;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonitoringAlertFactory extends Factory
{
    protected $model = MonitoringAlert::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'severity' => fake()->randomElement(['info', 'warning', 'critical']),
            'message' => fake()->sentence(),
            'acknowledged_at' => null,
        ];
    }
}
