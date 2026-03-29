<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Routine extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'goal', 'notes', 'created_by', 'active',
    ];

    protected $casts = ['active' => 'boolean'];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function exercises() { return $this->belongsToMany(Exercise::class, 'routine_exercises')
        ->withPivot(['sets', 'reps', 'rest_seconds', 'notes', 'order'])
        ->orderByPivot('order'); }
    public function assignments() { return $this->hasMany(RoutineAssignment::class); }
    public function activeAssignments() { return $this->hasMany(RoutineAssignment::class)->where('active', true); }
}
