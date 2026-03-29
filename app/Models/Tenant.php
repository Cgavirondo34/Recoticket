<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'address',
        'timezone', 'currency', 'active', 'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'active' => 'boolean',
    ];

    public function members() { return $this->hasMany(Member::class); }
    public function partners() { return $this->hasMany(Partner::class); }
    public function membershipPlans() { return $this->hasMany(MembershipPlan::class); }
    public function reservations() { return $this->hasMany(Reservation::class); }
    public function expenses() { return $this->hasMany(Expense::class); }
    public function settings() { return $this->hasMany(Setting::class); }
}
