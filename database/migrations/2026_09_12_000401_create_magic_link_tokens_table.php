<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('magic_link_tokens', function (Blueprint $table): void {
            $table->string('email');
            $table->string('guard');
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            // One pending link per address per participant guard.
            $table->primary(['email', 'guard']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magic_link_tokens');
    }
};
