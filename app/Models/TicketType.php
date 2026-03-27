<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TicketType extends Model {
    protected $fillable = ['event_id', 'name', 'description', 'price', 'quantity', 'quantity_sold', 'sale_start', 'sale_end', 'status'];
    protected $casts = ['price' => 'float', 'quantity' => 'integer', 'quantity_sold' => 'integer', 'sale_start' => 'datetime', 'sale_end' => 'datetime'];
    public function event() { return $this->belongsTo(Event::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function tickets() { return $this->hasMany(Ticket::class); }
    public function getAvailableAttribute(): int { return $this->quantity - $this->quantity_sold; }
}
