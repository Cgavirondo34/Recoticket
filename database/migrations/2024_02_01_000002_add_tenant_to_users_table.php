<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add tenant_id and extended role support to users.
 * Roles: admin, trainer, reception, partner, member (future)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->boolean('active')->default(true)->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'phone', 'active']);
        });
    }
};
