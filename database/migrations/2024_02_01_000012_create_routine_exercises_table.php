<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Routine exercises: individual exercises within a workout routine.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_routine_id')->constrained('workout_routines')->cascadeOnDelete();
            $table->string('name');
            $table->string('muscle_group')->nullable();
            $table->integer('sets')->nullable();
            $table->string('reps')->nullable(); // "10-12" or "to failure"
            $table->string('rest_seconds')->nullable();
            $table->string('weight')->nullable(); // "20kg" or "bodyweight"
            $table->integer('day_of_week')->nullable(); // 1-7, null = any day
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('workout_routine_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_exercises');
    }
};
