<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Rominas\Users\Model\User;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->grantSuperAdminAllPermissions();
    }

    /**
     * A user holding the configured super-admin role passes every gate check.
     */
    private function grantSuperAdminAllPermissions(): void
    {
        Gate::before(static fn(User $user) => $user->hasRole(config('permission.super_admin_role')) ? true : null);
    }
}
