<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineAssignment extends Model
{
    protected $fillable = [
        'member_id', 'routine_id', 'assigned_at', 'ends_at',
        'active', 'trainer_notes',
    ];

    protected $casts = [
        'assigned_at' => 'date',
        'ends_at' => 'date',
        'active' => 'boolean',
    ];

    public function member() { return $this->belongsTo(Member::class); }
    public function routine() { return $this->belongsTo(Routine::class); }
}
