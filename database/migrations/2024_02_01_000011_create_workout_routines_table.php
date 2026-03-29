<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Workout routines: training plans assigned to gym members by trainers.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('users')->restrictOnDelete();
            $table->string('name');
            $table->string('goal')->nullable(); // weight loss, muscle gain, rehab, etc.
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('version')->default(1); // version history
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'member_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_routines');
    }
};
