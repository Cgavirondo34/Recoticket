<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model {
    protected $fillable = ['order_id', 'mp_payment_id', 'mp_preference_id', 'status', 'payment_type', 'amount', 'currency', 'raw_response'];
    protected $casts = ['raw_response' => 'array', 'amount' => 'float'];
    public function order() { return $this->belongsTo(Order::class); }
}
