<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('result_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('result_snapshot_id')->constrained('result_snapshots')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('nominee_type');
            $table->unsignedBigInteger('nominee_id');
            // Raw summed points on each side, plus the derived shares and the weighted final score
            // (0..1, display-rounded — the ordering was decided on an exact integer key in Scoring).
            $table->unsignedInteger('academy_points');
            $table->unsignedInteger('public_points');
            $table->double('academy_share');
            $table->double('public_share');
            $table->double('final_score');
            $table->unsignedTinyInteger('position');
            $table->timestamps();

            // A nominee appears at most once per category, and one nominee per position.
            $table->unique(['result_snapshot_id', 'category_id', 'nominee_type', 'nominee_id'], 'result_entries_unique_nominee');
            $table->unique(['result_snapshot_id', 'category_id', 'position'], 'result_entries_unique_position');
            $table->index(['nominee_type', 'nominee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_entries');
    }
};
