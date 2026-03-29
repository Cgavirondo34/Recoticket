<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Membership plans available in the gym.
 * e.g., Monthly, Quarterly, Annual, Personal Training.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_days'); // e.g., 30, 90, 365
            $table->integer('sessions_per_week')->nullable(); // null = unlimited
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
