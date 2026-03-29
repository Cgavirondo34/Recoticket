<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'description', 'price',
        'duration_days', 'auto_renew_default', 'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'auto_renew_default' => 'boolean',
        'active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function members() { return $this->hasMany(Member::class, 'current_plan_id'); }
    public function memberMemberships() { return $this->hasMany(MemberMembership::class); }
}
