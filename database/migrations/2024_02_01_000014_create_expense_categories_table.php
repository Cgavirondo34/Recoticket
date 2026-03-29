<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Expense categories: gym, football_field, shared/general.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->enum('business_unit', ['gym', 'football_field', 'shared'])->default('shared');
            $table->string('color')->nullable(); // for UI display
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'business_unit']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
