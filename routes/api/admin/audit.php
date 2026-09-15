<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Audit\Controllers\AuditLogsController;
use Rominas\Audit\Model\AuditLog;

// Read-only audit trail. The `audit` permission is granted to no role by default, so only super_admin
// (via Gate::before) can read it — audited actors cannot inspect or scrub their own trail. Append-only:
// no create/update/delete endpoints. These routes are listed in config('audit.ignore') so reading the
// trail does not record fresh entries.
Route::get('/', [AuditLogsController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . AuditLog::class);

Route::get('/{auditLog}', [AuditLogsController::class, 'show'])
    ->name('view')
    ->middleware('can:view,auditLog');
