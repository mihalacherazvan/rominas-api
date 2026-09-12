<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Rominas\Permissions\Model\Permission;
use Rominas\Roles\Model\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Resource-level permissions checked by the simple policies. (User/role/permission
     * management stays super_admin-only for now; custodian/fraud_monitor sets arrive
     * with their Phase 2 modules.)
     *
     * @var list<string>
     */
    private const PERMISSIONS = [
        'editions',
        'categories',
        'artists',
        'bands',
        'venues',
        'songs',
        'albums',
        'taxonomies',
        'taxonomyTerms',
    ];

    /**
     * Baseline granted to the `admin` role (super_admin bypasses all via Gate::before).
     *
     * @var list<string>
     */
    private const ADMIN_GRANTS = self::PERMISSIONS;

    public function run(): void
    {
        foreach (self::PERMISSIONS as $name) {
            Permission::query()->firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::query()->where('name', 'admin')->where('guard_name', 'web')->first();

        if ($admin !== null) {
            $admin->syncPermissions(self::ADMIN_GRANTS);
        }
    }
}
