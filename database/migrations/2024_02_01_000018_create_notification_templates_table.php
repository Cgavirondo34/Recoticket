<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * WhatsApp notification templates with variable support.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('event_key')->unique(); // payment_due, payment_overdue, reservation_confirmed, etc.
            $table->string('name');
            $table->text('body'); // Template body with {{variable}} placeholders
            $table->json('available_variables')->nullable(); // documentation of available vars
            $table->boolean('active')->default(true);
            $table->enum('channel', ['whatsapp', 'email', 'sms'])->default('whatsapp');
            $table->timestamps();

            $table->index(['tenant_id', 'event_key', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
