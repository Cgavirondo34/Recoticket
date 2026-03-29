<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Football field reservation slots (configurable time windows per day).
 * e.g., Mon-Fri 18:00-19:00, weekends etc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('name'); // "Monday 18:00"
            $table->time('start_time');
            $table->time('end_time');
            $table->json('days_of_week'); // [1,2,3,4,5] = Mon-Fri
            $table->decimal('price', 10, 2);
            $table->boolean('active')->default(true);
            $table->integer('max_bookings')->default(1); // usually 1 (only one team per slot)
            $table->timestamps();

            $table->index(['tenant_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_slots');
    }
};
