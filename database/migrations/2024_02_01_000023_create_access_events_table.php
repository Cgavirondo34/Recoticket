<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Access events: log of every attempted and successful member access scan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('access_token_id')->nullable()->constrained('access_tokens')->nullOnDelete();
            $table->string('token_used')->nullable(); // raw token string at time of scan
            $table->enum('direction', ['entry', 'exit'])->default('entry');
            $table->enum('result', ['granted', 'denied', 'manual_override'])->default('granted');
            $table->string('denial_reason')->nullable(); // expired_membership, inactive_member, etc.
            $table->string('device_id')->nullable(); // turnstile/gate identifier
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete(); // staff manual override
            $table->timestamp('scanned_at');
            $table->timestamps();

            $table->index(['tenant_id', 'member_id', 'scanned_at']);
            $table->index(['tenant_id', 'result']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_events');
    }
};
