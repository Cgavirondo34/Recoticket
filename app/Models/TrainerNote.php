<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'member_id', 'trainer_id', 'workout_routine_id',
        'type', 'content', 'noted_at',
    ];

    protected $casts = [
        'noted_at' => 'date',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function trainer() { return $this->belongsTo(User::class, 'trainer_id'); }
    public function routine() { return $this->belongsTo(WorkoutRoutine::class, 'workout_routine_id'); }
}
