<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Monthly financial settlements: engine output per period.
 * Immutable once closed. Adjustments tracked separately.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->integer('year');
            $table->integer('month'); // 1-12
            $table->decimal('gym_income', 12, 2)->default(0);
            $table->decimal('field_income', 12, 2)->default(0);
            $table->decimal('total_income', 12, 2)->default(0);
            $table->decimal('gym_expenses', 12, 2)->default(0);
            $table->decimal('field_expenses', 12, 2)->default(0);
            $table->decimal('shared_expenses', 12, 2)->default(0);
            $table->decimal('total_expenses', 12, 2)->default(0);
            $table->decimal('gym_net', 12, 2)->default(0);
            $table->decimal('field_net', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);
            $table->json('partner_earnings')->nullable(); // {partner_id: {gym_share, field_share, total}}
            $table->json('adjustments')->nullable(); // manual adjustments with reason
            $table->enum('status', ['draft', 'closed', 'disputed'])->default('draft');
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'year', 'month']);
            $table->index(['tenant_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_settlements');
    }
};
