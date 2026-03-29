<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationPayment extends Model
{
    protected $fillable = [
        'reservation_id', 'payment_method_id', 'amount', 'paid_at', 'reference', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function paymentMethod() { return $this->belongsTo(PaymentMethod::class); }
}
