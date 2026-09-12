<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('edition_id')->constrained('editions')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('nominee_type');
            $table->integer('position')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            // Name/slug unique within an edition, not globally.
            $table->unique(['edition_id', 'name']);
            $table->unique(['edition_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
