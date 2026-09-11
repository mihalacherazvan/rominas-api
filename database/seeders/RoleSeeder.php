<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Rominas\Roles\Model\Role;

class RoleSeeder extends Seeder
{
    /**
     * The admin-side roles. `super_admin` bypasses every gate (see AuthServiceProvider);
     * the rest carry explicit permissions assigned separately.
     *
     * @var list<string>
     */
    private const ROLES = [
        'super_admin',
        'admin',
        'custodian',
        'fraud_monitor',
    ];

    public function run(): void
    {
        foreach (self::ROLES as $name) {
            Role::query()->firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }
    }
}
