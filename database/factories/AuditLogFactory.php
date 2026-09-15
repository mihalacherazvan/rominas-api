<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rominas\Audit\Model\AuditLog;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'causer_type' => 'user',
            'causer_id' => fake()->numberBetween(1, 1000),
            'causer_label' => fake()->name(),
            'action' => 'api.admin.editions.update',
            'method' => 'PATCH',
            'subject_type' => 'edition',
            'subject_id' => fake()->numberBetween(1, 1000),
            'status_code' => 200,
            'context' => ['request' => []],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
