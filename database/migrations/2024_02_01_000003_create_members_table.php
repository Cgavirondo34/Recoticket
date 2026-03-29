<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Gym members table.
 * Statuses: active, expired, suspended, prospect, inactive
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // future member portal
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name');
            $table->string('dni')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->enum('status', ['active', 'expired', 'suspended', 'prospect', 'inactive'])->default('prospect');
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('qr_code')->nullable()->unique(); // future access control
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'dni']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
