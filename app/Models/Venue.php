<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Venue extends Model {
    protected $fillable = ['name', 'address', 'city', 'state', 'country', 'latitude', 'longitude', 'capacity'];
    protected $casts = ['latitude' => 'float', 'longitude' => 'float', 'capacity' => 'integer'];
    public function events() { return $this->hasMany(Event::class); }
}
