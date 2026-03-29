<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'member_id', 'membership_plan_id', 'start_date', 'end_date',
        'price_paid', 'status', 'auto_renew', 'renewed_at', 'created_by', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'renewed_at' => 'date',
        'price_paid' => 'decimal:2',
        'auto_renew' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function plan() { return $this->belongsTo(MembershipPlan::class, 'membership_plan_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function gymPayments() { return $this->hasMany(GymPayment::class); }

    public function isActive(): bool { return $this->status === 'active' && $this->end_date->isFuture(); }
    public function isExpired(): bool { return $this->end_date->isPast(); }
    public function daysUntilExpiry(): int { return max(0, now()->diffInDays($this->end_date, false)); }
}
