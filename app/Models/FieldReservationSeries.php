<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldReservationSeries extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'field_slot_id', 'member_id', 'customer_name', 'customer_phone',
        'start_date', 'end_date', 'days_of_week', 'price_per_session', 'status',
        'created_by', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_of_week' => 'array',
        'price_per_session' => 'decimal:2',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function slot() { return $this->belongsTo(FieldSlot::class, 'field_slot_id'); }
    public function member() { return $this->belongsTo(Member::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function reservations() { return $this->hasMany(FieldReservation::class, 'reservation_series_id'); }
}
