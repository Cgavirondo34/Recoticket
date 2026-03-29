<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tenants table — SaaS multi-tenant readiness.
 * MVP: single tenant. Post-MVP: enable multi-tenant isolation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('timezone')->default('America/Argentina/Buenos_Aires');
            $table->string('currency')->default('ARS');
            $table->string('logo_path')->nullable();
            $table->boolean('active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
