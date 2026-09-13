<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ballots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('edition_id')->constrained('editions')->cascadeOnDelete();
            // Pseudonymized voter identity — HMAC-SHA256 of the normalized email (config voting.pepper).
            // Plaintext email is never stored (GDPR); this is the key that enforces one ballot per person.
            $table->string('email_hash', 64);
            // SHA-256 of the single-use link token. The plaintext token only ever leaves the app by email.
            $table->string('token_hash', 64)->unique();
            $table->string('status', 32)->default('issued');
            $table->timestamp('expires_at');
            $table->timestamp('submitted_at')->nullable();
            // HMAC-SHA256 of the submitter IP, captured at submit for FraudMonitoring; hashed, nullable.
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();

            // One link, ever, per email per edition.
            $table->unique(['edition_id', 'email_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ballots');
    }
};
