<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'user_id', 'display_name',
        'gym_percentage', 'field_percentage', 'active',
    ];

    protected $casts = [
        'gym_percentage' => 'decimal:2',
        'field_percentage' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user() { return $this->belongsTo(User::class); }
}
