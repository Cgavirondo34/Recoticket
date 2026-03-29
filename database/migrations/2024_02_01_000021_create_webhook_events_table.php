<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Webhook events: logs all inbound webhook payloads (e.g. from Mercado Pago).
 * Idempotent processing support via processed_at.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('source'); // mercado_pago, stripe, etc.
            $table->string('event_type')->nullable(); // payment, refund, chargeback
            $table->string('external_id')->nullable(); // provider event ID for deduplication
            $table->json('payload'); // raw request body
            $table->string('signature')->nullable(); // HMAC signature for verification
            $table->boolean('verified')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('processing_error')->nullable();
            $table->timestamps();

            $table->index(['source', 'external_id']); // deduplication
            $table->index(['tenant_id', 'processed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
