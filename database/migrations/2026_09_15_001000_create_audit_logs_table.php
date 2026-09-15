<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            // The actor, polymorphic and decoupled: `causer_type` is a stable alias ('user' for an admin,
            // 'member' for an academy member), `causer_id` the model key. Both nullable — an unauthenticated
            // attempt (a failed admin login, a magic-link request) has no actor. No FK: the trail must
            // outlive the actor, and `causer_label` snapshots their name so old rows stay legible.
            $table->string('causer_type')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->string('causer_label')->nullable();
            // The audited action: the matched route name (e.g. `api.admin.editions.transition`).
            $table->string('action');
            $table->string('method', 10);
            // The affected resource, taken from the route's most specific model binding: the binding's
            // parameter name (`edition`, `invalidationBatch`) + the model key. Null for non-bound actions.
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedSmallInteger('status_code');
            // Redacted request payload + any Context-supplied extras. No plaintext voter PII, no
            // credentials/tokens (see config/audit.php `redact`).
            $table->json('context')->nullable();
            // Plaintext admin IP: kept for staff forensic accountability (the §6 GDPR exception —
            // documented per-field), not hashed like voter IPs.
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index('action');
            $table->index(['causer_type', 'causer_id']);
            $table->index('created_at');
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
