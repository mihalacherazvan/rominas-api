<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ballots', function (Blueprint $table): void {
            // Support the FraudMonitoring detectors' per-edition grouping by ip_hash and submitted_at.
            $table->index(['edition_id', 'ip_hash']);
            $table->index(['edition_id', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('ballots', function (Blueprint $table): void {
            $table->dropIndex(['edition_id', 'ip_hash']);
            $table->dropIndex(['edition_id', 'submitted_at']);
        });
    }
};
