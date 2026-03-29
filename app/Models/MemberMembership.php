<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberMembership extends Model
{
    protected $fillable = [
        'tenant_id', 'member_id', 'membership_plan_id',
        'starts_at', 'expires_at', 'auto_renew', 'status',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'expires_at' => 'date',
        'auto_renew' => 'boolean',
    ];

    public function member() { return $this->belongsTo(Member::class); }
    public function plan() { return $this->belongsTo(MembershipPlan::class, 'membership_plan_id'); }
    public function payments() { return $this->hasMany(GymPayment::class); }
}
