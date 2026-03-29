<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'user_id', 'full_name', 'dni', 'email',
        'phone', 'whatsapp', 'birth_date', 'notes', 'status',
        'trainer_id', 'current_plan_id', 'membership_expires_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'membership_expires_at' => 'date',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function trainer() { return $this->belongsTo(Trainer::class); }
    public function currentPlan() { return $this->belongsTo(MembershipPlan::class, 'current_plan_id'); }
    public function memberships() { return $this->hasMany(MemberMembership::class); }
    public function payments() { return $this->hasMany(GymPayment::class); }
    public function routineAssignments() { return $this->hasMany(RoutineAssignment::class); }

    public function isExpired(): bool
    {
        return $this->membership_expires_at !== null && $this->membership_expires_at->isPast();
    }

    public function isDueSoon(int $days = 3): bool
    {
        return $this->membership_expires_at !== null
            && $this->membership_expires_at->isFuture()
            && $this->membership_expires_at->diffInDays(now()) <= $days;
    }

    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeExpired($query) { return $query->where('status', 'expired'); }
    public function scopeSuspended($query) { return $query->where('status', 'suspended'); }
}
