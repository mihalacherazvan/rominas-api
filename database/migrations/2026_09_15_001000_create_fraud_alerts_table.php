<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fraud_alerts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('edition_id')->constrained('editions')->cascadeOnDelete();
            $table->string('type', 32);
            // Stable dedupe key identifying the specific cluster (the ip_hash, the window start, or the
            // ranking hash). Unique per (edition, type) so re-detection updates rather than duplicates.
            $table->string('signature');
            $table->string('severity', 16);
            $table->string('status', 16)->default('pending');
            $table->json('context')->nullable();
            $table->unsignedInteger('ballot_count')->default(0);
            $table->timestamp('first_detected_at');
            $table->timestamp('last_detected_at');
            $table->timestamps();

            $table->unique(['edition_id', 'type', 'signature']);
            $table->index(['edition_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_alerts');
    }
};
