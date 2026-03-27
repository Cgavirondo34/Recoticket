<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Event extends Model {
    protected $fillable = ['organizer_id', 'category_id', 'venue_id', 'title', 'slug', 'description', 'cover_image', 'start_date', 'end_date', 'status', 'featured', 'total_capacity'];
    protected $casts = ['start_date' => 'datetime', 'end_date' => 'datetime', 'featured' => 'boolean', 'total_capacity' => 'integer'];
    public function organizer() { return $this->belongsTo(Organizer::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function venue() { return $this->belongsTo(Venue::class); }
    public function ticketTypes() { return $this->hasMany(TicketType::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function tickets() { return $this->hasMany(Ticket::class); }
}
