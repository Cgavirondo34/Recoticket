<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model {
    protected $fillable = ['order_id', 'ticket_type_id', 'quantity', 'unit_price', 'subtotal'];
    protected $casts = ['quantity' => 'integer', 'unit_price' => 'float', 'subtotal' => 'float'];
    public function order() { return $this->belongsTo(Order::class); }
    public function ticketType() { return $this->belongsTo(TicketType::class); }
}
