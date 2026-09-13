<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('member_proposals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposed_by_member_id')->constrained('members')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->index();
            $table->text('reason')->nullable();
            $table->string('status')->default('pending')->index();
            // The invited Member created on approval.
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_proposals');
    }
};
