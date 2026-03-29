<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'user_id', 'trainer_id', 'full_name', 'dni',
        'phone', 'whatsapp', 'email', 'birth_date', 'emergency_contact',
        'emergency_phone', 'status', 'notes', 'photo_path', 'qr_code',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /** Valid status transitions */
    public const VALID_TRANSITIONS = [
        'prospect'  => ['active', 'inactive'],
        'active'    => ['expired', 'suspended', 'inactive'],
        'expired'   => ['active', 'inactive'],
        'suspended' => ['active', 'inactive', 'expired'],
        'inactive'  => ['active', 'prospect'],
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function trainer() { return $this->belongsTo(User::class, 'trainer_id'); }
    public function memberships() { return $this->hasMany(MemberMembership::class); }
    public function activeMembership() { return $this->hasOne(MemberMembership::class)->where('status', 'active')->latestOfMany('start_date'); }
    public function gymPayments() { return $this->hasMany(GymPayment::class); }
    public function workoutRoutines() { return $this->hasMany(WorkoutRoutine::class); }
    public function activeRoutine() { return $this->hasOne(WorkoutRoutine::class)->where('active', true)->latestOfMany('start_date'); }
    public function trainerNotes() { return $this->hasMany(TrainerNote::class); }
    public function accessTokens() { return $this->hasMany(AccessToken::class); }
    public function accessEvents() { return $this->hasMany(AccessEvent::class); }
    public function notificationLogs() { return $this->hasMany(NotificationLog::class); }
    public function fieldReservations() { return $this->hasMany(FieldReservation::class); }

    public function isActive(): bool { return $this->status === 'active'; }
    public function isExpired(): bool { return $this->status === 'expired'; }
    public function isSuspended(): bool { return $this->status === 'suspended'; }
}
