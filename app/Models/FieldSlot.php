<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'name', 'start_time', 'end_time',
        'days_of_week', 'price', 'active', 'max_bookings',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function reservations() { return $this->hasMany(FieldReservation::class); }
    public function series() { return $this->hasMany(FieldReservationSeries::class); }

    /** Check if this slot is available on a given date */
    public function isAvailableOn(string $date): bool
    {
        $dayOfWeek = (int) date('N', strtotime($date)); // 1=Mon, 7=Sun
        return in_array($dayOfWeek, $this->days_of_week ?? []);
    }
}
