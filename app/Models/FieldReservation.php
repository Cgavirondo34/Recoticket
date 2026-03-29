<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldReservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'field_slot_id', 'member_id', 'reservation_series_id',
        'customer_name', 'customer_phone', 'customer_whatsapp',
        'reservation_date', 'start_time', 'end_time', 'price',
        'payment_status', 'status', 'mp_payment_id', 'payment_method_id',
        'created_by', 'notes',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function slot() { return $this->belongsTo(FieldSlot::class, 'field_slot_id'); }
    public function member() { return $this->belongsTo(Member::class); }
    public function series() { return $this->belongsTo(FieldReservationSeries::class, 'reservation_series_id'); }
    public function paymentMethod() { return $this->belongsTo(PaymentMethod::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }

    public function isPaid(): bool { return $this->payment_status === 'paid'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
}
