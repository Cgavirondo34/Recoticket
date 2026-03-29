<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'active', 'tenant_id'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    // Legacy ticket platform relations
    public function organizer() { return $this->hasOne(Organizer::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function tickets() { return $this->hasMany(Ticket::class); }

    // Gym + field platform relations
    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function assignedMembers() { return $this->hasMany(Member::class, 'trainer_id'); }
    public function businessPartner() { return $this->hasOne(BusinessPartner::class); }

    // Role helpers
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isOrganizer(): bool { return $this->role === 'organizer'; }
    public function isBuyer(): bool { return $this->role === 'buyer'; }
    public function isTrainer(): bool { return $this->role === 'trainer'; }
    public function isReception(): bool { return $this->role === 'reception'; }
    public function isPartner(): bool { return $this->role === 'partner'; }
    public function isMember(): bool { return $this->role === 'member'; }
}
