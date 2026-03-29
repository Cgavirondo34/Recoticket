<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldTimeSlot extends Model
{
    protected $fillable = [
        'tenant_id', 'label', 'starts_at', 'ends_at', 'price', 'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function reservations() { return $this->hasMany(Reservation::class); }
}
