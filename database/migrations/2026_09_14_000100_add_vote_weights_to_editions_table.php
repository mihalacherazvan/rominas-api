<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('editions', function (Blueprint $table): void {
            // Result weighting for this edition: the academy round vs. public voting. Defaults 60/40
            // (client-confirmed). Read by the Scoring module; they need not sum to 100 — the final score
            // divides by their sum — but 60/40 is the intended split.
            $table->unsignedTinyInteger('academy_vote_weight')->default(60)->after('status');
            $table->unsignedTinyInteger('public_vote_weight')->default(40)->after('academy_vote_weight');
        });
    }

    public function down(): void
    {
        Schema::table('editions', function (Blueprint $table): void {
            $table->dropColumn(['academy_vote_weight', 'public_vote_weight']);
        });
    }
};
