<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fraud_alert_ballot', function (Blueprint $table): void {
            $table->foreignId('fraud_alert_id')->constrained('fraud_alerts')->cascadeOnDelete();
            $table->foreignId('ballot_id')->constrained('ballots')->cascadeOnDelete();

            $table->unique(['fraud_alert_id', 'ballot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_alert_ballot');
    }
};
