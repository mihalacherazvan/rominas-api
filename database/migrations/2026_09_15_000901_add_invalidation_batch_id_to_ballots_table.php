<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ballots', function (Blueprint $table): void {
            // The sole validity signal: a ballot is valid iff this is NULL. Set when the ballot is
            // cancelled as part of an InvalidationBatch (which carries the reason + acting admin).
            // constrained() adds the index the valid()/Scoring exclusion filter relies on.
            $table->foreignId('invalidation_batch_id')
                ->nullable()
                ->after('ip_hash')
                ->constrained('invalidation_batches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ballots', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('invalidation_batch_id');
        });
    }
};
