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
     * management stays super_admin-only for now; the fraud_monitor set arrives with its
     * Phase 2 module.)
     *
     * @var list<string>
     */
    private const PERMISSIONS = [
        'editions',
        'categories',
        'members',
        'memberProposals',
        'shortlists',
        'results',
        'artists',
        'bands',
        'venues',
        'songs',
        'albums',
        'taxonomies',
        'taxonomyTerms',
    ];

    /**
     * Baseline granted to the `admin` role (super_admin bypasses all via Gate::before). `results` is
     * deliberately excluded — viewing/exporting final results before publication is custodian-only.
     *
     * @var list<string>
     */
    private const ADMIN_GRANTS = [
        'editions',
        'categories',
        'members',
        'memberProposals',
        'shortlists',
        'artists',
        'bands',
        'venues',
        'songs',
        'albums',
        'taxonomies',
        'taxonomyTerms',
    ];

    /**
     * The custodian's extra right: view/export an edition's complete final results before publication.
     *
     * @var list<string>
     */
    private const CUSTODIAN_GRANTS = [
        'results',
    ];

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

        $custodian = Role::query()->where('name', 'custodian')->where('guard_name', 'web')->first();

        if ($custodian !== null) {
            $custodian->syncPermissions(self::CUSTODIAN_GRANTS);
        }
    }
}
