<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Notification send logs: history of all sent notifications with retry support.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('notification_template_id')->nullable()->constrained('notification_templates')->nullOnDelete();
            $table->string('event_key'); // copied from template for historical accuracy
            $table->string('channel')->default('whatsapp');
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_name')->nullable();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->text('rendered_body'); // final message after variable substitution
            $table->enum('status', ['queued', 'sent', 'failed', 'retrying'])->default('queued');
            $table->string('provider')->nullable(); // whatsapp_provider_name
            $table->string('provider_message_id')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'member_id']);
            $table->index(['tenant_id', 'status', 'event_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
