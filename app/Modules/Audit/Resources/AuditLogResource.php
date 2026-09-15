<?php

declare(strict_types=1);

namespace Rominas\Audit\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Rominas\Audit\Model\AuditLog;

/**
 * One audit-trail entry: the acting admin, the action (route name), the affected subject, the response
 * status, and the redacted context.
 */
class AuditLogResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var AuditLog $log */
        $log = $this->resource;

        return [
            'id' => $log->id,
            'causer_type' => $log->causer_type,
            'causer_id' => $log->causer_id,
            'causer_label' => $log->causer_label,
            'action' => $log->action,
            'method' => $log->method,
            'subject_type' => $log->subject_type,
            'subject_id' => $log->subject_id,
            'status_code' => $log->status_code,
            'context' => $log->context,
            'ip_address' => $log->ip_address,
            'created_at' => $log->created_at?->toISOString(),
        ];
    }
}
