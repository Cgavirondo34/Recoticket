<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'phone',
        'gym_percentage', 'field_percentage', 'active',
    ];

    protected $casts = [
        'gym_percentage' => 'decimal:2',
        'field_percentage' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
}
