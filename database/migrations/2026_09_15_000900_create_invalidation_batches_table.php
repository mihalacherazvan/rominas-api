<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invalidation_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('edition_id')->constrained('editions')->cascadeOnDelete();
            // The mandatory cancellation reason, shared by every ballot in the batch (audited).
            $table->text('reason');
            // The admin who performed the cancellation. Nullable + nullOnDelete so the audit record
            // survives even if that user is later removed.
            $table->foreignId('invalidated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('edition_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invalidation_batches');
    }
};
