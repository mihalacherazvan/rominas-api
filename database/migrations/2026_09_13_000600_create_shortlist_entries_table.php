<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shortlist_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('edition_id')->constrained('editions')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('nominee_type');
            $table->unsignedBigInteger('nominee_id');
            // Aggregated academy points; nullable so a future manually-added entry need not carry a score.
            $table->unsignedInteger('points')->nullable();
            $table->unsignedTinyInteger('position');
            $table->timestamps();

            // A nominee appears at most once per category, and one nominee per position.
            $table->unique(['edition_id', 'category_id', 'nominee_type', 'nominee_id'], 'shortlist_entries_unique_nominee');
            $table->unique(['edition_id', 'category_id', 'position'], 'shortlist_entries_unique_position');
            $table->index(['nominee_type', 'nominee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shortlist_entries');
    }
};
