<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Gym payments table: tracks all payment transactions for memberships.
 * Immutable once confirmed — adjustments go in separate adjustment entries.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('member_membership_id')->nullable()->constrained('member_memberships')->nullOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'failed', 'refunded'])->default('pending');
            $table->enum('type', ['membership', 'extra', 'adjustment'])->default('membership');
            $table->string('reference')->nullable(); // external reference (MP payment id, transfer #, etc.)
            $table->string('mp_payment_id')->nullable(); // Mercado Pago specific ID
            $table->string('mp_status')->nullable(); // Mercado Pago status
            $table->date('paid_at')->nullable();
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // raw webhook payload or extra info
            $table->timestamps();

            $table->index(['tenant_id', 'member_id']);
            $table->index(['tenant_id', 'status']);
            $table->index(['paid_at']);
            $table->index('mp_payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_payments');
    }
};
