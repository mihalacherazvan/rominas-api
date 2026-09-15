<?php

declare(strict_types=1);

namespace Rominas\Audit\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rominas\Audit\Model\AuditLog;
use Rominas\Audit\QueryBuilders\AuditLogQueryBuilder;
use Rominas\Audit\Resources\AuditLogResource;
use Rominas\Users\Model\User;

/**
 * Read-only admin access to the audit trail, gated by the `audit` permission (super_admin only by
 * default). The trail is append-only — there are no write endpoints.
 */
class AuditLogsController
{
    /**
     * The audit log, newest first, optionally filtered by causer (type + id), action, subject or date range.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        $query = AuditLog::query()->visibleToUser($user);

        if ($request->filled('causer_type') && $request->filled('causer_id')) {
            $query->forCauser((string) $request->string('causer_type'), (int) $request->integer('causer_id'));
        }

        if ($request->filled('action')) {
            $query->forAction((string) $request->string('action'));
        }

        if ($request->filled('subject_type') && $request->filled('subject_id')) {
            $query->forSubject((string) $request->string('subject_type'), (int) $request->integer('subject_id'));
        }

        $query->betweenDates(
            $request->filled('from') ? (string) $request->string('from') : null,
            $request->filled('to') ? (string) $request->string('to') : null,
        );

        return AuditLogResource::collection(
            $query->orderByDesc('id')->paginate(AuditLogQueryBuilder::PER_PAGE),
        );
    }

    /**
     * A single audit entry.
     */
    public function show(AuditLog $auditLog): AuditLogResource
    {
        return AuditLogResource::make($auditLog);
    }
}
