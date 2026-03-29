<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkoutRoutine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'member_id', 'trainer_id', 'name', 'goal',
        'start_date', 'end_date', 'version', 'active', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function trainer() { return $this->belongsTo(User::class, 'trainer_id'); }
    public function exercises() { return $this->hasMany(RoutineExercise::class)->orderBy('day_of_week')->orderBy('sort_order'); }
    public function notes() { return $this->hasMany(TrainerNote::class); }
}
