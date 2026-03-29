<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'muscle_group', 'description', 'video_url',
    ];

    public function routines() { return $this->belongsToMany(Routine::class, 'routine_exercises'); }
}
