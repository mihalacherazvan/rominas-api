<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A signature is no longer globally unique per (edition, type): once an alert is `solved`, a fresh wave on
 * the same signature (e.g. a re-offending IP after its ballots were invalidated) raises a NEW pending
 * alert, so `solved` rows accumulate as per-episode history. Dedupe among active (non-solved) alerts is
 * handled in DetectVotingFraudAction. The columns keep a plain index for the upsert lookup.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('fraud_alerts', function (Blueprint $table): void {
            $table->dropUnique(['edition_id', 'type', 'signature']);
            $table->index(['edition_id', 'type', 'signature']);
        });
    }

    public function down(): void
    {
        Schema::table('fraud_alerts', function (Blueprint $table): void {
            $table->dropIndex(['edition_id', 'type', 'signature']);
            $table->unique(['edition_id', 'type', 'signature']);
        });
    }
};
