<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('taxonomy_terms', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('taxonomy_terms')
                ->nullOnDelete();

            $table->foreignId('taxonomy_id')
                ->constrained('taxonomies')
                ->cascadeOnDelete();

            // Term name/slug must be unique within a taxonomy, not globally.
            $table->unique(['taxonomy_id', 'name']);
            $table->unique(['taxonomy_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxonomy_terms');
    }
};
