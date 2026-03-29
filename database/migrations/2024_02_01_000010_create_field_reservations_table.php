<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Field reservations: individual booking for a specific date + slot.
 * Supports one-time, walk-in, and links to recurring series.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('field_slot_id')->constrained('field_slots')->restrictOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('reservation_series_id')->nullable()->constrained('field_reservation_series')->nullOnDelete();
            $table->string('customer_name')->nullable(); // for non-member walk-ins
            $table->string('customer_phone')->nullable();
            $table->string('customer_whatsapp')->nullable();
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price', 10, 2);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->enum('status', ['confirmed', 'cancelled', 'no_show', 'completed'])->default('confirmed');
            $table->string('mp_payment_id')->nullable();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint to prevent double booking
            $table->unique(['tenant_id', 'field_slot_id', 'reservation_date', 'deleted_at'], 'unique_slot_booking');
            $table->index(['tenant_id', 'reservation_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_reservations');
    }
};
