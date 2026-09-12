<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nomination_rankings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('nomination_id')->constrained('nominations')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->unsignedTinyInteger('rank');
            $table->string('nominee_type');
            $table->unsignedBigInteger('nominee_id');
            $table->timestamps();

            // No two picks at the same rank, and no duplicate nominee, within a category.
            $table->unique(['nomination_id', 'category_id', 'rank']);
            $table->unique(['nomination_id', 'category_id', 'nominee_type', 'nominee_id'], 'nomination_rankings_unique_nominee');
            $table->index(['nominee_type', 'nominee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomination_rankings');
    }
};
