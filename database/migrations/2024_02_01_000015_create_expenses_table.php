<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Expenses table: all business expenses with category, business unit, and audit trail.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->restrictOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->enum('business_unit', ['gym', 'football_field', 'shared'])->default('shared');
            $table->date('expense_date');
            $table->string('receipt_path')->nullable(); // file upload for receipts
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_adjustment')->default(false); // manual correction entry
            $table->foreignId('adjusted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('adjustment_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'expense_date']);
            $table->index(['tenant_id', 'business_unit']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
