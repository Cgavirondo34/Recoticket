<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Member memberships: assignment of a plan to a member for a given period.
 * Tracks start, end, renewal, and overdue state.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('membership_plan_id')->constrained('membership_plans')->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('price_paid', 10, 2); // price at time of purchase (immutable)
            $table->enum('status', ['active', 'expired', 'suspended', 'cancelled'])->default('active');
            $table->boolean('auto_renew')->default(false);
            $table->date('renewed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'member_id', 'status']);
            $table->index(['end_date', 'status']); // for overdue/upcoming queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_memberships');
    }
};
