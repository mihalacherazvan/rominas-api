<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rominas\Editions\Model\Edition;
use Rominas\FraudMonitoring\Enums\FraudAlertSeverity;
use Rominas\FraudMonitoring\Enums\FraudAlertStatus;
use Rominas\FraudMonitoring\Enums\FraudAlertType;
use Rominas\FraudMonitoring\Model\FraudAlert;

/**
 * @extends Factory<FraudAlert>
 */
class FraudAlertFactory extends Factory
{
    protected $model = FraudAlert::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'edition_id' => Edition::factory(),
            'type' => FraudAlertType::SharedIp,
            'signature' => hash('sha256', fake()->unique()->ipv4()),
            'severity' => FraudAlertSeverity::Low,
            'status' => FraudAlertStatus::Pending,
            'context' => ['count' => 5],
            'ballot_count' => 5,
            'first_detected_at' => now(),
            'last_detected_at' => now(),
        ];
    }
}
