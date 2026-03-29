<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Access tokens: QR codes or tokens for member gym entry.
 * MVP-ready schema, full implementation is Post-MVP.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->enum('type', ['qr', 'rfid', 'pin'])->default('qr');
            $table->boolean('active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'member_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_tokens');
    }
};
