<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TicketScan extends Model {
    protected $fillable = ['ticket_id', 'scanned_by', 'scanned_at', 'result', 'notes'];
    protected $casts = ['scanned_at' => 'datetime'];
    public function ticket() { return $this->belongsTo(Ticket::class); }
    public function scanner() { return $this->belongsTo(User::class, 'scanned_by'); }
}
