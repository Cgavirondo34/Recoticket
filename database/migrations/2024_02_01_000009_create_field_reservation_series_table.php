<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recurring reservation series: groups multiple reservations into a weekly/recurring booking.
 * Must be created before field_reservations due to FK dependency.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_reservation_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('field_slot_id')->constrained('field_slots')->restrictOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable(); // null = indefinite
            $table->json('days_of_week'); // [1,3,5] = Mon, Wed, Fri
            $table->decimal('price_per_session', 10, 2);
            $table->enum('status', ['active', 'paused', 'cancelled'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_reservation_series');
    }
};
