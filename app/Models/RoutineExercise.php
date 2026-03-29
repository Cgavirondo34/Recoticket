<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutineExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_routine_id', 'name', 'muscle_group', 'sets', 'reps',
        'rest_seconds', 'weight', 'day_of_week', 'sort_order', 'notes',
    ];

    public function routine() { return $this->belongsTo(WorkoutRoutine::class, 'workout_routine_id'); }
}
