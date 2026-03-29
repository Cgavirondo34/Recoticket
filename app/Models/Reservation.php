<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'field_time_slot_id', 'customer_name', 'customer_phone',
        'customer_whatsapp', 'reservation_date', 'type', 'recurrence_day',
        'recurring_until', 'status', 'payment_status', 'amount', 'notes',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'recurring_until' => 'date',
        'amount' => 'decimal:2',
    ];

    public function timeSlot() { return $this->belongsTo(FieldTimeSlot::class, 'field_time_slot_id'); }
    public function payments() { return $this->hasMany(ReservationPayment::class); }
    public function tenant() { return $this->belongsTo(Tenant::class); }

    public function scopeConfirmed($query) { return $query->where('status', 'confirmed'); }
    public function scopeForDate($query, $date) { return $query->where('reservation_date', $date); }
}
