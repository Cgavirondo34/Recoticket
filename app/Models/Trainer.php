<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'user_id', 'full_name', 'email',
        'phone', 'specialty', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function members() { return $this->hasMany(Member::class, 'trainer_id'); }
}
