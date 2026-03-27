<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
    protected $fillable = ['user_id', 'event_id', 'order_number', 'subtotal', 'fee', 'total', 'status', 'payment_method'];
    protected $casts = ['subtotal' => 'float', 'fee' => 'float', 'total' => 'float'];
    public function user() { return $this->belongsTo(User::class); }
    public function event() { return $this->belongsTo(Event::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function tickets() { return $this->hasMany(Ticket::class); }
    public function payment() { return $this->hasOne(Payment::class); }
}
