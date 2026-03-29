<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Business partners for revenue split configuration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('display_name');
            $table->decimal('gym_percentage', 5, 2)->default(50.00); // % of gym net income
            $table->decimal('field_percentage', 5, 2)->default(50.00); // % of field net income
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_partners');
    }
};
