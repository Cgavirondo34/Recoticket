<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trainer notes and progress observations per member.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('workout_routine_id')->nullable()->constrained('workout_routines')->nullOnDelete();
            $table->enum('type', ['observation', 'progress', 'follow_up', 'incident'])->default('observation');
            $table->text('content');
            $table->date('noted_at');
            $table->timestamps();

            $table->index(['tenant_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_notes');
    }
};
